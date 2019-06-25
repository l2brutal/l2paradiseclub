//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook38 extends _HOOK_CLASS_
{
	/**
	 * Edit Club Form
	 *
	 * @param	bool	$acp			TRUE if editing in the ACP
	 * @param	bool	$new			TRUE if creating new
	 * @param	array	$availableTypes	If creating new, the available types
	 * @return	\IPS\Helpers\Form|NULL
	 */
	public function form( $acp=FALSE, $new=FALSE, $availableTypes=NULL )
	{
		try
		{
			$form = call_user_func_array( 'parent::form', func_get_args() );
		
			if ( $form instanceof \IPS\Helpers\Form AND !\IPS\Settings::i()->clubs_require_approval )
			{
				if( \IPS\Request::i()->do == 'create' )
				{
					$availableClubNodes = array();
					foreach ( \IPS\Member\Club::availableNodeTypes() as $class )
					{
						if( \IPS\Member::loggedIn()->group['g_club_allowed_nodes'] === '*' or in_array( $class, explode( ',', \IPS\Member::loggedIn()->group['g_club_allowed_nodes'] ) ) )
						{
							$availableClubNodes[ $class ] = $class::clubAcpTitle();
						}
					}
					$form->add( new \IPS\Helpers\Form\CheckboxSet( 'club_new_features', array_keys( $availableClubNodes ), TRUE, array( 'options' => $availableClubNodes ), NULL, NULL, NULL, 'club_new_features' ) );
				}
			}
	
			return $form;
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

	public function save()
	{
		try
		{
			if( $this->_new AND !\IPS\Settings::i()->clubs_require_approval )
			{
				parent::save();
	
				if( is_array( \IPS\Request::i()->club_new_features ) AND count( \IPS\Request::i()->club_new_features ) )
				{
					foreach( \IPS\Request::i()->club_new_features as $node => $feature )
					{
						if( $feature == 1 )
						{
							$itemClass = $node::$contentItemClass;
		
							$class = '\\' . $node;
							$clubFeature = new $class;
		
							$form = new \IPS\Helpers\Form( uniqid() );
							$clubFeature->clubForm( $form );
		
							$values = static::clubsEnhancementsGetFormValues( $form );
							
							array_walk( $values, function( &$item )
							{
								\IPS\Member::loggedIn()->language()->parseOutputForDisplay( $item );
							});
		
							$clubFeature->saveClubForm( $this, $values );
						}
					}
				}
			}
			else
			{
				parent::save();
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
	 * Hacky way of getting a form's default values
	 * This will be used in other hook files.
	 *
	 * @param   $form 	\IPS\Helpers\Form
	 * @return  array 	Form values
	 */
	public static function clubsEnhancementsGetFormValues( \IPS\Helpers\Form $form )
	{
		try
		{
			$values = array();
		
			/* Loop elements */
			foreach ( $form->elements as $elements )
			{
				foreach ( $elements as $_name => $element )
				{
					/* If it's a matrix, populate the values from it */
					if ( ( $element instanceof \IPS\Helpers\Form\Matrix ) )
					{
						$values[ $_name ] = $element->values( TRUE );
						continue;
					}
					
					/* If it's not a form element, skip */
					if ( !( $element instanceof \iPS\Helpers\Form\FormAbstract ) )
					{
						continue;
					}
										
					/* Make sure we have a value (someone might try to be sneaky and remove the HTML from the form before submitting) */
					if ( !$element->valueSet )
					{
						$element->setValue( FALSE, TRUE );
					}
					
					/* Still here? Then add the value */
					$values[ $element->name ] = $element->defaultValue;
				}
			}
	
			return $values;
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
	 * Get Node names and URLs
	 *
	 * @return	array
	 */
	public function nodes()
	{
		try
		{
			$return = array();
	
			foreach ( \IPS\Db::i()->select( '*', 'core_clubs_node_map', array( 'club_id=? AND node_enabled=?', $this->id, 1 ), 'node_position ASC, id ASC' ) as $row )
			{
				$class = $row['node_class'];
	
				$return[ $row['id'] ] = array(
					'name'			=> $row['name'],
					'url'			=> \IPS\Http\Url::internal( $class::$urlBase . $row['node_id'], 'front', $class::$urlTemplate, array( \IPS\Http\Url\Friendly::seoTitle( $row['name'] ) ) ),
					'node_class'	=> $row['node_class'],
					'node_id'		=> $row['node_id'],
				);
			}
	
			return $return;
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
	 * Get number clubs a member is leader of
	 *
	 * @param	\IPS\Member	$member	The member
	 * @return	int
	 */
	public static function numberOfClubsMemberIsMember( \IPS\Member $member )
	{
		try
		{
			return \IPS\Db::i()->select( 'COUNT(*)', 'core_clubs_memberships', array( 'member_id=? AND status=?', $member->member_id, static::STATUS_MEMBER ) )->first();
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