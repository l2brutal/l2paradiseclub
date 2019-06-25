//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class invite_hook_isFURL extends _HOOK_CLASS_
{
	public static function furlDefinition( $revert=FALSE )
	{
		try
		{
			$furls = parent::furlDefinition( $revert );
		
			if ( !isset( $furls['settings_invitesystem'] ) )
			{
				$furls['settings_invitesystem'] = array(
					'friendly'  => 'settings/invitesystem',
					'real'      => 'app=core&module=system&controller=settings&area=invitesystem',
					'regex'    	=> array('settings\/invitesystem'),
					'params'   	=> array(),
				);
			}
			
			return $furls;
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
