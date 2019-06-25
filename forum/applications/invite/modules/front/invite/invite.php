<?php


namespace IPS\invite\modules\front\invite;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * invite
 */
class _invite extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		if ( !\IPS\Member::loggedIn()->group['is_canvite'] OR \IPS\Member::loggedIn()->invite_revoke_access )
		{
			\IPS\Output::i()->error( 'invite_no_permission', '2C138/3', 403, '' );
		}

		if( \IPS\Settings::i()->is_on AND \IPS\Member::loggedIn()->group['is_canvite'] AND ( \IPS\Member::loggedIn()->invites_remaining <= 0 AND !\IPS\Member::loggedIn()->group['is_unlimited'] ) )
		{
			\IPS\Output::i()->error( 'invites_no_remaining_invitesx', '2IS-SEND/1', 404, '' );
		}

		$form = new \IPS\Helpers\Form( 'form', 'send_invitation' );
		$form->class = 'ipsForm_vertical';
		$form->add( new \IPS\Helpers\Form\Text( 'invite_invited_name', NULL, TRUE, array() ) );
		$form->add( new \IPS\Helpers\Form\Email( 'invite_invited_email', NULL, TRUE, array(), function( $val )
		{
			/* Check email exists */
			foreach ( \IPS\Login::handlers( TRUE ) as $k => $handler )
			{
				if ( $handler->emailIsInUse( $val ) === TRUE )
				{
					throw new \LogicException( 'email_already_in_use' );
					break;
				}
			}

			/* Check if the email was already invited */
			try
			{
				$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_invited_email=?', $val ) )->first();

				if( $invite['invite_invited_email'] )
				{
					throw new \LogicException( 'invite_email_already_invited' );
				}
			}
			catch( \UnderflowException $e ){}
		}) );

		if ( $values = $form->values() )
		{
			if( \IPS\Settings::i()->is_expireinvite > 0 )
			{
				$expire  = new \IPS\DateTime;
				$expire->add( new \DateInterval( 'P' . \IPS\Settings::i()->is_expireinvite . 'D' ) );
				$newDate = $expire->getTimestamp();
			}
			else
			{
				$newDate = 0;
			}

			$invitationCode = md5( uniqid( microtime(), true ) );

			$toInsert 	= array(
				'invite_sender_id'			=> \IPS\Member::loggedIn()->member_id,
				'invite_code'				=> $invitationCode,
				'invite_date'				=> time(),
				'invite_invited_name'		=> $values['invite_invited_name'],
				'invite_invited_email'		=> $values['invite_invited_email'],
				'invite_status'				=> 0,
				'invite_expiration_date'	=> $newDate
			);

			\IPS\Db::i()->insert( 'invite_invites', $toInsert );

			/* Update # of Invites of the sender */
			if ( !\IPS\Member::loggedIn()->group['is_unlimited'] )
			{
				$invites = ( \IPS\Member::loggedIn()->invites_remaining - 1 );
				\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $invites ), array( 'member_id=?', \IPS\Member::loggedIn()->member_id ) );

				if ( $invites === 0 )
				{
					\IPS\Member::clearCreateMenu();
				}
			}

			/* Send the invitation */
			\IPS\invite\Invite::sendInvitation( $values['invite_invited_email'], $values['invite_invited_name'], $invitationCode, \IPS\Member::loggedIn()->email, \IPS\Member::loggedIn()->name, $newDate );

			$url = \IPS\Http\Url::internal( 'app=core&module=system&controller=settings&area=invitesystem', 'front', 'settings_invitesystem' )->setQueryString( 'area', 'invitesystem' );
			\IPS\Output::i()->redirect( $url, 'invite_sent' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'send_new_invitation' );
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'submit' )->categorySelector( $form );	
	}

	/**
	 * Top Inviters
	 *
	 * @retun	void
	 */
	public function topInviters()
	{
		/* How many? */
		$limit = intval( ( isset( \IPS\Request::i()->limit ) and \IPS\Request::i()->limit < 20 ) ? \IPS\Request::i()->limit : 5 );
		
		/* What timeframe? */
		$where = array( array( 'invite_sender_id > 0' ) );
		$timeframe = 'all';
		if ( isset( \IPS\Request::i()->time ) and \IPS\Request::i()->time != 'all' )
		{
			switch ( \IPS\Request::i()->time )
			{
				case 'week':
					$where[] = array( 'invite_conv_date>?', \IPS\DateTime::create()->sub( new \DateInterval( 'P1W' ) )->getTimestamp() );
					$timeframe = 'week';
					break;
				case 'month':
					$where[] = array( 'invite_conv_date>?', \IPS\DateTime::create()->sub( new \DateInterval( 'P1M' ) )->getTimestamp() );
					$timeframe = 'month';
					break;
				case 'year':
					$where[] = array( 'invite_conv_date>?', \IPS\DateTime::create()->sub( new \DateInterval( 'P1Y' ) )->getTimestamp() );
					$timeframe = 'year';
					break;
			}

            $topContributors = iterator_to_array( \IPS\Db::i()->select( 'invite_sender_id as member, count(invite_id) as rep', 'invite_invites', $where, 'rep DESC', $limit, 'member' )->setKeyField('member')->setValueField('rep') );
        }
        else
        {
            $topContributors = iterator_to_array( \IPS\Db::i()->select( 'invite_sender_id as member, count(invite_id) as rep', 'invite_invites', array( 'invite_conv_member_id > 0' ), 'rep DESC', $limit )->setKeyField('member')->setValueField('rep') );
        }

		/* Load their data */	
		foreach ( \IPS\Db::i()->select( '*', 'core_members', \IPS\Db::i()->in( 'member_id', array_keys( $topContributors ) ) ) as $member )
		{
			\IPS\Member::constructFromData( $member );
		}

		/* Render */
		$output = \IPS\Theme::i()->getTemplate( 'widgets' )->topInvitersRows( $topContributors, $timeframe, \IPS\Request::i()->orientation );

		if ( \IPS\Request::i()->isAjax() )
		{
			\IPS\Output::i()->sendOutput( $output );
		}
		else
		{
			\IPS\Output::i()->output = $output;
		}
	}
}