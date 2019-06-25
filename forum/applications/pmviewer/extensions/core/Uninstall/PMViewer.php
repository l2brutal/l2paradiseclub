<?php
/**
 * @brief		Uninstall callback
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	PM Viewer
 * @since		04 Oct 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\pmviewer\extensions\core\Uninstall;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Uninstall callback
 */
class _PMViewer
{
	/**
	 * Code to execute before the application has been uninstalled
	 *
	 * @param	string	$application	Application directory
	 * @return	array
	 */
	public function preUninstall( $application )
	{
	}

	/**
	 * Code to execute after the application has been uninstalled
	 *
	 * @param	string	$application	Application directory
	 * @return	array
	 */
	public function postUninstall( $application )
	{
		try
		{
			//\IPS\Db::i()->dropColumn( 'core_message_topics',  'mt_is_hidden' );
			\IPS\Db::i()->dropColumn( 'core_message_posts',  'msg_edited_pmviewer' );
			\IPS\Db::i()->dropColumn( 'core_groups', array( 'g_pmviewer_protectedgroup', 'g_pmviewer_viewhidden', 'g_pmviewer_hideunhide', 'g_pmviewer_managemembers', 'g_pmviewer_editposts' ) );
		}
		catch( \IPS\Db\Exception $e )
		{
			/* Ignore "Cannot drop because it does not exist" */
			if( $e->getCode() <> 1091 )
			{
				throw $e;
			}
		}
	}
}