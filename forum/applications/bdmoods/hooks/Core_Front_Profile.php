//<?php

class bdmoods_hook_Core_Front_Profile extends _HOOK_CLASS_
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
      'content' => '{{if \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND $member->bdm_mood }}
<li class="ipsDataItem">
    <span class="ipsDataItem_generic ipsDataItem_size3">
        <strong>
            {lang="bd_moods_hovercard_feeling"}
        </strong>
    </span>
    <span class="ipsDataItem_main">
        {$member->get_moodTitle()}
    </span>
</li> 
{{endif}}',
    ),
  ),
  'profile' => 
  array (
    0 => 
    array (
      'selector' => '#elProfileInfoColumn > div',
      'type' => 'add_inside_end',
      'content' => '{{if \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND $member->bdm_mood}}
<div class="ipsWidget ipsWidget_vertical cProfileSidebarBlock ipsBox ipsSpacer_bottom">
	<h2 class="ipsWidget_title ipsType_reset">{lang=\'bd_moods_mood\'}</h2>
  	<div class="ipsWidget_inner ipsPad mood-profile">
		<ul class="ipsDataList ipsDataList_reducedSpacing cProfileFields">
          <li class="ipsDataItem mood">{expression=\'\IPS\bdmoods\Mood::load($member->bdm_mood)->get_image($member->get_moodTitle())\' raw=\'true\'}</li>
			<li class="ipsDataItem">{lang=\'bd_moods_customFeeling\'} {$member->get_moodTitle()}</li>
    	</ul>
  </div>
</div>
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */
























}