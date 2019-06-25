//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook65 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'participant' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsPhotoPanel.ipsPhotoPanel_tiny > div',
      'type' => 'replace',
      'content' => '	<div>
		{{if $map[\'map_user_id\'] == \IPS\Member::loggedIn()->member_id}}
      	    {{$name = \IPS\Member::loggedIn()->name;}}
      	    {{$group = \IPS\Member::loggedIn()->member_group_id;}}
			<strong>{expression="\IPS\Member\Group::load( $group )->formatName( $name )" raw="true"}</strong><br>
		{{elseif !\IPS\Member::load( $map[\'map_user_id\'] )->member_id}}
			{lang="messenger_deleted_member"}<br>
		{{else}}
      		{{$group = \IPS\Member::load( $map[\'map_user_id\'] )->member_group_id;}}
      		{{$name = \IPS\Member::load( $map[\'map_user_id\'] )->name;}}
			<a href=\'#\' id=\'elMessage{$conversation->id}_user{$map["map_user_id"]}\' class=\'cMessage_name\' data-role=\'userActions\' data-username=\'{member="name" id="$map[\'map_user_id\']" }\' data-ipsMenu><strong>
              {expression="\IPS\Member\Group::load( $group )->formatName( $name )" raw="true"} <i class=\'fa fa-caret-down\'></i></strong></a><br>
		{{endif}}
		<span class=\'ipsType_light ipsType_small\' data-role=\'userReadInfo\'>
			{{if $map[\'map_user_banned\']}}
				<span class="ipsType_warning"><i class="fa fa-ban"></i> {lang="messenger_map_removed"}</span>
			{{elseif !$map[\'map_user_active\']}}
				{{if $map[\'map_left_time\']}}{lang="messenger_map_left"}{datetime="$map[\'map_left_time\']"}{{else}}{lang="messenger_map_left_notime"}{{endif}}
			{{elseif \IPS\Member::load( $map[\'map_user_id\'] )->members_disable_pm}}
				<span title=\'{lang="messenger_map_disabled_desc" sprintf="\IPS\Member::load( $map[\'map_user_id\'] )->name"}\' data-ipsTooltip>{lang="messenger_map_disabled"}</span>
			{{else}}
				{{if $map[\'map_read_time\']}}{lang="messenger_map_read"}{datetime="$map[\'map_read_time\']"}{{else}}{lang="messenger_map_not_read"}{{endif}}
			{{endif}}
		</span>
</div>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
