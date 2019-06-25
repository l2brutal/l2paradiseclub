//<?php

class invite_hook_is_getConvertedMembers extends _HOOK_CLASS_
{
    public function getConvertedMembers()
    {
	try
	{
			$converted = array();
			
			if( \IPS\Settings::i()->is_on AND \IPS\Session::i()->member->group['is_canvite'] )
			{
				/* Accounts converted */
				foreach( \IPS\Db::i()->select( '*', 'invite_invites', array( "invite_sender_id=? AND invite_status=?", \IPS\Member::loggedIn()->member_id, 1 ), NULL, array( 0, 10 ) )->join( 'core_members', 'invite_invites.invite_conv_member_id=core_members.member_id' ) as $member )
				{
					$converted[ $member['invite_conv_member_id'] ] = $member;
				}
			}
	
			return $converted;
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