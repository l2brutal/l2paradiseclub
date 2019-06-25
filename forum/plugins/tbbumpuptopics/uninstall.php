//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

// Check all fields and generate queries
$columnsToDrop = array();

foreach( array( 'tb_but_use', 'tb_but_forums', 'tb_but_bumpall', 'tb_but_day_limit', 'tb_but_time_limit', 'tb_but_last_limit' ) as $col )
{
	if( \IPS\Db::i()->checkForColumn( 'core_groups', "{$col}" ) )
	{
		$columnsToDrop[] = $col;
	}
}

# Remove group table columns
if ( count($columnsToDrop) )
{
	\IPS\Db::i()->dropColumn( 'core_groups', $columnsToDrop );
}


# Remove member table column
if( \IPS\Db::i()->checkForColumn( 'core_members', 'tb_but_cache' ) )
{
	\IPS\Db::i()->dropColumn( 'core_members', 'tb_but_cache' );
}
