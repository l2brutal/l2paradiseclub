//<?php

abstract class advancedtagsprefixes_hook_nodeModel extends _HOOK_CLASS_
{
	/**
	 * Construct Load Query
	 *
	 * @param	int|string	$id					ID
	 * @param	string		$idField			The database column that the $id parameter pertains to
	 * @param	mixed		$extraWhereClause	Additional where clause(s)
	 * @return	\IPS\Db\Select
	 */
	protected static function constructLoadQuery( $id, $idField, $extraWhereClause )
	{
		try
		{
			$select = parent::constructLoadQuery( $id, $idField, $extraWhereClause );
			
			if ( static::isTaggable() === TRUE and !is_null( static::$permApp ) and !is_null( static::$permType ) )
			{
				$select->join(
					array( 'advancedtagsprefixes_node_settings', 'atp' ),
					array( "atp.node_app=? AND atp.node_type=? AND atp.node_id=" . static::$databaseTable . "." . static::$databasePrefix . static::$databaseColumnId, static::$permApp, static::$permType )
				);
				
				$position = mb_strpos( $select->query, ' FROM' );
				
				if( $position !== FALSE ) {
					$columns = '';
					
					foreach ( \IPS\advancedtagsprefixes\Application::$nodeSettingsKeys as $key )
					{
						$columns .= ', atp.' . $key;
					}
					
					$select->query = substr_replace( $select->query, $columns, $position, 0 );
				}
			}
			
			return $select;
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
	 * Construct ActiveRecord from database row
	 *
	 * @param	array	$data							Row from database table
	 * @param	bool	$updateMultitonStoreIfExists	Replace current object in multiton store if it already exists there?
	 * @return	static
	 */
	public static function constructFromData( $data, $updateMultitonStoreIfExists = TRUE )
	{
		try
		{
			if ( static::isTaggable() === TRUE and !is_null( static::$permApp ) and !is_null( static::$permType ) )
			{
			/**
			 * When loading data, join on from our node settings... because there's no guarantee that our join actually happened.
			 * 
			 * Loads via loadIntoMemory() totally bypass constructLoadQuery(), and are unhookable.
			 */
				$key = static::$permApp . '-' . static::$permType . '-' . $data[ static::$databasePrefix . static::$databaseColumnId ];
				
				$adtlData = \IPS\Application::load('advancedtagsprefixes')->getNodeSettingsByKey( $key );
				
				if ( $adtlData !== FALSE )
				{
					$data = array_merge( $data, $adtlData );
				}
			}
			
			return parent::constructFromData( $data, $updateMultitonStoreIfExists );
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
	 * [Node] Save Add/Edit Form
	 *
	 * @param	array	$values	Values from the form
	 * @return	void
	 */
	public function saveForm( $values )
	{
		try
		{
		/**
		 * Unpack our values and cleanse.
		 */
			if ( isset( $values['allowed_prefixes'] ) and !empty( $values['allowed_prefixes'] ) )
			{
				if( !is_array( $values['allowed_prefixes'] ) )
				{
					$values['allowed_prefixes'] = explode( ',', $values['allowed_prefixes'] );
				}
				
				array_map( 'trim', $values['allowed_prefixes'] );
				
				$values['allowed_prefixes'] = array_filter( $values['allowed_prefixes'] );
			}
			
			$nodeSettings = array();
			foreach ( \IPS\advancedtagsprefixes\Application::$nodeSettingsKeys as $key )
			{
				$nodeSettings[ $key ] = isset( $values[ $key ] ) ? $values[ $key ] : NULL;
				
				unset( $values[ $key ] );
			}
			
		/**
		 * Do the normal node save hereish.
		 */
			parent::saveForm( $values );
			
		/**
		 * Save our values after the node.
		 */
			$this->saveNodeSettings( $nodeSettings );
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
	 * [ActiveRecord] Duplicate
	 *
	 * @return	void
	 */
	public function __clone()
	{
		try
		{
		/**
		 * Pull our node settings out so they don't get inserted with the parent (bad)
		 * and so that we can clone them ourselves afterwards (good)
		 */
			$nodeSettings = array();
			foreach ( \IPS\advancedtagsprefixes\Application::$nodeSettingsKeys as $key )
			{
				$nodeSettings[ $key ] = isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : NULL;
				
				unset( $this->_data[ $key ] );
				unset( $this->changed[ $key ] );
			}
			
			parent::__clone();
			
			$this->saveNodeSettings( $nodeSettings );
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
	 * Save Changed Columns
	 *
	 * @return	void
	 */
	public function save()
	{
		try
		{
		/**
		 * Ensure that IPS does not attempt to save our added fields with the parent (bad)
		 */
			foreach ( \IPS\advancedtagsprefixes\Application::$nodeSettingsKeys as $key )
			{
				unset( $this->_data[ $key ] );
				unset( $this->changed[ $key ] );
			}
	
			parent::save();
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
	 * Determine whether the current node class is taggable. Only way to find this is through the child content item class. Isn't that grand?
	 */
	public static function isTaggable()
	{
		try
		{
			if ( isset( static::$contentItemClass ) and \IPS\Settings::i()->tags_enabled and in_array( 'IPS\Content\Tags', class_implements( static::$contentItemClass ) ) )
			{
				return TRUE;
			}
			
			return FALSE;
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
	 * Save AT&P node data to the DB.
	 *
	 * @param  array $nodeSettings
	 * @return $this
	 */
	protected function saveNodeSettings( $nodeSettings )
	{
		try
		{
			if ( static::isTaggable() === TRUE and !is_null( static::$permApp ) and !is_null( static::$permType ) and !empty( $nodeSettings ) )
			{
				$primaryKey	= static::$databaseColumnId;
	
				if ( !isset( $nodeSettings['default_tags'] ) )
				{
					$nodeSettings['default_tags'] = array();
				}
	
				if ( !is_array( $nodeSettings['default_tags'] ) )
				{
					$nodeSettings['default_tags'] = explode( ',', $nodeSettings['default_tags'] );
				}
	
				if ( !isset( $nodeSettings['allowed_prefixes'] ) )
				{
					$nodeSettings['allowed_prefixes'] = array();
				}
	
				if ( !is_array( $nodeSettings['allowed_prefixes'] ) )
				{
					$nodeSettings['allowed_prefixes'] = explode( ',', $nodeSettings['allowed_prefixes'] );
				}
	
				$data		= array(
					'node_app'			=> static::$permApp,
					'node_type'			=> static::$permType,
					'node_id'			=> $this->$primaryKey,
					'require_prefix'	=> isset( $nodeSettings['require_prefix'] ) ? (int)$nodeSettings['require_prefix'] : 0,
					'default_prefix'	=> $nodeSettings['default_prefix'] ?: NULL,
					'default_tags'		=> !empty( $nodeSettings['default_tags'] ) ? implode( ',', $nodeSettings['default_tags'] ) : NULL,
					'tag_mode'			=> $nodeSettings['tag_mode'] ?: NULL,
					'allowed_prefixes'	=> !empty( $nodeSettings['allowed_prefixes'] ) ? implode( ',', $nodeSettings['allowed_prefixes'] ) : NULL,
					'tag_min'			=> (int)$nodeSettings['tag_min'],
					'tag_max'			=> (int)$nodeSettings['tag_max'],
				);
	
				\IPS\Db::i()->insert( 'advancedtagsprefixes_node_settings', $data, TRUE );
	
				\IPS\Application::load('advancedtagsprefixes')->flushNodeSettingsCache();
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
