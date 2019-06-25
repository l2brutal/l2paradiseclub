//<?php

class invite_hook_is_Actions extends _HOOK_CLASS_
{
	/**
	 * Report Status
	 *
	 * @return	void
	 */
	protected function inviteRevokeAccess()
	{	
			try
			{
			\IPS\Session::i()->csrfCheck();
	
			if ( \IPS\Settings::i()->is_on AND \IPS\Member::loggedIn()->isAdmin() )
			{
				\IPS\Db::i()->update( 'core_members', array( 'invite_revoke_access' => 1 ), array( 'member_id=?', $this->member->member_id ) );
				
				/* Clear create menu caches */
				\IPS\Member::clearCreateMenu();
				
				\IPS\Output::i()->redirect( $this->member->url(), 'invitation_revoked' );
			}
			else
			{
				\IPS\Output::i()->error( 'invite_no_permission', '2C138/3', 403, '' );
			}
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

	/**
	 * Report Status
	 *
	 * @return	void
	 */
	protected function inviteAllowAccess()
	{
		try
		{
			\IPS\Session::i()->csrfCheck();
	
			if ( \IPS\Settings::i()->is_on AND \IPS\Member::loggedIn()->isAdmin() )
			{
				\IPS\Db::i()->update( 'core_members', array( 'invite_revoke_access' => 0 ), array( 'member_id=?', $this->member->member_id ) );
	
				/* Clear create menu caches */
				\IPS\Member::clearCreateMenu();
	
				\IPS\Output::i()->redirect( $this->member->url(), 'invitation_allowed' );
			}
			else
			{
				\IPS\Output::i()->error( 'invite_no_permission', '2C138/3', 403, '' );
			}
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