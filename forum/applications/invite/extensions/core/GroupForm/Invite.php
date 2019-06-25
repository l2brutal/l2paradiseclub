<?php
/**
 * @brief		Admin CP Group Form
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	Invite System
 * @since		30 Aug 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Group Form
 */
class _Invite
{
	/**
	 * Process Form
	 *
	 * @param	\IPS\Form\Tabbed		$form	The form
	 * @param	\IPS\Member\Group		$group	Existing Group
	 * @return	void
	 */
	public function process( &$form, $group )
	{
		if( $group->g_id != \IPS\Settings::i()->guest_group )
		{
			$form->add( new \IPS\Helpers\Form\YesNo( 'is_canvite', $group->g_id ? $group->is_canvite : 0, FALSE, array( 'togglesOn' => array( 'is_unlimited', 'is_inviteexpire' ) ) ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'is_unlimited', $group->g_id ? $group->is_unlimited : 0, FALSE, array( 'togglesOff' => array( 'is_nrinvites' ) ), NULL, NULL, NULL, 'is_unlimited' ) );
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
		if( $group->g_id != \IPS\Settings::i()->guest_group )
		{
			$group->is_canvite 		= intval( $values['is_canvite'] );
			$group->is_unlimited 	= intval( $values['is_unlimited'] );

			/* Clear create menu caches */
			\IPS\Member::clearCreateMenu();
		}
	}
}