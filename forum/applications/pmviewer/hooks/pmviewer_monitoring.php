//<?php

class pmviewer_hook_pmviewer_monitoring extends _HOOK_CLASS_
{
	/**
	 * Process created object AFTER the object has been created
	 *
	 * @param	\IPS\Content\Comment|NULL	$comment	The first comment
	 * @param	array						$values		Values from form
	 * @return	void
	 */
	protected function processAfterCreate( $comment, $values )
	{
		try
		{
			if ( \IPS\Settings::i()->pmviewer_monitoring_enable AND \IPS\Settings::i()->pmviewer_monitoring_keywords )
			{
				$found 		= 0;
				$haystack 	= $comment->post;
				$words 		= explode( ',', \IPS\Settings::i()->pmviewer_monitoring_keywords );
				$detected	= array();
	
				foreach( $words as $word )
				{
					if( mb_strpos( mb_strtolower( $comment->post ), mb_strtolower( $word ) ) !== false )
					{
					    $found 		= 1;
					    $detected[] = mb_strtolower( $word );
					}
				}
	
				if ( $found AND \IPS\Settings::i()->pmviewer_monitoring_groups )
				{
					$notification = new \IPS\Notification( \IPS\Application::load('pmviewer'), 'keyword_used', $this, array( $this ) );
					$_groups	= explode( ',', \IPS\Settings::i()->pmviewer_monitoring_groups );
					$_set		= array();
	
					foreach( $_groups as $_group )
					{
						$_set[]		= "FIND_IN_SET(" . $_group . ",mgroup_others)";
						$groupsF[] 	= $_group;
					}
	
					if( count($_set) )
					{
						$where[] = array( "( member_group_id IN(" . implode( ',', $groupsF ) . ") OR " . implode( ' OR ', $_set ) . ' )' );
					}
	
					foreach ( \IPS\Db::i()->select( '*', 'core_members', $where ) as $user )
					{
						$notification->recipients->attach( \IPS\Member::constructFromData( $user ) );
					}
	
					$notification->send(); 	
				}
			}
	
			return parent::processAfterCreate( $comment, $values );
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