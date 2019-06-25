//<?php

class hook31 extends _HOOK_CLASS_
{
	public function form( &$form )
    {
	try
	{
	    	$data = parent::form( $form );
	
			if ( !$this->sub_can_post OR $this->redirect_url )
			{
				return $data;
			}
	
	     	$form->addTab( 'auto_reply' );
			$form->addHeader( 'auto_reply_settings' );
	
			$form->add( new \IPS\Helpers\Form\YesNo( 'autoreply_onoff', $this->autoreply_onoff ? 1 : 0, FALSE, array( 'togglesOn' => array( 'autoreply_postcount', 'autoreply_closetopic', 'autoreply_authorid', 'autoreply_text' ) ) ) );
	
			$form->add( new \IPS\Helpers\Form\YesNo( 'autoreply_postcount', $this->autoreply_postcount ? 1 : 0, FALSE, array(), NULL, NULL, NULL, 'autoreply_postcount' ) );
	
			$form->add( new \IPS\Helpers\Form\YesNo( 'autoreply_closetopic', $this->autoreply_closetopic ? 1 : 0, FALSE, array(), NULL, NULL, NULL, 'autoreply_closetopic' ) );
	
			$form->add( new \IPS\Helpers\Form\Member( 'autoreply_authorid', $this->autoreply_authorid ? \IPS\Member::load( $this->autoreply_authorid ) : NULL, FALSE, array(), function( $member ) use ( $form )
			{
				if( !is_object( $member ) or !$member->member_id )
				{
					throw new \InvalidArgumentException( 'autoreply_authorid' );
				}
			},
			NULL, NULL, 'autoreply_authorid' ) );
	
			$form->add( new \IPS\Helpers\Form\Translatable( 'autoreply_text', $this->autoreply_text, FALSE, array( 'app' => 'core', 'key' => 'Admin', 'editor' => array( 'app' => 'core', 'key' => 'Admin', 'autoSaveKey' => 'autoreply_text', 'attachIds' => array( NULL, NULL, 'post_lock_post_content' ), 'minimize' => 'autoreply_text_placeholder' ) ), NULL, NULL, NULL, 'autoreply_text' ) );
	
			return $data;
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
	 * [Node] Format form values from add/edit form for save
	 *
	 * @param	array	$values	Values from the form
	 * @return	array
	 */
	public function formatFormValues( $values )
	{
		try
		{
			if ( isset( $values['autoreply_authorid'] ) AND $values['autoreply_authorid'] != '' )
			{
				$values['autoreply_authorid'] = $values['autoreply_authorid']->member_id;
			}
	
			return parent::formatFormValues( $values );
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