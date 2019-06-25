//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

class hook67 extends _HOOK_CLASS_
{
	/**
	 * Process Form
	 *
	 * @param	\IPS\Helpers\Form		$form	The form
	 * @param	\IPS\Member\Group		$group	Existing Group
	 * @return	void
	 */
	public function process( &$form, $group )
	{
		try
		{
			parent::process( $form, $group );
			
		// Guest group?
			if( $group->g_id != \IPS\Settings::i()->guest_group )
			{
			// Add new tab
				$form->addTab( 'tb_but_tab_title' );
				
			// Settings
				$form->add( new \IPS\Helpers\Form\YesNo( 'tb_but_use', $group->tb_but_use, FALSE, array( 'togglesOn' => array( "{$form->id}_tb_but_forums", "{$form->id}_tb_but_bumpall", "{$form->id}_tb_but_day_limit", "{$form->id}_tb_but_time_limit", "{$form->id}_tb_but_last_limit" ) ) ) );
				$form->add( new \IPS\Helpers\Form\Node( 'tb_but_forums', ( !$group->tb_but_forums or $group->tb_but_forums === '*' ) ? 0 : explode( ',', $group->tb_but_forums ), FALSE, array( 'class' => 'IPS\forums\Forum', 'zeroVal' => 'all', 'multiple' => TRUE ) ) );
				$form->add( new \IPS\Helpers\Form\YesNo( 'tb_but_bumpall', $group->tb_but_bumpall ) );
				$form->add( new \IPS\Helpers\Form\Number( 'tb_but_day_limit', $group->tb_but_day_limit, FALSE, array( 'unlimited' => 0 ) ) );
				$form->add( new \IPS\Helpers\Form\Number( 'tb_but_time_limit', $group->tb_but_time_limit, FALSE, array( 'unlimited' => 0, 'endSuffix' => \IPS\Member::loggedIn()->language()->addToStack( 'tb_but_quick_values' ) ) ) );
				$form->add( new \IPS\Helpers\Form\Number( 'tb_but_last_limit', $group->tb_but_last_limit, FALSE, array( 'unlimited' => 0, 'endSuffix' => \IPS\Member::loggedIn()->language()->addToStack( 'tb_but_quick_values' ) ) ) );
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
	 * Save
	 *
	 * @param	array				$values	Values from form
	 * @param	\IPS\Member\Group	$group	The group
	 * @return	void
	 */
	public function save( $values, &$group )
	{
		try
		{
			parent::save( $values, $group );
			
		// Guest group?
			if( $group->g_id != \IPS\Settings::i()->guest_group )
			{
				$group->tb_but_use			= $values['tb_but_use'];
				$group->tb_but_forums		= $values['tb_but_forums'] ? implode( ',', array_keys( $values['tb_but_forums'] ) ) : '*';
				$group->tb_but_bumpall		= $values['tb_but_bumpall'];
				$group->tb_but_day_limit	= $values['tb_but_day_limit'];
				$group->tb_but_time_limit	= $values['tb_but_time_limit'];
				$group->tb_but_last_limit	= $values['tb_but_last_limit'];
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
}