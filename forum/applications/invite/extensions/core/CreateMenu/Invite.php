<?php
/**
 * @brief		Create Menu Extension : Invite
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	Invite System
 * @since		03 Sep 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\extensions\core\CreateMenu;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Create Menu Extension: Invite
 */
class _Invite
{
	/**
	 * Get Items
	 *
	 * @return	array
	 */
	public function getItems()
	{
		$array = array();

		if( \IPS\Settings::i()->is_on AND \IPS\Settings::i()->is_showmoremenu AND \IPS\Member::loggedIn()->group['is_canvite'] AND ( \IPS\Member::loggedIn()->invites_remaining > 0 OR \IPS\Member::loggedIn()->group['is_unlimited'] ) AND !\IPS\Member::loggedIn()->invite_revoke_access )
		{
			$array = array(
				'invitation_create_new' => array(
				'link' 			=> \IPS\Http\Url::internal( "app=invite&module=invite&controller=invite&_new=1", 'front', 'invite_submit' ),
				'extraData'		=> array( 'data-ipsDialog' => true ),
				'title' 		=> 'send_invitation'
				)
			);
		}

		return $array;
	}
}