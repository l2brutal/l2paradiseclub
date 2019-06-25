//<?php

class advancedtagsprefixes_hook_forumTopicTagList extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'topicRow' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto[itemtype=\'http://schema.org/Article\'] > div.ipsDataItem_main > div.ipsDataItem_meta.ipsType_reset.ipsType_light.ipsType_blendLinks',
      'type' => 'replace',
      'content' => '<div class=\'ipsDataItem_meta ipsType_reset ipsType_light ipsType_blendLinks\'>
	<span itemprop="author" itemscope itemtype="http://schema.org/Person">
		{lang="byline_itemprop" htmlsprintf="$row->author()->link()"}
	</span>{datetime="$row->__get( $row::$databaseColumnMap[\'date\'] )"}
	<meta itemprop="dateCreated" content="{expression="\IPS\DateTime::create( $row->__get( $row::$databaseColumnMap[\'date\'] ) )->rfc3339()"}">
	{{if !in_array( \IPS\Dispatcher::i()->controller, array( \'forums\', \'index\' ) )}}
		{lang="in"} <a href="{$row->container()->url()}">{$row->container()->_title}</a>
	{{endif}}
	{{if \IPS\Settings::i()->prefix_tags_on_topic_list and count( $row->tags() )}}
		&nbsp;&nbsp;
		{template="tags" group="global" app="core" params="$row->tags(), true, true"}
	{{endif}}
</div>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */






}