//<?php

class hook44 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'whosOnline' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsWidget_inner',
      'type' => 'add_inside_end',
      'content' => '{template="gni" group="plugins" location="global" app="core" params="\IPS\Settings::i()->gni_groups"}',
    ),
  ),
  'activeUsers' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsWidget_inner',
      'type' => 'add_inside_end',
      'content' => '{template="gni" group="plugins" location="global" app="core" params="\IPS\Settings::i()->gni_groups"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */

}