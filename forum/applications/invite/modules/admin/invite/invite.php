<?php


namespace IPS\invite\modules\admin\invite;

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
		\IPS\Dispatcher::i()->checkAcpPermission( 'invite_manage' );
		parent::execute();
	}
	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__invite_invite_invite');

		/* Create the table */
		$table = new \IPS\Helpers\Table\Db( 'invite_invites', \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite' ) );
		$table->limit = 10;

		$table->joins = array(
			array(
				'select'	=> 'core_members.*',
				'from'		=> 'core_members',
				'where'		=> 'core_members.member_id=invite_invites.invite_sender_id'
			)
		);

		$table->tableTemplate  = array( \IPS\Theme::i()->getTemplate( 'view', 'invite', 'admin' ), 'inviteTable' );
		$table->rowsTemplate   = array( \IPS\Theme::i()->getTemplate( 'view', 'invite', 'admin' ), 'inviteRows' );

        /* Columns we need */
        $table->include 	= array( 'invite_id', 'photo', 'invite_sender_id', 'invite_code', 'invite_date', 'invite_invited_name', 'invite_invited_email', 'invite_status', 'member_group_id' );
		$table->mainColumn 	= 'invite_sender_id';
		$table->noSort		= array( 'photo' );

		/* Filters */
		$table->filters = array(
			'invites_filter_pending'	=> 'invite_status = 0',
			'invites_filter_converted'	=> 'invite_status = 1',
			'invites_filter_expired'	=> 'invite_status = 2',
		);

		$table->quickSearch = array( array( 'name', \IPS\Member::load(  $row['invite_sender_id']->name ) ), 'invite_sender_id' );

		$filterOptions = array(
			'0'	=> 'invites_filter_pending',
			'1'	=> 'invites_filter_converted',
			'2'	=> 'invites_filter_expired',
		);

		$table->advancedSearch = array(
			'invite_sender_id'		=> \IPS\Helpers\Table\SEARCH_MEMBER,
			'invite_invited_name'	=> \IPS\Helpers\Table\SEARCH_CONTAINS_TEXT,
			'invite_invited_email'	=> \IPS\Helpers\Table\SEARCH_CONTAINS_TEXT,
			'invite_status'			=> array( \IPS\Helpers\Table\SEARCH_SELECT, array( 'options' => $filterOptions ) ),
		);

		$table->advancedSearchCallback = function( $table, $values )
		{
			switch ( $values['invite_status'] )
			{
				case 0:
					$table->where[] = array( 'invite_status=?', 0 );
					break;
				case 1:
					$table->where[] = array( 'invite_status=?', 1 );
					break;
				case 2:
					$table->where[] = array( 'invite_status=?', 2 );
					break;
			}
		};

        /* Custom parsers */
        $table->parsers = array(
            'invite_sender_id'	=> function( $val, $row )
            {
				return "<a href='" . \IPS\Http\Url::internal( 'app=core&module=members&controller=members&do=edit&id=' ) . $row['invite_sender_id'] . "'>" . $row['name'] . "</a>";
            },
			'member_group_id' => function( $val, $row )
			{
				return \IPS\Member\Group::load( $row['member_group_id'] )->formattedName;
			},
            'photo' => function( $val, $row )
            {
                return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( \IPS\Member::constructFromData( $row ), 'tiny' );
            },
            'invite_invited_name' => function( $val, $row )
            {
                return $row['invite_invited_name'];
            },
            'invite_invited_email' => function( $val, $row )
            {
                return $row['invite_invited_email'];
            },
            'invite_date' => function( $val, $row )
            {
                return \IPS\DateTime::ts( $val )->relative();
            },
            'invite_status' => function( $val, $row )
            {
				switch( $row['invite_status'] )
				{
					case 0:
						$text = 'invite_status_pending';
					break;
					case 1:
						$text = 'invite_status_converted';
					break;
					case 2:
						$text = 'invite_status_expired';
					break;
				}

				return \IPS\Member::loggedIn()->language()->addToStack( $text );
            },
        );

		$table->rootButtons = array(
			'add'	=> array(
				'icon'	=> 'plus',
				'link'	=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=add' ),
				'title'	=> 'send_invitation',
				'data'	=> array( 'ipsDialog' => '', 'ipsDialog-size' => 'medium', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('send_invitation') )
			)
		);

		$lang = \IPS\Settings::i()->is_restoreinvitation ? delete_all_expired_willrestore : delete_all_expired_wontrestore;

		$table->rootButtons['delete'] = array(
			'icon'	=> 'trash',
			'title'	=> 'delete_all_expired',
			'link'	=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=deleteAll' ),
			'data'	=> array( 'delete' => '', 'delete-warning' => \IPS\Member::loggedIn()->language()->addToStack( $lang ) )
		);

		if( \IPS\Member::loggedIn()->group['is_unlimited'] )
		{
			$table->rootButtons['batch'] = array(
				'icon'	=> 'plus',
				'title'	=> 'is_batch_invitations',
				'link'	=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=batch' ),
				'data'	=> array( 'ipsDialog' => '', 'ipsDialog-size' => 'medium', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('is_batch_invitations') )
			);
		}

		/* Row buttons */
		$table->rowButtons = function( $row )
		{
			if ( $row['invite_status'] == 1 )
			{
				$return = array( 'view' => array(
					'icon'		=> 'search',
					'title'		=> 'view_member',
					'link'		=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=member&id=' ) . $row['invite_conv_member_id'],
					'data'		=> array( 'ipsDialog' => '', 'ipsDialog-size' => "narrow", 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('view_member') ),
					'hotkey'	=> 'v'
				) );
			}

			if ( $row['invite_status'] != 1 )
			{
				$return['delete'] = array(
					'icon'		=> 'trash-o',
					'title'		=> 'delete_invite',
					'link'		=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=delete&id=' ) . $row['invite_id'],
					'data'		=> array( 'delete' => '' ),
					'hotkey'	=> 'd'
				);

				$return['resend'] = array(
					'icon'		=> 'envelope',
					'title'		=> 'resend_invite',
					'link'		=> \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite&do=resend&id=' ) . $row['invite_id'],
					'data'		=> array( 'confirm' => '' ),
					'hotkey'	=> 'r'
				);
			}

			return $return;
		};

		$table->sortBy = $table->sortBy ?: 'invite_id';
		$table->sortDirection = $table->sortDirection ?: 'desc';

		/* Display */
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}

	/**
	 * Delete invite
	 *
	 * @return	void
	 */
	protected function delete()
	{
		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		try
		{
			$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_id=?', \IPS\Request::i()->id ) )->first();
		}
		catch( \UnderflowException $ex )
		{
			\IPS\Output::i()->error( 'invite_does_not_exist', '2C135/A', 403, '' );
		}

		\IPS\Db::i()->delete( 'invite_invites', array( 'invite_id=?', $invite['invite_id'] ) );

		if( \IPS\Settings::i()->is_restoreinvitation )
		{
			$inviter = \IPS\Member::load( $invite['invite_sender_id'] );

			if ( !$inviter->group['is_unlimited'] )
			{
				$invites = ( $inviter->invites_remaining + 1 );
				\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $invites ), array( 'member_id=?', $inviter->member_id ) );
			}
		}

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite' ), 'invite_deleted' );
	}

	/**
	 * Delete all expired invitations
	 *
	 * @return	void
	 */
	protected function deleteAll()
	{
		if( \IPS\Settings::i()->is_restoreinvitation )
		{
			foreach( \IPS\Db::i()->select( '*', 'invite_invites',  array( 'invite_status=?', 2 ) ) as $invite )
			{
				$inviter = \IPS\Member::load( $invite['invite_sender_id'] );

				if ( !$inviter->group['is_unlimited'] )
				{
					$invites = ( $inviter->invites_remaining + 1 );
					\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $invites ), array( 'member_id=?', $inviter->member_id ) );
				}

				\IPS\Db::i()->delete( 'invite_invites', array( 'invite_id=?', $invite['invite_id'] ) );
			}
		}
		else
		{
			\IPS\Db::i()->delete( 'invite_invites', array( 'invite_status=?', 2 ) );
		}

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=invite&module=invite&controller=invite' ), 'expired_invites_deleted' );
	}

	/**
	 * Add/Send invite
	 *
	 * @return	void
	 */
	protected function add()
	{
		$form = new \IPS\Helpers\Form( 'form', 'send_invitation' );
		
		$form->add( new \IPS\Helpers\Form\Member( 'invite_sender_id', \IPS\Member::loggedIn()->name, TRUE, array() ) );
		$form->add( new \IPS\Helpers\Form\Text( 'invite_invited_name', NULL, TRUE, array() ) );
		$form->add( new \IPS\Helpers\Form\Email( 'invite_invited_email', NULL, TRUE, array(), function( $val ){
			
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
				'invite_sender_id'			=> $values['invite_sender_id']->member_id,
				'invite_code'				=> $invitationCode,
				'invite_date'				=> time(),
				'invite_invited_name'		=> $values['invite_invited_name'],
				'invite_invited_email'		=> $values['invite_invited_email'],
				'invite_status'				=> 0,
				'invite_expiration_date'	=> $newDate
			);

			\IPS\Db::i()->insert( 'invite_invites', $toInsert );

			/* Update # of Invites of the sender */
			$sender = \IPS\Member::load( $values['invite_sender_id']->member_id );

			if ( !$sender->group['is_unlimited'] )
			{
				$invites = ( $sender->invites_remaining - 1 );
				\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $invites ), array( 'member_id=?', $values['invite_sender_id']->member_id ) );
			}

			/* Send the invitation */
			\IPS\invite\Invite::sendInvitation( $values['invite_invited_email'], $values['invite_invited_name'], $invitationCode, \IPS\Member::loggedIn()->email, \IPS\Member::loggedIn()->name, $newDate );

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=invite&module=invite&controller=invite" ), 'invite_sent' );
		}

		\IPS\Output::i()->output = $form;
	}

	/**
	 * Veiw converted invite
	 *
	 * @return	void
	 */
	protected function member()
	{
		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$member = \IPS\Member::load( \IPS\Request::i()->id );

		/* Display */
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'view', 'invite', 'admin' )->member( $member );
	}

	protected function resend()
	{
		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		try
		{
			$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_id=?', \IPS\Request::i()->id ) )->first();
		}
		catch( \UnderflowException $ex )
		{
			/* The invitation code does not exists */
			\IPS\Output::i()->error( 'invite_does_not_exist', '2C135/A', 403, '' );
		}

		/* Send the invitation */
		\IPS\invite\Invite::sendInvitation( $invite['invite_invited_email'], $invite['invite_invited_name'], $invite['invite_code'], \IPS\Member::loggedIn()->email, \IPS\Member::loggedIn()->name, $invite['invite_expiration_date'] );

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=invite&module=invite&controller=invite" ), 'invite_resent' );
	}

	/**
	 * Add batch invite without send emails
	 *
	 * @return	void
	 */
	protected function batch()
	{
		$form = new \IPS\Helpers\Form( 'form', 'add_invitations' );

		$form->addMessage('is_batch_invitations_desc');

		if ( !\IPS\Member::loggedIn()->group['is_unlimited'] )
		{
			$form->addMessage('is_batch_no_unlimited_perm');
		}

		$form->add( new \IPS\Helpers\Form\Member( 'invite_sender_id', \IPS\Member::loggedIn()->name, TRUE, array() ) );
		$form->add( new \IPS\Helpers\Form\Number( 'invite_batch_nr', 5, FALSE, array( 'min' => 1, 'max' => 100 ) ) );

		if ( $values = $form->values() )
		{
			$inviter = \IPS\Member::load( $values['invite_sender_id']->member_id );

			if ( !$inviter->group['is_unlimited'] )
			{
				\IPS\Output::i()->error( 'is_batch_no_unlimited_perm_er', '' );
			}

			for ( $i = 1; $i <= $values['invite_batch_nr']; $i++ )
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
					'invite_sender_id'			=> $values['invite_sender_id']->member_id,
					'invite_code'				=> $invitationCode,
					'invite_date'				=> time(),
					'invite_invited_name'		=> '',
					'invite_invited_email'		=> '',
					'invite_status'				=> 0,
					'invite_expiration_date'	=> $newDate
				);
	
				\IPS\Db::i()->insert( 'invite_invites', $toInsert );
			}

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=invite&module=invite&controller=invite" ), 'batch_invite_added' );
		}

		\IPS\Output::i()->output = $form;
	}
}