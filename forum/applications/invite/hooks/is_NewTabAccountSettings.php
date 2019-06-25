//<?php

class invite_hook_is_NewTabAccountSettings extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'settings' => 
  array (
    0 => 
    array (
      'selector' => '#elSettingsTabs > div.ipsColumns.ipsColumns_collapsePhone.ipsColumns_bothSpacing > div.ipsColumn.ipsColumn_wide > div.ipsSideMenu > ul.ipsSideMenu_list',
      'type' => 'add_inside_end',
      'content' => '{template="inviteSystemSettings" group="settings" location="front" app="invite" params="$tab"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */








}