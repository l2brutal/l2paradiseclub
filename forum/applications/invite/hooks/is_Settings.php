//<?php

class invite_hook_is_Settings extends _HOOK_CLASS_
{
	protected function _invitesystem()
	{
		try
		{
			if ( !\IPS\Member::loggedIn()->group['is_canvite'] OR \IPS\Member::loggedIn()->invite_revoke_access )
			{
				\IPS\Output::i()->error( 'invite_no_permission', '2C138/3', 403, '' );
			}
	
			$form = new \IPS\Helpers\Form( 'form', 'send_invitation' );
			
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
	
			/* Invites I've done and are confirmed */
			$iInvited = array();
	
			foreach( \IPS\Db::i()->select( '*', 'invite_invites', array( "invite_sender_id=? AND invite_status=?", \IPS\Member::loggedIn()->member_id, 1 ) )->join( 'core_members', 'invite_invites.invite_conv_member_id=core_members.member_id' ) as $member )
			{
				$iInvited[ $member['invite_conv_member_id'] ] = $member;
			}
	
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
	
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=core&module=system&controller=settings&area=invitesystem", 'front', 'settings_invitesystem' ), 'invite_sent' );
			}
			
			return \IPS\Theme::i()->getTemplate( 'settings', 'invite', 'front' )->settingsInviteSystem( $form, $iInvited );
		}
		catch ( \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
}