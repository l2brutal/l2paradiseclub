//<?php

class bdmoods_hook_front_global extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'userBar' => 
  array (
    0 => 
    array (
      'selector' => '#elUserNav > li.cInbox',
      'type' => 'add_after',
      'content' => '{{if \IPS\Settings::i()->bd_moods_userNavLink AND \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND \IPS\Member::loggedIn()->group[\'g_bdm_canChange\']}}
<li class="cUserNav_icon" data-ipsTooltip _title="{lang=\'bd_moods_mood\'}"><a href="{url=\'app=bdmoods&module=mood&controller=update\'}" data-ipsDialog data-ipsDialog-title="{lang=\'bd_moods_moodChooser\'}" data-ipsDialog-size="medium"><i class="fa {setting=\'bd_moods_moodChooserIcon\'}"> </i></a></li>
{{endif}}',
    ),
    1 => 
    array (
      'selector' => '#elAccountSettingsLink',
      'type' => 'add_after',
      'content' => '{{if \IPS\Settings::i()->bd_moods_userDropLink AND \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND \IPS\Member::loggedIn()->group[\'g_bdm_canChange\']}}
<li class="ipsMenu_item"><a href="{url=\'app=bdmoods&module=mood&controller=update\'}" data-ipsDialog data-ipsDialog-title="{lang=\'bd_moods_moodChooser\'}" data-ipsDialog-size="medium">{lang=\'bd_moods_updateMood\'}</a></li>
{{endif}}',
    ),
  ),
  'mobileNavigation' => 
  array (
    0 => 
    array (
      'selector' => '#elUserNav_mobile > li.cInbox',
      'type' => 'add_before',
      'content' => '{{if \IPS\Settings::i()->bd_moods_userNavLink AND \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND \IPS\Member::loggedIn()->group[\'g_bdm_canChange\']}}
<li class="cUserNav_icon" data-ipsTooltip _title="{lang=\'bd_moods_mood\'}"><a href="{url=\'app=bdmoods&module=mood&controller=update\'}" data-ipsdialog data-ipsdialog-title="{lang=\'bd_moods_moodChooser\'}" data-ipsdialog-size="medium" ><i class="fa {setting=\'bd_moods_moodChooserIcon\'}"> </i></a></li>
{{endif}}',
    ),
    1 => 
    array (
      'selector' => '#elAccountSettingsLinkMobile',
      'type' => 'add_after',
      'content' => '{{if \IPS\Settings::i()->bd_moods_userDropLink AND \IPS\Member::loggedIn()->group[\'g_bdm_canSee\'] AND \IPS\Member::loggedIn()->group[\'g_bdm_canChange\']}}
<li><a href="{url=\'app=bdmoods&module=mood&controller=update\'}" data-ipsDialog data-ipsDialog-title="{lang=\'bd_moods_moodChooser\'}" data-ipsDialog-size="medium">{lang=\'bd_moods_updateMood\'}</a></li>
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */






















































}