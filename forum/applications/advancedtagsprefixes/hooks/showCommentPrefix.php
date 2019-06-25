//<?php

class advancedtagsprefixes_hook_showCommentPrefix extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'contentComment' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsDataItem > div.ipsDataItem_main > div.ipsType_break.ipsContained > h4.ipsType_sectionHead.ipsContained > a.ipsType_blendLinks > span.cSearchResultHighlight',
      'type' => 'add_before',
      'content' => '{{if $comment->item()->prefix()}}
	{{$prefix = \IPS\Application::load("advancedtagsprefixes")->getPrefixByTitle( $comment->item()->prefix() );}}
	{{if $prefix instanceof \IPS\advancedtagsprefixes\Prefix and $prefix->id}}
		{$prefix->pre|raw}
		{{if $prefix->showtitle}}
			{$comment->item()->prefix()}
		{{endif}}
		{$prefix->post|raw}
	{{endif}}
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */




}