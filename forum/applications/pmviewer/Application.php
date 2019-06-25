<?php
/**
 * @brief		PM Viewer Application Class
 * @author		<a href='http://www.sosinvision.com.br'>Adriano Fariar</a>
 * @copyright	(c) 2015 Adriano Fariar
 * @package		IPS Social Suite
 * @subpackage	PM Viewer
 * @since		16 Sep 2015
 * @version		
 */
 
namespace IPS\pmviewer;

/**
 * PM Viewer Application Class
 */
class _Application extends \IPS\Application
{
	/**
	 * [Node] Get Node Icon
	 *
	 * @return	string
	 */
	protected function get__icon()
	{
		return 'envelope';
	}

	public function installOther()
	{
		/* Full power to Admins */
		foreach( \IPS\Member\Group::groups( TRUE, FALSE ) as $group )
		{
			if( $group->g_id == \IPS\Settings::i()->admin_group )
			{
				$group->g_pmviewer_viewhidden 		= TRUE;
				$group->g_pmviewer_hideunhide 		= TRUE;
				$group->g_pmviewer_managemembers	= TRUE;
				$group->g_pmviewer_editposts		= TRUE;
				$group->save();
			}
		}
	}
}