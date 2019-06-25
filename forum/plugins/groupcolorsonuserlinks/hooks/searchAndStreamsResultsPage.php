//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook62 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'searchResult' => 
  array (
    0 => 
    array (
      'selector' => 'li[data-role=\'activityItem\'] p.ipsType_reset.ipsStreamItem_status.ipsType_blendLinks',
      'type' => 'replace',
      'content' => '						<p class=\'ipsType_reset ipsStreamItem_status ipsType_blendLinks\'>
							H1 {expression="$itemClass::searchResultSummaryLanguage( $authorData, $articles, $indexData, $itemData )" raw="true"} <a href=\'{$containerUrl}\'>{$containerTitle|raw}</a>
						</p>',
    ),
    1 => 
    array (
      'selector' => 'li[data-role=\'activityItem\'] p.ipsStreamItem_status.ipsType_reset.ipsType_blendLinks',
      'type' => 'replace',
      'content' => '<p class=\'ipsStreamItem_status ipsType_reset ipsType_blendLinks\'>
	{expression="$itemClass::searchResultSummaryLanguage( $authorData, $articles, $indexData, $itemData )" raw="TRUE"} <a href=\'{$containerUrl}\'>{$containerTitle|raw}</a>
</p>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
