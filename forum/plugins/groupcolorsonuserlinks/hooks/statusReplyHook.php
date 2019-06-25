//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook61 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'statusReply' => 
  array (
    0 => 
    array (
      'selector' => 'p.ipsComment_author.ipsType_normal > strong',
      'type' => 'replace',
      'content' => '<strong>{template="userLink" app="core" group="global" location="front" params="$comment->author(), NULL, $comment->author()->member_group_id"}</strong>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
