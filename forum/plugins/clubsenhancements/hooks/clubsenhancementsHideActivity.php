//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook35 extends _HOOK_CLASS_
{
	/**
	 * Filter by club
	 *
	 * @param	\IPS\Member\Club|int|array|null	$club	The club, or array of club IDs, or NULL to exclude content from clubs
	 * @return	\IPS\Content\Search\Query	(for daisy chaining)
	 */
	public function filterByClub( $club )
	{
		try
		{
			foreach ( \IPS\Db::i()->select( '*', 'core_clubs_node_map', array( 'club_id=? AND node_enabled=?', $club instanceof \IPS\Member\Club ? $club->id : $club, 0 ) ) as $row )
			{
				$disabled[] = $row['node_id'];
			}
	
			if ( $club === NULL )
			{
				$this->where[] = 'index_club_id IS NULL';
			}
			if ( is_array( $club ) )
			{
				$this->where[] = array( \IPS\Db::i()->in( 'index_club_id', $club ) );
			}
			else
			{
				$this->where[] = array( 'index_club_id=?', $club instanceof \IPS\Member\Club ? $club->id : $club );
				if( is_array( $disabled ) AND count( $disabled ) )
				{
					foreach( $disabled as $id )
					{
						$this->where[] = array( 'index_container_id!=?', $id );
					}
				}
			}
	
			return $this;
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
