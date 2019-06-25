//<?php

class hook28 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'postContainer' => 
  array (
    0 => 
    array (
      'selector' => 'article[itemtype=\'http://schema.org/Answer\'] > aside.ipsComment_author.cAuthorPane.ipsColumn.ipsColumn_medium > ul.cAuthorPane_info.ipsList_reset',
      'type' => 'add_inside_end',
	  'content' => '{{$gID = isset( \IPS\Member::loggedIn()->member_id ) ? \IPS\Member::loggedIn()->member_group_id : 0;}}
{{$check = false;}}
{{$enabled = false;}}
{{$ids = array();}}

{{if !empty( \IPS\Settings::i()->alow_change_group )}}
	{{$alow_change_group = json_decode( \IPS\Settings::i()->alow_change_group, true );}}
	{{$enabled = isset( $alow_change_group[ $gID ] ) ? $alow_change_group[ $gID ] : false;}}
{{endif}}

{{if !empty( \IPS\Settings::i()->add_userpane_link )}}
	{{$add_userpane_link = json_decode( \IPS\Settings::i()->add_userpane_link, true );}}
	{{$link = isset( $add_userpane_link[ $comment->author()->member_group_id ] ) ? $add_userpane_link[ $comment->author()->member_group_id ] : false;}}
{{endif}}

{{if !empty( \IPS\Settings::i()->alowed_change_groups )}}
	{{$alowed_change_groups = json_decode( \IPS\Settings::i()->alowed_change_groups, true );}}
	{{$ids = isset( $alowed_change_groups[ $gID ] ) ? explode( \'|\', $alowed_change_groups[ $gID ] ) : array();}}
{{endif}}

{{if $enabled and $link}}
	{{foreach $ids as $_id}}
		{{if intval( $_id ) === $comment->author()->member_group_id}}
			{{$check = true;}}
		{{endif}}
	{{endforeach}}
{{endif}}

{template="changeGroupLink" params="$comment, $check" group="plugins" location="global" app="core"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */








}