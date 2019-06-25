//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

class hook69 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'topic' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsToolList_primaryAction',
      'type' => 'add_after',
      'content' => '{template="tbButBumpButton" params="$topic" group="plugins" location="global" app="core"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}