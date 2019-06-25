<?php


namespace IPS\advancedtagsprefixes\setup\upg_30100;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 3.1.0 Upgrade Code
 */
class _Upgrade
{
	/**
	 * Convert forum prefix settings to new node settings.
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step1()
	{
		/**
		 * Load all forums data
		 * Parse out our settings and prefixes
		 * Insert new settings rows
		 */
		$data = array();
		
		foreach ( \IPS\Db::i()->select( '*', 'forums_forums', '', 'id ASC' ) as $forum )
		{
			$row = array(
				'node_app'			=> 'forums',
				'node_type'			=> 'forum',
				'node_id'			=> $forum['id'],
				'require_prefix'	=> $forum['require_prefix'] ?: '0',
				'default_prefix'	=> $forum['default_prefix'] ?: NULL,
				'default_tags'		=> $forum['default_tags'] ?: NULL,
				'tag_mode'			=> $forum['tag_mode'] ?: 'inherit',
				'allowed_prefixes'	=> array(),
			);
			
			/**
			 * Convert old tag/prefix setting to prefixes list
			 */
			$tags	= array_filter( explode( ',', $forum['tag_predefined'] ) );
			$app	= \IPS\Application::load('advancedtagsprefixes');
			
			foreach ( $tags as $tag )
			{
				$prefix	= $app->getPrefixByTitle( $tag );
				
				if ( $prefix !== FALSE and $prefix->canBeUsedFor( $container ) )
				{
					$row['allowed_prefixes'][ $tag ] = $tag;
				}
			}
			
			$row['allowed_prefixes'] = implode( ',', $row['allowed_prefixes'] );
			
			$data[] = $row;
		}
		
		if ( count( $data ) > 0 )
		{
			/**
			 * Insert our converted settings data.
			 */
			\IPS\Db::i()->insert( 'advancedtagsprefixes_node_settings', $data, TRUE, TRUE );
		}
		
		return TRUE;
	}
}
