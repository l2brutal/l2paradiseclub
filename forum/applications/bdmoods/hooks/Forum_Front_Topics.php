//<?php

class bdmoods_hook_Forum_Front_Topics extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'postContainer' => 
  array (
    0 => 
    array (
      'selector' => 'article > aside.ipsComment_author.cAuthorPane.ipsColumn.ipsColumn_medium.ipsResponsive_hidePhone > ul.cAuthorPane_info.ipsList_reset > li.cAuthorPane_photo',
      'type' => 'add_inside_end',
      'content' => '{{if \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND $comment->author()->bdm_mood}}
<div class="mood 
{{if settings.bd_moods_displaypos==0}}mood-top-left
{{elseif settings.bd_moods_displaypos==1}}mood-top-right
{{elseif settings.bd_moods_displaypos==2}}mood-bottom-right         
{{elseif settings.bd_moods_displaypos==3}}mood-bottom 
{{elseif settings.bd_moods_displaypos==4}}mood-bottom-left
{{endif}}">{expression=\'\IPS\bdmoods\Mood::load($comment->author()->bdm_mood)->get_image($comment->author()->get_moodTitle())\' raw=\'true\'}</div>{{endif}}
',
    ),
    1 => 
    array (
      'selector' => 'article > div.cAuthorPane.cAuthorPane_mobile.ipsResponsive_showPhone.ipsResponsive_block > div.cAuthorPane_photo',
      'type' => 'add_inside_start',
      'content' => '{{if \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND $comment->author()->bdm_mood AND settings.bd_moods_showOnMobile}}
<div class="mood 
{{if settings.bd_moods_displaypos==0}}mood-top-left
{{elseif settings.bd_moods_displaypos==1}}mood-top-right
{{elseif settings.bd_moods_displaypos==2}}mood-bottom-right         
{{elseif settings.bd_moods_displaypos==3}}mood-bottom 
{{elseif settings.bd_moods_displaypos==4}}mood-bottom-left
{{endif}}">{expression=\'\IPS\bdmoods\Mood::load($comment->author()->bdm_mood)->get_image($comment->author()->get_moodTitle())\' raw=\'true\'}</div>{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */








}