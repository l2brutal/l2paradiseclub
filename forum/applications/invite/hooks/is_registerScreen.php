//<?php

class invite_hook_is_registerScreen extends _HOOK_CLASS_
{
	static public function buildRegistrationForm()
	{
		try
		{
			$form 		= call_user_func_array( 'parent::buildRegistrationForm', func_get_args() );
			
			if ( \IPS\Settings::i()->is_on )
			{
				if ( \IPS\Settings::i()->is_requireinvite )
				{
					$form->add( new \IPS\Helpers\Form\Text( 'invite_code_req', NULL, TRUE, array( 'maxLength' => 32 ), function( $val )
					{
						try
						{
							if ( ! $val )
							{
								return true;
							}
			
							try
							{
								$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_code=?', $val ) )->first();
							}
							catch( \UnderflowException $ex )
							{
								/* The invitation code does not exists */
								\IPS\Output::i()->error( 'invite_does_not_exist', '2C135/A', 403, '' );
							}
	
							/* The invitation code is already in use */
							if ( $invite['invite_status'] == 1 )
							{
								\IPS\Output::i()->error( 'invite_already_in_use', '2C135/A', 403, '' );
							}
	
							/* The invitation code is already in use */
							if ( $invite['invite_status'] == 2 )
							{
								\IPS\Output::i()->error( 'invite_already_expired', '2C135/A', 403, '' );
							}
						}
						catch ( \OutOfRangeException $e )
						{
							/* Slug is OK as load failed */
							return true;
						}
					} ), 'password_confirm' );
	
					if ( \IPS\Settings::i()->is_emailfrominvite AND isset( \IPS\Request::i()->email_address ) AND \IPS\Request::i()->email_address )
					{
						$form->elements[''][ 'email_address' ]->value = \IPS\Request::i()->email_address;
					}
				}
				else
				{
					$form->add( new \IPS\Helpers\Form\Text( 'invite_code_opt', NULL, FALSE, array( 'maxLength' => 32 ), function( $val )
					{
						try
						{
							if ( ! $val )
							{
								return true;
							}
	
							try
							{
								$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_code=?', $val ) )->first();
							}
							catch( \UnderflowException $ex )
							{
								/* The invitation code does not exists */
								\IPS\Output::i()->error( 'invite_does_not_exist', '2C135/A', 403, '' );
							}
	
							/* The invitation code is already in use */
							if ( $invite['invite_status'] == 1 )
							{
								\IPS\Output::i()->error( 'invite_already_in_use', '2C135/A', 403, '' );
							}
	
							/* The invitation code is already in use */
							if ( $invite['invite_status'] == 2 )
							{
								\IPS\Output::i()->error( 'invite_already_expired', '2C135/A', 403, '' );
							}
						}
						catch ( \OutOfRangeException $e )
						{
							/* Slug is OK as load failed */
							return true;
						}
					} ), 'password_confirm' );
				}
			}
	
			return $form;
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

    public static function _createMember( $values, $profileFields )
	{
		try
		{
			if ( \IPS\Settings::i()->is_on )
			{
				$val = \IPS\Settings::i()->is_requireinvite ? 'invite_code_req' : 'invite_code_opt';
	
				if( $values[ $val ] )
				{
					$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_code=?', $values[ $val ] ) )->first();
	
					/* Force email address */
					if( \IPS\Settings::i()->is_emailfrominvite )
					{
						if( $values['email_address'] != $invite['invite_invited_email'] )
						{
							\IPS\Output::i()->error( 'is_error_notsameemails', '2S129/1', 403, '' );
						}
					}
				}
			}
	
	        $member = parent::_createMember( $values, $profileFields );
	
			if ( \IPS\Settings::i()->is_on )
			{
				$val = \IPS\Settings::i()->is_requireinvite ? 'invite_code_req' : 'invite_code_opt';
	
				/* Update Member */
				if( $values[ $val ] )
				{
					$invite = \IPS\Db::i()->select( '*', 'invite_invites', array( 'invite_code=?', $values[ $val ] ) )->first();
	
					/* Force email address */
					if( \IPS\Settings::i()->is_emailfrominvite )
					{
						if( $values['email_address'] != $invite['invite_invited_email'] )
						{
							\IPS\Output::i()->error( 'is_error_notsameemails', '2S129/1', 403, '' );
							return $member;
						}
					}
	
					if( \IPS\Settings::i()->is_registrationgroup_toggle )
					{
						$member->member_group_id = \IPS\Settings::i()->is_registrationgroup;
					}
	
					$member->invited_by = $invite['invite_sender_id'];
	
					if( \IPS\Settings::i()->is_registration_earninvitations AND \IPS\Settings::i()->is_registration_earninvitations_nr > 0 )
					{
						$member->invites_remaining = \IPS\Settings::i()->is_registration_earninvitations_nr;
					}
	
					$member->save();
	
					/* Update Invite */
					\IPS\Db::i()->update( 'invite_invites', array( 'invite_status' => 1, 'invite_conv_member_id' => $member->member_id, 'invite_conv_date' => time(), 'invite_expiration_date' => 0 ), array( 'invite_id=?', $invite['invite_id'] ) );
				}
			}
	
	        return $member;
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