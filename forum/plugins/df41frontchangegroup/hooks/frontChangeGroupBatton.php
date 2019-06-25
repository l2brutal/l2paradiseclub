//<?php

class hook27 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
		
	return array_merge_recursive( array (
		'profileHeader' => 
		array (
			0 => 
			array (
			  'selector' => '#elProfileHeader > div.ipsColumns.ipsColumns_collapsePhone',
			  'type' => 'add_before',
			  'content' => '{{$gID = isset( \IPS\Member::loggedIn()->member_id ) ? \IPS\Member::loggedIn()->member_group_id : 0;}}
{{$check = false;}}
{{$enabled = false;}}
{{$ids = array();}}

{{if !empty( \IPS\Settings::i()->alow_change_group )}}
	{{$alow_change_group = json_decode( \IPS\Settings::i()->alow_change_group, true );}}
	{{$enabled = isset( $alow_change_group[ $gID ] ) ? $alow_change_group[ $gID ] : false;}}
{{endif}}

{{if !empty( \IPS\Settings::i()->alowed_change_groups ) }}
	{{$alowed_change_groups = json_decode( \IPS\Settings::i()->alowed_change_groups, true );}}
	{{$ids = isset( $alowed_change_groups[ $gID ] ) ? explode( \'|\', $alowed_change_groups[ $gID ] ) : array();}}
{{endif}}

{{if $enabled}}
	{{foreach $ids as $_id}}
		{{if intval( $_id ) === $member->member_group_id}}
			{{$check = true;}}
		{{endif}}
	{{endforeach}}
{{endif}}

{template="changeGroupBatton" params="$member, $check" group="plugins" location="global" app="core"}',
		),
	  ),
	), parent::hookData() );
}
/* End Hook Data */




}