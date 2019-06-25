//<?php

class invite_hook_is_newGlobalLink extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'userBar' => 
  array (
    0 => 
    array (
      'selector' => '#elUserNav > li.cNotifications.cUserNav_icon',
      'type' => 'add_before',
      'content' => '{template="inviteSystemGlobalLink" group="global" location="front" app="invite" params=""}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */




}