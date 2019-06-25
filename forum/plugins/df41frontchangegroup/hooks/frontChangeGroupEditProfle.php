//<?php

class hook25 extends _HOOK_CLASS_
{


	/**
	 * Change Group
	 *
	 * @return	void
	 */
	protected function change()
	{
		try
		{
			/* Do we have permission? */
			$gID = \IPS\Member::loggedIn()->member_group_id;
			$ids = array();
			$check = false;
			
			if ( !empty( \IPS\Settings::i()->alowed_change_groups ) )
			{
				$alowed_change_groups = json_decode( \IPS\Settings::i()->alowed_change_groups, true );
				$ids = explode( '|', $alowed_change_groups[ $gID ] );
			}	
			
			foreach ( $ids as $_id )
			{
				if ( intval( $_id ) === $this->member->member_group_id )
					$check = true;
			}
			
			if ( !$check and ( \IPS\Member::loggedIn()->member_id !== $this->member->member_id or !$this->member->group['g_edit_profile'] ) )
			{
				\IPS\Output::i()->error( 'no_permission_edit_profile', '2S147/1', 403, '' );
			}
			
			/* Build the form */
			$form = new \IPS\Helpers\Form;
			
			/* Profile fields */
			try
			{
				$values = \IPS\Db::i()->select( '*', 'core_pfields_content', array( 'member_id=?', $this->member->member_id ) )->first();
			}
			catch( \UnderflowException $e )
			{
				$values	= array();
			}
			
			/* Change Group */
			$groups = array();
				
			if ( !empty( \IPS\Settings::i()->alow_change_group ) )
				$alow_change_group = json_decode( \IPS\Settings::i()->alow_change_group, true );
			
			if ( isset( $alow_change_group[ $gID ] ) && $alow_change_group[ $gID ] == true && !$this->member->group['g_access_cp'] )
			{
				if ( isset( $alowed_change_groups[ $gID ] ) && !empty( $alowed_change_groups[ $gID ] ) )
				{
					foreach( $ids as $_id )
					{
						$groups[$_id] = \IPS\Member\Group::load( $_id );
						
					}
				}
				
				if ( !empty( \IPS\Settings::i()->alow_secondary_groups ) )
					$alow_secondary_groups = json_decode( \IPS\Settings::i()->alow_secondary_groups, true );
				
				$form->addTab( 'front_change_group', 'user');
				
				if ( !empty( $groups ) && intval( $alow_secondary_groups[ \IPS\Member::loggedIn()->group['g_id'] ] ) != 2 )
					$form->add( new \IPS\Helpers\Form\Select( 'group', $this->member->member_group_id, FALSE, array( 'options' => $groups, 'parse' => 'normal' ) ) );
				
				if ( isset( $alow_secondary_groups[ $gID ] ) && intval( $alow_secondary_groups[ $gID ] ) != 0 )
				{
					$s_groups  = array();
					$s_ids = array();
					
					if ( !empty( \IPS\Settings::i()->alowed_secondary_groups ) )
						$alowed_secondary_groups = json_decode( \IPS\Settings::i()->alowed_secondary_groups, true );
					
					if ( isset( $alowed_secondary_groups[ $gID ] ) )
						$s_ids = explode( '|', $alowed_secondary_groups[ $gID ] );
					
					foreach( $s_ids as $_sid )
					{
						$s_groups[$_sid] = \IPS\Member\Group::load( $_sid );
						
					}
					
					$form->add( new \IPS\Helpers\Form\Select( 'secondary_groups', $this->member->mgroup_others ? explode( ',', $this->member->mgroup_others ) : array(), FALSE, array( 'options' => $s_groups, 'multiple' => TRUE, 'parse' => 'normal' ) ) );
				}
			}
			
			/* Handle the submission */
			if ( $values = $form->values() )
			{
				if( isset( $values['group'] ) && $values['group'] != $this->member->member_group_id && in_array( $values['group'], $ids ) )
				{
					$this->member->member_group_id = $values['group'];
				}
	
				if ( isset( $values['secondary_groups'] ) && implode( ',', $values['secondary_groups'] ) != $this->member->mgroup_others )
				{
					$this->member->mgroup_others = implode( ',', $values['secondary_groups'] );
				}
				
				/* Save */
				$this->member->save();
	
				\IPS\Output::i()->redirect( $this->member->url() );
			}
			
			/* Set Session Location */
			\IPS\Session::i()->setLocation( $this->member->url(), array(), 'loc_editing_profile', array( $this->member->name => FALSE ) );
			
			if ( \IPS\Request::i()->isAjax() )
			{
				\IPS\Output::i()->output = $form->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'forms', 'core' ) ), 'popupTemplate' ) );
			}
			else
			{
				\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'forms', 'core' )->editContentForm( \IPS\Member::loggedIn()->language()->addToStack( 'f_change_group' ), $form );
			}
			
			\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'f_change_group', FALSE, array( 'sprintf' => array( $this->member->name ) ) );
			\IPS\Output::i()->breadcrumb[] = array( NULL, \IPS\Member::loggedIn()->language()->addToStack( 'f_change_group', FALSE, array( 'sprintf' => array( $this->member->name ) ) ) );
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