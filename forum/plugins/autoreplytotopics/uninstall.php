//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

\IPS\Db::i()->dropColumn( 'forums_forums', 'autoreply_onoff' );
\IPS\Db::i()->dropColumn( 'forums_forums', 'autoreply_postcount' );
\IPS\Db::i()->dropColumn( 'forums_forums', 'autoreply_closetopic' );
\IPS\Db::i()->dropColumn( 'forums_forums', 'autoreply_authorid' );
\IPS\Db::i()->dropColumn( 'forums_forums', 'autoreply_text' );