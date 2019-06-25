//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

try
{
	\IPS\Db::i()->dropColumn( 'core_clubs', array( 'new_home_page', 'new_home_page_content' ) );
	\IPS\Db::i()->dropColumn( 'core_clubs_node_map', array( 'node_position', 'node_enabled' ) );
}
catch( \IPS\Db\Exception $e )
{
	/* Ignore "Cannot drop because it does not exist" */
	if( $e->getCode() <> 1091 )
	{
		throw $e;
	}
}