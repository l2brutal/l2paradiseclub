//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook66 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'comment' => 
  array (
    0 => 
    array (
      'selector' => 'div[data-controller=\'core.front.core.comment\'].ipsComment_content.ipsType_medium > div.ipsComment_header.ipsPhotoPanel.ipsPhotoPanel_mini > div > h3.ipsComment_author.ipsType_blendLinks > strong.ipsType_normal',
      'type' => 'replace',
      'content' => '<strong class=\'ipsType_normal\'>{template="userLink" app="core" group="global" params="$comment->author(), $comment->warningRef(), TRUE"}</strong>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
