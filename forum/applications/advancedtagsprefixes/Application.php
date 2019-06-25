<?php
/**
 * @brief		Advanced Tags & Prefixes Application Class
 * @author		<a href='http://www.sublimism.com'>Ryan Hoerr</a>
 * @copyright	(c) 2015 Ryan Hoerr
 * @package		IPS Social Suite
 * @subpackage	Advanced Tags & Prefixes
 * @since		17 Jan 2015
 * @version		
 */
 
namespace IPS\advancedtagsprefixes;

/**
 * Advanced Tags & Prefixes Application Class
 */
class _Application extends \IPS\Application
{
	public static $nodeSettingsKeys = array(
		'require_prefix',
		'default_prefix',
		'default_tags',
		'tag_mode',
		'allowed_prefixes',
		'tag_min',
		'tag_max',
	);
	
	protected $prefixCache;
	protected $nodeSettingsCache;
	
	/**
	 * Fetch the prefix cache, building if necessary.
	 */
	public function getPrefixCache()
	{
		if( $this->prefixCache !== NULL )
		{
			return $this->prefixCache;
		}
		
		if( !isset( \IPS\Data\Store::i()->advancedtagsprefixes_prefixCache ) )
		{
			$prefixes = array();
			
			foreach( \IPS\Db::i()->select( '*', 'advancedtagsprefixes_prefixes', '', 'prefix_title ASC' ) as $row )
			{
				$prefixes[ $row['prefix_title'] ] = $row;
			}
			
			\IPS\Data\Store::i()->advancedtagsprefixes_prefixCache = $prefixes;
			
			$this->prefixCache = $prefixes;
		}
		else {
			$this->prefixCache = \IPS\Data\Store::i()->advancedtagsprefixes_prefixCache;
		}
		
		return $this->prefixCache;
	}
	
	/**
	 * Fetch the node settings cache, building if necessary.
	 */
	public function getNodeSettingsCache()
	{
		if( $this->nodeSettingsCache !== NULL )
		{
			return $this->nodeSettingsCache;
		}
		
		if( !isset( \IPS\Data\Store::i()->advancedtagsprefixes_nodeSettingsCache ) )
		{
			$nodeSettings   = array();
			$settingKeyKeys = array_flip( static::$nodeSettingsKeys );
			
			foreach( \IPS\Db::i()->select( '*', 'advancedtagsprefixes_node_settings', '', 'id ASC' ) as $row )
			{
				$nodeSettings[ $row['node_app'] . '-' . $row['node_type'] . '-' . $row['node_id'] ] = array_intersect_key( $row, $settingKeyKeys );
			}
			
			\IPS\Data\Store::i()->advancedtagsprefixes_nodeSettingsCache = $nodeSettings;
			
			$this->nodeSettingsCache = $nodeSettings;
		}
		else {
			$this->nodeSettingsCache = \IPS\Data\Store::i()->advancedtagsprefixes_nodeSettingsCache;
		}
		
		return $this->nodeSettingsCache;
	}
	
	/**
	 * Fetch the node settings from cache by key. False if none.
	 * 
	 * Key should be app-type-id
	 */
	public function getNodeSettingsByKey( $key )
	{
		$settings = $this->getNodeSettingsCache();
		
		if( isset( $settings[ $key ] ) )
		{
			return $settings[ $key ];
		}
		
		return FALSE;
	}
	
	/**
	 * Fetch a prefix from cache by title (key).
	 */
	public function getPrefixByTitle( $title )
	{
		$prefixes = $this->getPrefixCache();
		
		if( isset( $prefixes[ $title ] ) )
		{
			return \IPS\advancedtagsprefixes\Prefix::constructFromData( $prefixes[ $title ] );
		}
		
		return FALSE;
	}
	
	/**
	 * Fetch a prefix from cache by ID.
	 */
	public function getPrefixById( $id )
	{
		$prefixes = $this->getPrefixCache();
		
		foreach( $prefixes as $prefix )
		{
			if( $prefix['prefix_id'] == $id )
			{
				return \IPS\advancedtagsprefixes\Prefix::constructFromData( $prefix );
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Clear the prefix cache.
	 */
	public function flushPrefixCache()
	{
		unset( \IPS\Data\Store::i()->advancedtagsprefixes_prefixCache );
		
		$this->prefixCache = NULL;
		
		return $this;
	}
	
	/**
	 * Clear the prefix cache.
	 */
	public function flushNodeSettingsCache()
	{
		unset( \IPS\Data\Store::i()->advancedtagsprefixes_nodeSettingsCache );
		
		$this->nodeSettingsCache = NULL;
		
		return $this;
	}
	
	/**
	 * Change all tags and prefixes matching $from to $to.
	 */
	public function updateTags( $from, $to, $app=NULL, $area=NULL )
	{
		$count	= 0;
		
		$where 	= array(
			array( 'binary tag_text=?', $from ),
		);
		
		if( !is_null( $app ) )
		{
			$where[] = array( "tag_meta_app=?", $app );
			
			if( !is_null( $area ) )
			{
				$where[] = array( "tag_meta_area=?", $area );
			}
		}
		
		if( \IPS\Settings::i()->tags_force_lower )
		{
			$to = mb_strtolower( $to );
		}
		
		/**
		 * Update any instances of the tag
		 */
		\IPS\DB::i()->update( 'core_tags', array( 'tag_text' => $to ), $where );
		
		/**
		 * Update any affected tag caches
		 */
		$where[0][0] = 'tag_cache_text like ?';
		$where[0][1] = '%' . $from . '%';
		
		$caches = \IPS\DB::i()->select( 'c.tag_cache_key, c.tag_cache_text', array( 'core_tags_cache', 'c' ), $where, NULL, NULL, array( 'tag_cache_key', 'tag_cache_text' ) );
		$caches->join( array( 'core_tags', 't' ), 't.tag_aai_lookup=c.tag_cache_key' );
		
		foreach( $caches as $cache )
		{
			$tags = @unserialize( $cache['tag_cache_text'] );
			
			if( $tags === FALSE )
			{
				$tags = json_decode( $cache['tag_cache_text'], 1 );
			}
			
			if( isset( $tags['tags'] ) )
			{
				foreach( $tags['tags'] as $k => $v )
				{
					if( $v == $from )
					{
						$tags['tags'][ $k ] = $to;
					}
				}
			}
			
			if( isset( $tags['prefix'] ) and $tags['prefix'] == $from )
			{
				$tags['prefix'] = $to;
			}
			
			\IPS\DB::i()->update( 'core_tags_cache', array( 'tag_cache_text' => json_encode( $tags ) ), array( array( 'tag_cache_key=?', $cache['tag_cache_key'] ) ) );
			
			$count++;
		}
		
		/**
		 * Update any affected search indexes
		 */
		if( \IPS\Application::load('core')->long_version >= 101026 ) // Search index modified in 4.1.9
		{
			$where = array(
				array( 'c.index_id=t.index_id' ),
				array( 't.index_tag=?', $from ),
			);
			
			if( !is_null( $app ) )
			{
				$where[] = array( 'c.index_class like ?', '%' . $app . '%' );
				
				if( !is_null( $area ) && $area != $app )
				{
					$where[] = array( 'c.index_class like ?', '%' . $area . '%' );
				}
			}
			
			$indexes = \IPS\DB::i()->select( 't.index_id, t.index_tag', array( array( 'core_search_index', 'c' ), array( 'core_search_index_tags', 't' ) ), $where );
			
			foreach( $indexes as $index )
			{
				if( !empty( $index['index_tag'] ) )
				{
					if( $index['index_tag'] == $from )
					{
						\IPS\DB::i()->update( 'core_search_index_tags', array( 'index_tag' => $to ), array( array( 'index_id=?', $index['index_id'] ), array( 'index_tag=?', $from ) ) );
					}
					
				}
			}
		}
		
		return $count;
	}
	
	/**
	 * Remove all tags and prefixes matching $tag.
	 */
	public function deleteTags( $tag, $app=NULL, $area=NULL )
	{
		$tag	= mb_strtolower( $tag );
		
		$where 	= array(
			array( 'binary tag_text=?', $tag ),
		);
		
		if( !is_null( $app ) )
		{
			$where[] = array( "tag_meta_app=?", $app );
			
			if( !is_null( $area ) )
			{
				$where[] = array( "tag_meta_area=?", $area );
			}
		}
		
		/**
		 * Delete any instances of the given tag
		 */
		$count = \IPS\Db::i()->delete( 'core_tags', $where );
		
		/**
		 * Update any affected tag caches
		 */
		$where[0][0] = 'tag_cache_text like ?';
		
		$caches = \IPS\DB::i()->select( 'c.*, t.tag_meta_app, t.tag_meta_area', array( 'core_tags_cache', 'c' ), $where );
		$caches->join( array( 'core_tags', 't' ), 't.tag_aai_lookup=c.tag_cache_key' );
		
		$processed = array();
		
		foreach( $caches as $cache )
		{
			// Filtering to avoid pesky group-by
			if( isset( $processed[ $cache['tag_cache_key'] ] ) ) {
				continue;
			}
			else {
				$processed[ $cache['tag_cache_key'] ] = TRUE;
			}
			
			$tags = @unserialize( $cache['tag_cache_text'] );
			
			if( $tags === FALSE )
			{
				$tags = json_decode( $cache['tag_cache_text'], 1 );
			}
			
			if( is_array( $tags['tags'] ) )
			{
				foreach( $tags['tags'] as $k => $v )
				{
					if( mb_strtolower( $v ) == $tag )
					{
						unset( $tags['tags'][ $k ] );
					}
				}
			}
			
			if( isset( $tags['prefix'] ) and mb_strtolower( $tags['prefix'] ) == $tag )
			{
				$tags['prefix'] = '';
			}
			
			\IPS\DB::i()->update( 'core_tags_cache', array( 'tag_cache_text' => json_encode( $tags ) ), array( array( 'tag_cache_key=?', $cache['tag_cache_key'] ) ) );
		}
		
		/**
		 * Update any affected search indexes
		 */
		if( \IPS\Application::load('core')->long_version >= 101026 ) // Search index modified in 4.1.9
		{
			$where = array(
				array( 't.index_tag=?', $tag ),
			);
			
			if( !is_null( $app ) )
			{
				$where[] = array( 'c.index_class like ?', '%' . $app . '%' );
				
				if( !is_null( $area ) && $area != $app )
				{
					$where[] = array( 'c.index_class like ?', '%' . $area . '%' );
				}
			}
			
			$indexes = \IPS\DB::i()->select( 'c.index_id, t.index_tag', array( 'core_search_index', 'c' ), $where );
			$indexes->join( array( 'core_search_index_tags', 't' ), 't.index_id=c.index_id', 'INNER' );
			
			$indexIds = array();
			foreach( $indexes as $index )
			{
				if( mb_strtolower( $index['index_tag'] ) == $tag )
				{
					$indexIds[] = $index['index_id'];
				}
			}
			
			if( !empty( $indexIds ) )
			{
				\IPS\DB::i()->delete(
					'core_search_index_tags',
					array(
						array( 'index_id in (?)', implode( ',', $indexIds ) ),
						array( 'index_tag=?', $tag ),
					)
				);
			}
		}
		
		return $count;
	}
	
	/**
	 * [Node] Get Node Icon
	 *
	 * @return	string
	 */
	protected function get__icon()
	{
		return 'tags';
	}
}
