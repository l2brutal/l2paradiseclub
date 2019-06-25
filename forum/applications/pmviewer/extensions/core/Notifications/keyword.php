<?php
/**
 * @brief		Notification Options
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	PM Viewer
 * @since		06 Oct 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\pmviewer\extensions\core\Notifications;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Notification Options
 */
class _keyword
{
	/**
	 * Get configuration
	 *
	 * @param	\IPS\Member	$member	The member
	 * @return	array
	 */
	public function getConfiguration( $member )
	{
		$return = array();

		if ( $member === NULL or ( \IPS\Settings::i()->pmviewer_monitoring_enable AND \IPS\Member::loggedIn()->inGroup( explode( ',', \IPS\Settings::i()->pmviewer_monitoring_groups ) ) ) )
		{
			return array(
				'keyword_used'	=> array( 'default' => array( 'inline' ), 'disabled' => array(), 'icon' => 'lock' )
			);
		}
	}
	
	// For each type of notification you need a method like this which controls what will be displayed when the user clicks on the notification icon in the header:
	// Note that for each type of notification you must *also* create email templates. See documentation for details: https://remoteservices.invisionpower.com/docs/devdocs-notifications
	
	/**
	 * Parse notification: key
	 *
	 * @param	\IPS\Notification\Inline	$notification	The notification
	 * @return	array
	 */
	public function parse_keyword_used( $notification )
	{
		$item = $notification->item;

		return array(
				'title'		=> \IPS\Member::loggedIn()->language()->addToStack( 'notification__keyword_used', FALSE, array( 'sprintf' => array( $item->author()->name ) ) ),
				'url'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=view&id=' . $item->id, 'admin' ),
				'content'	=> $item->content(),
				'author'	=> \IPS\Member::load( $item->author()->member_id ),
		);
	}
}