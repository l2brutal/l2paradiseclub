<?php
/**
 * @brief		Invite System Application Class
 * @author		<a href='http://www.sosinvison.com.br'>Adirano Faria</a>
 * @copyright	(c) 2015 Adirano Faria
 * @package		IPS Social Suite
 * @subpackage	Invite System
 * @since		30 Aug 2015
 * @version		
 */
 
namespace IPS\invite;

/**
 * Invite System Application Class
 */
class _Application extends \IPS\Application
{
	/**
	 * [Node] Get Icon for tree
	 *
	 * @note	Return the class for the icon (e.g. 'globe')
	 * @return	string|null
	 */
	protected function get__icon()
	{
		return 'ticket';
	}

	public function installOther()
	{
		/* Full power to Admins */
		foreach( \IPS\Member\Group::groups( TRUE, FALSE ) as $group )
		{
			if( $group->g_id == \IPS\Settings::i()->admin_group )
			{
				$group->is_canvite 		= TRUE;
				$group->is_unlimited 	= TRUE;
				$group->save();
			}
		}
		
		/* Members Posts Offset */
		\IPS\Task::queue( 'invite', 'membersOffset', array(), 1 );
	}
}