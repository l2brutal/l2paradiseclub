<?php
/**
 * @brief		Admin CP Group Form
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	(BD4) Moods
 * @since		22 Dec 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\bdmoods\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Group Form
 */
class _GroupForm
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
		$form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_canSee', $group->g_bdm_canSee, TRUE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_canChange', $group->g_bdm_canChange, TRUE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_canUseCustom', $group->g_bdm_canUseCustom,TRUE ) );
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
		$group->g_bdm_cansee = $values['bd_moods_canSee'];
		$group->g_bdm_canchange = $values['bd_moods_canChange'];
		$group->g_bdm_canusecustom = $values['bd_moods_canUseCustom'];
	}
}
