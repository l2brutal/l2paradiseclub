//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook43 extends _HOOK_CLASS_
{
	/**
	 * Remove All Followers
	 *
	 * @return	array
	 */
	public function convertToClub()
	{
		try
		{
			if ( !\IPS\Request::i()->id OR !intval( \IPS\Request::i()->id ) )
			{
				\IPS\Output::i()->error( 'node_error', 'Clubs Enhancements/9', 403, '' );
			}
	
			$id = \IPS\Request::i()->id;
	
			$nodeClass		= static::getNodeClass();
			$area			= $nodeClass::$permType;
			$app			= \IPS\Dispatcher::i()->application->directory;
			$module			= \IPS\Dispatcher::i()->module->key;
			$controller		= \IPS\Dispatcher::i()->controller;
	
			/* Do not ALLOW to convert if it has subcategories */
			$thing = $nodeClass::load( $id );
			if( $thing->hasChildren() )
			{
				\IPS\Output::i()->error( 'clubsenhancementsConvertToFeatureError', 'Clubs Enhancements/10', 403, '' );
			}
	
			$clubOptions = array();
			/* CAN'T USE THIS BECAUSE THIS ONLY SHOWS CLUBS THAT YOU ARE MEMBER SOMEHOW (MEMBER, LEADER, MODERATOR) */
		/*foreach ( \IPS\Member\Club::clubs( \IPS\Member::loggedIn(), NULL, 'name', TRUE ) as $club )
		{
			$clubOptions[ $club->id ] = $club->name;
		}*/
	
			foreach( \IPS\Db::i()->select( '*', 'core_clubs', NULL, 'name ASC' ) as $club )
			{
				$clubOptions[ $club['id'] ] = $club['name'];
			}
	
			$form = new \IPS\Helpers\Form();
			$form->add( new \IPS\Helpers\Form\Select( 'clubsenhancementsConvertToClubFeatureClub', NULL, FALSE, array( 'options' => $clubOptions, 'parse' => 'normal', 'multiple' => FALSE, 'noDefault' => TRUE ) ) );
	
			if ( $values = $form->values() )
			{
				$club  			= \IPS\Member\Club::load( $values['clubsenhancementsConvertToClubFeatureClub'] );
				$clubFeature 	= $nodeClass::load( $id );
	
				$parentColumn = $clubFeature::$databaseColumnParent;
				if ( $parentColumn !== NULL )
				{
					$clubFeature->$parentColumn = -1;
	
					/* Need to set 'sub_can_post' back to 1 for forums, as setting 'parent_id' to -1 will set that to 0 */
					if ( $nodeClass == 'IPS\forums\Forum' )
					{
						$clubFeature->sub_can_post	= 1;
					}
				}
				
				$form = new \IPS\Helpers\Form( uniqid() );
				$clubFeature->clubForm( $form );
				
				$values = \IPS\Member\Club::clubsEnhancementsGetFormValues( $form ); /* Getting default form values */
									
				array_walk( $values, function( &$item )
				{
					\IPS\Member::loggedIn()->language()->parseOutputForDisplay( $item );
				});
				
				$clubFeature->_saveClubForm( $club, $values );
				
				$clubIdColumn = $clubFeature->clubIdColumn();
				$clubFeature->$clubIdColumn = $club->id;
	
				/* Remove moderators */
				if ( isset( $clubFeature::$modPerm ) )
				{
					foreach ( \IPS\Db::i()->select( '*', 'core_moderators' ) as $mod )
			 		{
			 			$canView = FALSE;
			 			if ( $mod['perms'] != '*' )
			 			{
			 				$perms = json_decode( $mod['perms'], TRUE );
	
			 				if ( isset( $perms[ $clubFeature::$modPerm ] ) AND is_array( $perms[ $clubFeature::$modPerm ] ) )
			 				{
								foreach ( $perms[ $clubFeature::$modPerm ] as $id )
								{
									if( $id == $clubFeature->_id )
									{
										unset( $perms[ $clubFeature::$modPerm ][ $id ] );
	
										\IPS\Db::i()->update( 'core_moderators', array( 'perms' => json_encode( $perms ) ), 'id=' . $mod['id'] );
										unset( \IPS\Data\Store::i()->moderators );
	
										break;
									}
								}
							}
			 			}
			 		}
			 	}
	
				$clubFeature->save();
	
				\IPS\Db::i()->insert( 'core_clubs_node_map', array(
					'club_id'		=> $club->id,
					'node_class'	=> $nodeClass,
					'node_id'		=> $clubFeature->_id,
					'name'			=> $values['club_node_name']
				) );
				
				\IPS\Lang::saveCustom( $app, $nodeClass::$titleLangPrefix . $clubFeature->_id, $values['club_node_name'] );
				\IPS\Lang::saveCustom( $app, $nodeClass::$titleLangPrefix . $clubFeature->_id . '_desc', isset( $values['club_node_description'] ) ? $values['club_node_description'] : '' );
	
				$clubFeature->setPermissionsToClub( $club );
				
				/* Boink */
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app='.$app.'&module='.$module.'&controller='.$controller ), 'completed' );
			}
		
			\IPS\Output::i()->output = $form;
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