//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook32 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'hovercard' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsPad_half.cUserHovercard > div.cUserHovercard_data > ul.ipsDataList.ipsDataList_reducedSpacing',
      'type' => 'add_inside_end',
      'content' => '{{$cont = 0;}}
{{if settings.clubsenhancementsShowProfileBadge}}
	{{$order = \IPS\Settings::i()->clubsenhancementsShowProfileBadgeDisplay;}}
	{{foreach \IPS\Member\Club::clubs( $member, NULL, $order, TRUE ) as $club}}
		{{if $club->canView()}}
			<span data-ipsTooltip title=\'{$club->name}\'>{template="clubIcon" group="clubs" app="core" params="$club, \'tiny\'"}</span>
			{{$cont++;}}
			{{if settings.clubsenhancementsShowPostBadgeNr > 0 AND settings.clubsenhancementsShowPostBadgeNr == $cont}}
				{{break;}}
			{{endif}}
		{{endif}}
	{{endforeach}}
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
