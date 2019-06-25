<?php
/**
 * @brief		Admin CP Group Form
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	PM Viewer
 * @since		08 Oct 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\pmviewer\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Group Form
 */
class _PMViewer
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
			$form->add( new \IPS\Helpers\Form\YesNo( 'g_pmviewer_protectedgroup', $group->g_id ? $group->g_pmviewer_protectedgroup : 0, FALSE ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'g_pmviewer_viewhidden', $group->g_id ? $group->g_pmviewer_viewhidden : 0, FALSE ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'g_pmviewer_hideunhide', $group->g_id ? $group->g_pmviewer_hideunhide : 0, FALSE ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'g_pmviewer_managemembers', $group->g_id ? $group->g_pmviewer_managemembers : 0, FALSE ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'g_pmviewer_editposts', $group->g_id ? $group->g_pmviewer_editposts : 0, FALSE ) );
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
			$group->g_pmviewer_protectedgroup 	= intval( $values['g_pmviewer_protectedgroup'] );
			$group->g_pmviewer_viewhidden 		= intval( $values['g_pmviewer_viewhidden'] );
			$group->g_pmviewer_hideunhide 		= intval( $values['g_pmviewer_hideunhide'] );
			$group->g_pmviewer_managemembers 	= intval( $values['g_pmviewer_managemembers'] );
			$group->g_pmviewer_editposts 		= intval( $values['g_pmviewer_editposts'] );
		}
	}
}