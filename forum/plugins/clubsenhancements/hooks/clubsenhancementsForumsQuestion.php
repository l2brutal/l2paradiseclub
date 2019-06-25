//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook36 extends _HOOK_CLASS_
{
	/* !Clubs */
	
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
		
			if( !isset( \IPS\Request::i()->node ) )
			{
				$form->add( new \IPS\Helpers\Form\Radio( 'forum_type', NULL, TRUE, array(
					'options' => array(
						'normal' 	=> 'forum_type_normal',
						'qa' 		=> 'forum_type_qa'
					) )
				) );
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
	 * Class-specific routine when saving club form
	 *
	 * @param	\IPS\Member\Club	$club	The club
	 * @param	array				$values	Values
	 * @return	void
	 */
	public function _saveClubForm( \IPS\Member\Club $club, $values )
	{
		try
		{
			if( $this->_new AND $values['forum_type'] == 'qa' )
			{
				$this->forums_bitoptions['bw_enable_answers'] = TRUE;
			}
	
			parent::_saveClubForm( $club, $values );
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