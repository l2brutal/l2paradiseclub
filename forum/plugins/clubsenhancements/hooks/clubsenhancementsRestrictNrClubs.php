//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook34 extends _HOOK_CLASS_
{
	/**
	 * Create
	 *
	 * @return	void
	 */
	protected function create()
	{
		try
		{
			if( \IPS\Settings::i()->clubsenhancementsRestrictClubsNr > 0 )
			{
				$totalClubs = \IPS\Db::i()->select( 'count(*)', 'core_clubs', array( "owner=?", \IPS\Member::loggedIn()->member_id ) )->first();
	
				if( $totalClubs >= \IPS\Settings::i()->clubsenhancementsRestrictClubsNr )
				{
					\IPS\Output::i()->error( 'clubsenhancementsRestrictClubsError', 'Clubs Enhancements/12', 403, '' );
				}
			}
	
			parent::create();
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
