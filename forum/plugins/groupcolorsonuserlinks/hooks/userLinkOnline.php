//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook59 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'userLink' => 
  array (
    0 => 
    array (
      'selector' => 'a',
      'type' => 'add_before',
      'content' => '{{if \IPS\Request::i()->module == \'online\'}}
{{$groupFormatting = TRUE;}}
{{endif}}
',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
