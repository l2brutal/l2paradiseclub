//<?php

abstract class advancedtagsprefixes_hook_addPrefixToForm extends _HOOK_CLASS_
{
	/**
	 * Get elements for add/edit form
	 *
	 * @param	\IPS\Content\Item|NULL	$item		The current item if editing or NULL if creating
	 * @param	\IPS\Node\Model|NULL	$container	Container (e.g. forum), if appropriate
	 * @return	array
	 */
	public static function formElements( $item=NULL, \IPS\Node\Model $container=NULL )
	{
		try
		{
		/**
		 * Short-circuit if tags are not available here
		 */
			if ( !in_array( 'IPS\Content\Tags', class_implements( get_called_class() ) ) or !static::canTag( NULL, $container ) )
			{
				return call_user_func_array( 'parent::formElements', func_get_args() );
			}
			
			
		/**
		 * Get default fields.
		 */
			$fields	= call_user_func_array( 'parent::formElements', func_get_args() );
			
		/**
		 * Add tag input if missing. Bad logic.
		 */
			if( !isset( $fields['tags'] ) and ( $container->tag_mode === 'closed' or $container->tag_mode === 'open' ) )
			{
				$options = array( 'autocomplete' => array( 'unique' => TRUE, 'source' => static::definedTags( $container ), 'freeChoice' => \IPS\Settings::i()->tags_open_system ? TRUE : FALSE ) );
	
				if ( \IPS\Settings::i()->tags_force_lower )
				{
					$options['autocomplete']['forceLower'] = TRUE;
				}
				if ( \IPS\Settings::i()->tags_min )
				{
					$options['autocomplete']['minItems'] = \IPS\Settings::i()->tags_min;
				}
				if ( \IPS\Settings::i()->tags_max )
				{
					$options['autocomplete']['maxItems'] = \IPS\Settings::i()->tags_max;
				}
				if ( \IPS\Settings::i()->tags_len_min )
				{
					$options['autocomplete']['minLength'] = \IPS\Settings::i()->tags_len_min;
				}
				if ( \IPS\Settings::i()->tags_len_max )
				{
					$options['autocomplete']['maxLength'] = \IPS\Settings::i()->tags_len_max;
				}
				if ( \IPS\Settings::i()->tags_clean )
				{
					$options['autocomplete']['filterProfanity'] = TRUE;
				}
				
				$options['autocomplete']['prefix'] = static::canPrefix( NULL, $container );
				
				$tagsInput = new \IPS\Helpers\Form\Text( static::$formLangPrefix . 'tags', $item ? ( $item->prefix() ? array_merge( array( 'prefix' => $item->prefix() ), $item->tags() ) : $item->tags() ) : array(), \IPS\Settings::i()->tags_min and \IPS\Settings::i()->tags_min_req, $options );
				
			// Inject tag input before content
				$tmp	= array();
				foreach( $fields as $k => $field )
				{
					if( $k == 'content' )
					{
						$tmp['tags']	= $tagsInput;
					}
					
					$tmp[ $k ]	= $field;
				}
				
				$fields = $tmp;
			}
			
		/**
		 * Build our prefix input, if necessary.
		 */
			if ( static::canPrefix( NULL, $container ) )
			{
			// Build allowed prefix options
				$app		= \IPS\Application::load('advancedtagsprefixes');
				$prefixes	= explode( ',', $container->allowed_prefixes );
				$options	= array( '' => \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_prefix_select_one') );
				foreach ( $prefixes as $k => $v )
				{
					$prefix				= $app->getPrefixByTitle( $v );
					
					if ( $prefix !== FALSE and $prefix->canBeUsedFor( $container ) )
					{
						$options[ $v ] = $v;
					}
				}
				
			// Do nothing if no options are available. Note, if permissions mean no prefixes available for a group, this means they can post even if required.
				if ( count( $options ) > 1 )
				{
				// Prefix required? Depends on container and member.
					$requirePrefix = ( $container->require_prefix ) ? TRUE : FALSE;
					if( $requirePrefix === TRUE and \IPS\Settings::i()->prefix_exclude_supers and \IPS\Member::loggedIn()->modPermissions() == '*' )
					{
						$requirePrefix = FALSE;
					}
					
				// Create the prefix input.
					$prefixInput	= new \IPS\Helpers\Form\Select( static::$formLangPrefix . 'prefix', $item ? $item->prefix() : $container->default_prefix, $requirePrefix, array( 'options' => $options, 'sort' => TRUE ) );
				}
	
            /**
             * Overwrite prefix option
             */
	            if ( isset( $fields['tag'] ) )
	            {
	                unset( $fields['tag']->options['autocomplete']['prefix'] );
	
	                if ( $item !== NULL )
	                {
	                    $fields['tag']->value = array_diff( $fields['tag']->value, array( $item->prefix() ) );
	                }
	            }
			}
	
        /**
         * Inject our prefix input before the tags one, if necessary.
         */
	        if ( isset( $prefixInput ) )
	        {
	            $seek	= isset( $fields['tags'] ) ? 'tags' : 'content';
	            $tmp	= array();
	            foreach( $fields as $k => $field )
	            {
	                if( $k == $seek )
	                {
	                    $tmp['prefix']	= $prefixInput;
	                }
	
	                $tmp[ $k ]	= $field;
	            }
	
	            $fields = $tmp;
	        }
	
        /**
         * Update tag settings
         */
	        if ( isset( $fields['tags'] ) ) {
	            $fields['tags'] = self::updateTagFieldSettings( $item, $container, $fields['tags'] );
	
	            if ( $fields['tags'] === NULL ) {
	                unset( $fields['tags'] );
	            }
	        }
			
			return $fields;
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
	 * Process create/edit form
	 *
	 * @param	array				$values	Values from form
	 * @return	void
	 */
	public function processForm( $values )
	{
		try
		{
			call_user_func_array( 'parent::processForm', func_get_args() );
			
			if ( $this instanceof \IPS\Content\Tags and static::canPrefix( NULL, $this->container() ) and isset( $values[ static::$formLangPrefix . 'prefix' ] ) and !empty( $values[ static::$formLangPrefix . 'prefix' ] ) )
			{
			// NOTE: This is a behavior change (normally doesn't save until after processForm in its entirety),
			// but we have to save here or else changes will be overwritten during setLastComment().
				$this->save();
				
				$tags			= $this->tags();
				
				$prefixChanged	= ( !isset( $tags['prefix'] ) || $tags['prefix'] != $values[ static::$formLangPrefix . 'prefix' ] ) ? TRUE : FALSE;
				
			// Reverses are so the prefix is first on the list, to ensure it takes precedent over any identical tags. Added 3.1.7
				$tags			= array_reverse( $tags );
				$tags['prefix']	= $values[ static::$formLangPrefix . 'prefix' ];
				$tags			= array_reverse( $tags );
				
				$tags			= array_filter( array_unique( $tags ) );
				
				$this->setTags( $tags );
				
				if ( $prefixChanged === TRUE )
				{
				// On prefix edit, ensure forum last_prefix gets updated.
					$this->container()->setLastComment();
				}
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
     * Generate the tags form element
     *
     * @note	It is up to the calling code to verify the tag input field should be shown
     * @param	\IPS\Content\Item|NULL	$item		Item, if editing
     * @param	\IPS\Node\Model|NULL	$container	Container
     * @return	\IPS\Helpers\Form\Text|NULL
     */
    public static function tagsFormField( $item, $container )
    {
	try
	{
        /**
         * Short-circuit if tags are not available here
         */
	        if ( !in_array( 'IPS\Content\Tags', class_implements( get_called_class() ) ) or !static::canTag( NULL, $container ) )
	        {
	            return NULL;
	        }
	
        /**
         * Impossible situation here... tagsFormField() returns a single form field (tag input) to
         * \IPS\Content\Controller::editTags() for the tag quick-edit. There is no mechanism for us to add a separate
         * prefix field to that form (with simultaneous access to $item and $container) short of replacing that entire
         * method, which we cannot do for stability and license reasons.
         *
         * What does that mean?
         * If we disable prefix setting via the standard form, we lose any prefix on save.
         * If we do not, we lose prefix permissions and separation.
         * ...I'm leaving them enabled.
         */
	
	        return self::updateTagFieldSettings(
	            $item,
	            $container,
	            parent::tagsFormField( $item, $container )
	        );
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
     * Update tag field with context-dependent settings.
     *
     * @param	\IPS\Content\Item|NULL	$item		Item, if editing
     * @param	\IPS\Node\Model|NULL	$container	Container
     * @param	\IPS\Helpers\Form\Text	$field	    Tag field
     * @return	\IPS\Helpers\Form\Text|NULL
     */
    protected static function updateTagFieldSettings($item, $container, $field)
    {
	try
	{
	        if ( ($field instanceof \IPS\Helpers\Form\Text) === FALSE ) {
	            return $field;
	        }
	
        /**
         * Set default tags, if necessary.
         */
	        if ( is_null( $item ) and $container->default_tags !== NULL and $field->value == array() )
	        {
	            $tags = array_filter( explode( ',', $container->default_tags ) );
	
	            $field->defaultValue	= $tags;
	            $field->value			= $tags;
	        }
	
        /**
         * Update tag settings
         */
	
        /**
         * Overwrite global tag mode
         */
	        if ( $container->tag_mode !== NULL and $container->tag_mode !== 'inherit' )
	        {
	            if( $container->tag_mode === 'open' )
	            {
	                $field->options['autocomplete']['freeChoice'] = TRUE;
	            }
	            elseif( $container->tag_mode === 'closed' )
	            {
	                $field->options['autocomplete']['freeChoice'] = FALSE;
	            }
	            elseif( $container->tag_mode === 'prefix' )
	            {
                // If prefix-only mode, remove the tag field entirely.
	                return NULL;
	            }
	        }
	
        /**
         * Overwrite global minimum
         */
	        if ( $container->tag_min > 0 )
	        {
	            $field->options['autocomplete']['minItems'] = $container->tag_min;
	        }
	        else if ( $container->tag_min == 0 )
	        {
	            unset( $field->options['autocomplete']['minItems'] );
	        }
	
        /**
         * Overwrite global maximum
         */
	        if ( $container->tag_max > 0 )
	        {
	            $field->options['autocomplete']['maxItems'] = $container->tag_max;
	
            // Constrain to no less than minimum
	            if ( isset( $field->options['autocomplete']['minItems'] ) and $field->options['autocomplete']['minItems'] > $field->options['autocomplete']['maxItems'] )
	            {
	                $field->options['autocomplete']['maxItems'] = $field->options['autocomplete']['minItems'];
	            }
	        }
	        else if ( $container->tag_max == 0 )
	        {
	            unset( $field->options['autocomplete']['maxItems'] );
	        }
	
        /**
         * Recalculate description in case min/max changed...
         */
	        if ( \IPS\Settings::i()->tags_open_system or $container->tag_mode === 'open' )
	        {
	            $extralang = array();
	
	            if ( isset( $field->options['autocomplete']['minItems'], $field->options['autocomplete']['maxItems'] ) )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_min_max', FALSE, array( 'sprintf' => array( $field->options['autocomplete']['maxItems'] ), 'pluralize' => array( $field->options['autocomplete']['minItems'] ) ) );
	            }
	            else if( isset( $field->options['autocomplete']['minItems'] ) )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_min', FALSE, array( 'pluralize' => array( $field->options['autocomplete']['minItems'] ) ) );
	            }
	            else if( isset( $field->options['autocomplete']['maxItems'] ) )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_max', FALSE, array( 'pluralize' => array( $field->options['autocomplete']['maxItems'] ) ) );
	            }
	
	            if( \IPS\Settings::i()->tags_len_min && \IPS\Settings::i()->tags_len_max )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_len_min_max', FALSE, array( 'sprintf' => array( \IPS\Settings::i()->tags_len_min, \IPS\Settings::i()->tags_len_max ) ) );
	            }
	            else if( \IPS\Settings::i()->tags_len_min )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_len_min', FALSE, array( 'pluralize' => array( \IPS\Settings::i()->tags_len_min ) ) );
	            }
	            else if( \IPS\Settings::i()->tags_len_max )
	            {
	                $extralang[] = \IPS\Member::loggedIn()->language()->addToStack( 'tags_desc_len_max', FALSE, array( 'sprintf' => array( \IPS\Settings::i()->tags_len_max ) ) );
	            }
	
	            $field->options['autocomplete']['desc'] = \IPS\Member::loggedIn()->language()->addToStack('tags_desc') . ( ( count( $extralang ) ) ? '<br>' . implode( $extralang, ' ' ) : '' );
	        }
	
	        return $field;
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
