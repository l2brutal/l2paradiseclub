//<?php

class invite_hook_is_profiles extends _HOOK_CLASS_
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
      'content' => '{template="invitedByHoverCard" params="$member" group="global" location="front" app="invite"}',
    ),
  ),
  'profileHeader' => 
  array (
    0 => 
    array (
      'selector' => '#elProfileStats > ul.ipsList_inline.ipsPos_left',
      'type' => 'add_inside_end',
      'content' => '{template="invitedByProfileHeader" params="$member" group="global" location="front" app="invite"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */








}