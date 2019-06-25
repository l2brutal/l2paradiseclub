//<?php

class advancedtagsprefixes_hook_forumLastPostPrefix extends _HOOK_CLASS_
{
	/**
	 * Set last comment
	 *
	 * @param	\IPS\Content\Comment	$comment	The latest comment or NULL to work it out
	 * @return	void
	 */
	public function setLastComment( \IPS\Content\Comment $comment=NULL )
	{
		try
		{
		/**
		 * Save prefix off with the topic
		 */
			if ( $comment === NULL )
			{
				try
				{
					$select = \IPS\Db::i()->select( '*', 'forums_topics', array( "forums_topics.forum_id={$this->id} AND forums_topics.approved=1" ), 'forums_topics.last_post DESC', 1 )->first();
					$topic = \IPS\forums\Topic::constructFromData( $select );
					
					$this->last_prefix = $topic->prefix();
					return call_user_func_array( 'parent::setLastComment', func_get_args() );
				}
				catch ( \UnderflowException $e )
				{
					$this->last_prefix = NULL;
					return call_user_func_array( 'parent::setLastComment', func_get_args() );
				}
			}
			
			$this->last_prefix = $comment->item()->prefix();
			
			return call_user_func_array( 'parent::setLastComment', func_get_args() );
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
	 * Get last post data
	 *
	 * @return	array|NULL
	 */
	public function lastPost()
	{
		try
		{
		/**
		 * Load prefix for display with last post info
		 */
			$result = call_user_func_array( 'parent::lastPost', func_get_args() );
			
			if( !is_null( $result ) )
			{
			/**
			 * Must recreate the condition structure to get the data from the right place.
			 */
				if
				(
					!$this->can_view_others
					and
					(
						!(
							\IPS\Member::loggedIn()->modPermission( 'forums' ) === -1
							or
							(
								is_array( \IPS\Member::loggedIn()->modPermission( 'forums' ) )
								and
								in_array( $this->_id, \IPS\Member::loggedIn()->modPermission( 'forums' ) )
							)
						)
						and
						!\IPS\Member::loggedIn()->modPermission( 'can_read_all_topics' )
					)
				)
				{
					try
					{
						$lastPost = \IPS\forums\Topic\Post::constructFromData( \IPS\Db::i()->select( '*', 'forums_posts', array( 'topic_id=? AND queued=0', \IPS\Db::i()->select( 'tid', 'forums_topics', array( 'forum_id=? AND approved=1 AND starter_id=?', $this->_id, \IPS\Member::loggedIn()->member_id ), 'last_post DESC', 1 )->first() ), 'post_date DESC', 1 )->first() );
						
						$result['prefix']			= $lastPost->item()->prefix();
						$result['prefix_encoded']	= rawurlencode( $result['prefix'] );
					}
					catch ( \UnderflowException $e )
					{
					}
				}
				elseif ( !$this->permission_showtopic and !$this->can('read') )
				{
				}
				else
				{
					if( $this->last_post and $result['date'] == $this->last_post )
					{
						$result['prefix']			= $this->last_prefix;
						$result['prefix_encoded']	= rawurlencode( $result['prefix'] );
					}
				}
			}
			
			return $result;
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
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		try
		{
			parent::form( $form );
			
			if ( \IPS\Settings::i()->tags_enabled )
			{
				$form->add( new \IPS\Helpers\Form\YesNo( 'show_prefix_in_desc', $this->show_prefix_in_desc, FALSE, array(), NULL, NULL, NULL, 'show_prefix_in_desc' ) );
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
     * Set form for creating a node of this type in a club
     *
     * @param	\IPS\Helpers\Form	$form	Form object
     * @return	void
     */
    public function clubForm( \IPS\Helpers\Form $form )
    {
	try
	{
	        parent::clubForm( $form );
	
	        if ( static::isTaggable() === TRUE )
	        {
            /**
             * If there are multiple tabs, add another one for ours.
             */
	            if ( count( $form->elements ) > 1 )
	            {
                // No icon: looks nice, but no way to tell if the existing tabs have icons. aarg.
	                $form->addTab( 'advancedtagsprefixes_node_tab'/*, 'tags'*/ );
	            }
	
	            $form->addHeader('advancedtagsprefixes_node_tab');
	
	            $isForum = ( $this instanceof \IPS\forums\Forum ) ? TRUE : FALSE;
	
	            if ( $isForum === TRUE )
	            {
                /**
                 * If forums, toggle our settings on the 'allow prefixes' and 'allow tags' settings.
                 */
	                $tagFieldKeys		= array( 'bw_disable_prefixes', 'forum_tag_predefined', 'tag_mode', 'require_prefix', 'default_prefix', 'default_tags', 'show_prefix_in_desc', 'tag_min', 'tag_max' );
	                $prefixFieldKeys	= array( 'require_prefix', 'default_prefix', 'show_prefix_in_desc', 'allowed_prefixes' );
	
	                $form->add( new \IPS\Helpers\Form\YesNo( 'bw_disable_tagging', !$this->forums_bitoptions['bw_disable_tagging'], FALSE, array( 'togglesOn' => $tagFieldKeys ), NULL, NULL, NULL, 'bw_disable_tagging' ), NULL );
	                $form->add( new \IPS\Helpers\Form\YesNo( 'bw_disable_prefixes', !$this->forums_bitoptions['bw_disable_prefixes'], FALSE, array( 'togglesOn' => $prefixFieldKeys ), NULL, NULL, NULL, 'bw_disable_prefixes' ), NULL );
	            }
	
            /**
             * Gather sources for settings
             */
	            $tagModes		= array(
	                'inherit'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_inherit'),
	                'open'		=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_open'),
	                'closed'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_closed'),
	                'prefix'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_prefix'),
	            );
	
	            $app			= \IPS\Application::load('advancedtagsprefixes');
	
	            $allowedTags	= array();
	            foreach ( explode( ',', $this->tag_predefined ) as $v )
	            {
	                $allowedTags[ $v ] = $v;
	            }
	
	            $allPrefixes	= array();
	            foreach ( $app->getPrefixCache() as $k => $v )
	            {
	                $prefix		= \IPS\advancedtagsprefixes\Prefix::constructFromData( $v );
	
	                if ( $prefix !== FALSE )
	                {
	                    $allPrefixes[ $k ]	= $k;
	                }
	            }
	
	            if ( count( $allPrefixes ) < 2 )
	            {
	                $allPrefixes	= array( '' => '' ) + $allPrefixes;
	            }
	
            /**
             * Add our special settings
             */
	            if ( $isForum === TRUE )
	            {
                // Adding input that may otherwise be missing (if \IPS\Settings::i()->tags_open_system)
	                $form->add( new \IPS\Helpers\Form\Text( 'forum_tag_predefined', implode( ',', $allowedTags ), FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => 'forum_tag_predefined_unlimited' ), NULL, NULL, NULL, 'forum_tag_predefined' ), NULL );
	            }
	
	            $form->add( new \IPS\Helpers\Form\YesNo( 'require_prefix', $this->require_prefix, FALSE, array(), NULL, NULL, NULL, 'require_prefix' ) );
	            $form->add( new \IPS\Helpers\Form\Select( 'tag_mode', $this->tag_mode, FALSE, array( 'options' => $tagModes ), NULL, NULL, NULL, 'tag_mode' ) );
	            $form->add( new \IPS\Helpers\Form\Text( 'default_tags', $this->default_tags, FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => NULL ), NULL, NULL, NULL, 'default_tags' ) );
	            $form->add( new \IPS\Helpers\Form\Text( 'allowed_prefixes', $this->allowed_prefixes, FALSE, array( 'autocomplete' => array( 'unique' => 'true', 'source' => $allPrefixes, 'freeChoice' => FALSE ) ), NULL, NULL, NULL, 'allowed_prefixes' ) );
	            $form->add( new \IPS\Helpers\Form\Select( 'default_prefix', $this->default_prefix, FALSE, array( 'options' => array( '' => '' ) + $allPrefixes ), NULL, NULL, NULL, 'default_prefix' ) );
	            $form->add( new \IPS\Helpers\Form\Number( 'tag_min', !is_null( $this->tag_min ) ? $this->tag_min : -1, FALSE, array( 'unlimited' => -1, 'unlimitedLang' => 'use_default' ), NULL, NULL, NULL, 'tag_min' ) );
	            $form->add( new \IPS\Helpers\Form\Number( 'tag_max', !is_null( $this->tag_max ) ? $this->tag_max : -1, FALSE, array( 'unlimited' => -1, 'unlimitedLang' => 'use_default' ), NULL, NULL, NULL, 'tag_max' ) );
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
     * Save club form
     *
     * @param	\IPS\Member\Club	$club	The club
     * @param	array				$values	Values
     * @return	void
     */
    public function saveClubForm( \IPS\Member\Club $club, $values )
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
         * Set default tag values
         */
	        $this->forums_bitoptions['bw_disable_tagging'] = !$values['bw_disable_tagging'];
	        $this->forums_bitoptions['bw_disable_prefixes'] = !$values['bw_disable_prefixes'];
	        $this->tag_predefined = implode( ',', $values['forum_tag_predefined'] );
	
        /**
         * Do the normal node save hereish.
         */
	        parent::saveClubForm( $club, $values );
	
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
