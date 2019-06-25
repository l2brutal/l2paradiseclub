<?php


namespace IPS\pmviewer\setup\upg_100004;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 1.0.4 Upgrade Code
 */
class _Upgrade
{
	/**
	 * ...
	 *
	 * @return	array	If returns TRUE, upgrader will proceed to next step. If it returns any other value, it will set this as the value of the 'extra' GET parameter and rerun this step (useful for loops)
	 */
	public function step1()
	{
		if( \IPS\Db::i()->checkForColumn( 'core_groups', 'msg_edited_pmviewer' ) )
		{
			\IPS\Db::i()->dropColumn( 'core_groups', array( 'msg_edited_pmviewer' ) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_message_topics', 'mt_is_hidden' ) )
		{
			\IPS\Db::i()->addColumn( 'core_message_topics', array(
				'name'			=> 'mt_is_hidden',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_groups', 'g_pmviewer_protectedgroup' ) )
		{
			\IPS\Db::i()->addColumn( 'core_groups', array(
				'name'			=> 'g_pmviewer_protectedgroup',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_groups', 'g_pmviewer_viewhidden' ) )
		{
			\IPS\Db::i()->addColumn( 'core_groups', array(
				'name'			=> 'g_pmviewer_viewhidden',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_groups', 'g_pmviewer_hideunhide' ) )
		{
			\IPS\Db::i()->addColumn( 'core_groups', array(
				'name'			=> 'g_pmviewer_hideunhide',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_groups', 'g_pmviewer_managemembers' ) )
		{
			\IPS\Db::i()->addColumn( 'core_groups', array(
				'name'			=> 'g_pmviewer_managemembers',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_groups', 'g_pmviewer_editposts' ) )
		{
			\IPS\Db::i()->addColumn( 'core_groups', array(
				'name'			=> 'g_pmviewer_editposts',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		if( !\IPS\Db::i()->checkForColumn( 'core_message_posts', 'msg_edited_pmviewer' ) )
		{
			\IPS\Db::i()->addColumn( 'core_message_posts', array(
				'name'			=> 'msg_edited_pmviewer',
				'type'			=> 'tinyint',
				'length'		=> 1,
				'allow_null'	=> false,
				'default'		=> 0
			) );
		}

		return TRUE;
	}
	
	// You can create as many additional methods (step2, step3, etc.) as is necessary.
	// Each step will be executed in a new HTTP request
}