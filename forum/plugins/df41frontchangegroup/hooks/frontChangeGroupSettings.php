//<?php

class hook26 extends _HOOK_CLASS_
{


	/**
	 * Add/Edit Group
	 *
	 * @return	void
	 */
	public function form()
	{
		try
		{
			/* Load group */
			try
			{
				$group = \IPS\Member\Group::load( \IPS\Request::i()->id );
				\IPS\Dispatcher::i()->checkAcpPermission( 'groups_edit' );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Dispatcher::i()->checkAcpPermission( 'groups_add' );
				$group = new \IPS\Member\Group;
			}
			/* Get extensions */
			$extensions = \IPS\Application::allExtensions( 'core', 'GroupForm', FALSE, 'core', 'GroupSettings', TRUE );
			/* Build form */
			$form = new \IPS\Helpers\Form( ( !$group->g_id ? 'groups_add' : $group->g_id ) );
			foreach ( $extensions as $k => $class )
			{
				$form->addTab( 'group__' . $k );
				$class->process( $form, $group );
			}
			
			/* Change Group Srttings*/
			$gID = intval( \IPS\Request::i()->id );
			
			if ( $gID != \IPS\Settings::i()->guest_group )
			{
				$add_userpane_link = array();
				$alow_change_group = array();
				$alowed_change_groups = array();
				$alow_secondary_groups = array();
				$alowed_secondary_groups = array();
				$groups = \IPS\Member\Group::groups( FALSE, TRUE );
				$options = array(
									0 => \IPS\Member::loggedIn()->language()->get( 'no' ),
									1 => \IPS\Member::loggedIn()->language()->get( 'yes' ),
									2 => \IPS\Member::loggedIn()->language()->get( 'only_secondary_groups' )
								);
				
				if ( !empty( \IPS\Settings::i()->add_userpane_link ) )
					$add_userpane_link = json_decode( \IPS\Settings::i()->add_userpane_link, true );
				
				if ( !empty( \IPS\Settings::i()->alow_change_group ) )
					$alow_change_group = json_decode( \IPS\Settings::i()->alow_change_group, true );
				
				if ( !empty( \IPS\Settings::i()->alowed_change_groups ) )
					$alowed_change_groups = json_decode( \IPS\Settings::i()->alowed_change_groups, true );
	
				if ( !empty( \IPS\Settings::i()->alow_secondary_groups ) )
					$alow_secondary_groups = json_decode( \IPS\Settings::i()->alow_secondary_groups, true );
				
				if ( !empty( \IPS\Settings::i()->alowed_secondary_groups ) )
					$alowed_secondary_groups = json_decode( \IPS\Settings::i()->alowed_secondary_groups, true );
	
				$form->addTab( 'front_change_group', $group );
				$form->add( new \IPS\Helpers\Form\YesNo( 'alow_change_group', isset( $alow_change_group[$gID] ) ? $alow_change_group[$gID] : FALSE, FALSE ) );
				$form->add( new \IPS\Helpers\Form\Select( 'alowed_change_groups', isset( $alowed_change_groups[$gID] ) ? explode( '|', $alowed_change_groups[$gID] ) : array(), FALSE, array( 'options' => $groups, 'multiple' => TRUE, 'parse' => 'normal' ) ) );
				$form->add( new \IPS\Helpers\Form\Select( 'alow_secondary_groups', isset( $alow_secondary_groups[$gID] ) ? intval( $alow_secondary_groups[$gID] ) : 0, FALSE, array( 'options' => $options, 'parse' => 'normal' ) ) );
				$form->add( new \IPS\Helpers\Form\Select( 'alowed_secondary_groups', isset( $alowed_secondary_groups[$gID] ) ? explode( '|', $alowed_secondary_groups[$gID] ) : array(), FALSE, array( 'options' => $groups, 'multiple' => TRUE, 'parse' => 'normal' ) ) );
				
				if ( !$group->g_access_cp )
					$form->add( new \IPS\Helpers\Form\YesNo( 'add_userpane_link', isset( $add_userpane_link[$gID] ) ? $add_userpane_link[$gID] : FALSE, FALSE ) );
			}
			
			/* Handle submissions */
			if ( $values = $form->values() )
			{
				if ( $gID != \IPS\Settings::i()->guest_group )
				{
					if ( !isset( $alow_change_group[$gID] ) || $values['alow_change_group'] != $alow_change_group[$gID] )
					{
						$alow_change_group[$gID] = $values['alow_change_group'];
						$form->saveAsSettings( array( 'alow_change_group' => json_encode( $alow_change_group ) ) );
					}
					
					if ( !isset( $alowed_change_groups[$gID] ) || implode( '|', $values['alowed_change_groups'] ) != $alowed_change_groups[$gID] )
					{
						$alowed_change_groups[$gID] = implode( '|', $values['alowed_change_groups'] );
						$form->saveAsSettings( array( 'alowed_change_groups' => json_encode( $alowed_change_groups ) ) );
					}
					
					if ( !isset( $alow_secondary_groups[$gID] ) || $values['alow_secondary_groups'] != $alow_secondary_groups[$gID] )
					{
						$alow_secondary_groups[$gID] = $values['alow_secondary_groups'];
						$form->saveAsSettings( array( 'alow_secondary_groups' => json_encode( $alow_secondary_groups ) ) );
					}
					
					if ( !isset( $alowed_secondary_groups[$gID] ) || implode( '|', $values['alowed_secondary_groups'] ) != $alowed_secondary_groups[$gID] )
					{
						$alowed_secondary_groups[$gID] = implode( '|', $values['alowed_secondary_groups'] );
						$form->saveAsSettings( array( 'alowed_secondary_groups' => json_encode( $alowed_secondary_groups ) ) );
					}
					
					if ( !$group->g_access_cp && ( !isset( $add_userpane_link[$gID] ) || $values['add_userpane_link'] != $add_userpane_link[$gID] ) )
					{
						$add_userpane_link[$gID] = $values['add_userpane_link'];
						$form->saveAsSettings( array( 'add_userpane_link' => json_encode( $add_userpane_link ) ) );
					}
				}
				
				/* Create a group if we don't have one already - we have to save it so we have an ID for our translatables */
				if ( !$group->g_id )
				{
					$group->save();
				}
				
				/* Process each extension */
				foreach ( $extensions as $class )
				{
					$class->save( $values, $group );
				}
				
				/* And save */
				$group->save();
	
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=core&module=members&controller=groups' ), 'saved' );
			}
			
			/* Display */
			\IPS\Output::i()->title	 		= ( $group->g_id ? $group->name : \IPS\Member::loggedIn()->language()->addToStack('groups_add') );
			\IPS\Output::i()->breadcrumb[]	= array( NULL, \IPS\Output::i()->title );
			\IPS\Output::i()->output 		= \IPS\Theme::i()->getTemplate( 'global' )->block( \IPS\Output::i()->title, $form );
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