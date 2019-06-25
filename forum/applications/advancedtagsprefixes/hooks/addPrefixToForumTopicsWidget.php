//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class advancedtagsprefixes_hook_addPrefixToForumTopicsWidget extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'topicFeed' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsPad_half.ipsWidget_inner > ul > li a.ipsDataItem_title',
      'type' => 'add_before',
      'content' => '{{if $topic->prefix()}}
	{template="prefix" group="global" app="core" params="$topic->prefix( TRUE ), $topic->prefix()"}
{{endif}}',
    ),
  ),
  'hotTopics' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsPad_half.ipsWidget_inner > ul > li a.ipsDataItem_title',
      'type' => 'add_before',
      'content' => '{{if $topic->prefix()}}
	{template="prefix" group="global" app="core" params="$topic->prefix( TRUE ), $topic->prefix()"}
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
