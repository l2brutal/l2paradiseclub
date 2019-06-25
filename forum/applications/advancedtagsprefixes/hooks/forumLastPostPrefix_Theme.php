//<?php

class advancedtagsprefixes_hook_forumLastPostPrefix_Theme extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'forumRow' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix > ul.ipsDataItem_lastPoster.ipsDataItem_withPhoto > li:nth-child(2) > a',
      'type' => 'add_before',
      'content' => '{{if \IPS\Settings::i()->prefix_on_index and !empty( $lastPost[\'prefix\'] )}}
	{template="prefix" group="global" app="core" params="$lastPost[\'prefix_encoded\'], $lastPost[\'prefix\']"}
{{endif}}',
    ),
    1 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix > ul.ipsDataItem_lastPoster.ipsDataItem_withPhoto > li > a.ipsType_break.ipsContained',
      'type' => 'remove_class',
      'css_classes' => 
      array (
        0 => 'ipsContained',
      ),
    ),
    2 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix > div.ipsDataItem_main',
      'type' => 'add_inside_end',
      'content' => '{{if $forum->show_prefix_in_desc}}
	<ul class="ipsDataItem_subList ipsList_inline">
		{{foreach explode( \',\', $forum->allowed_prefixes ) as $label}}
			{{if ( $prefix = \IPS\Application::load(\'advancedtagsprefixes\')->getPrefixByTitle( $label ) ) !== FALSE}}
				<li>
					{{$url = str_replace( \'&\', \'&amp;\', \IPS\Http\Url::internal( "app=core&module=search&controller=search&type=forums_topic&nodes=" . $forum->id . "&tags=" . rawurlencode( $label ), null, "search", array(), 0 ) );}}
					<a href="{$url}" title="{lang="find_tagged_content" sprintf="$label"}"{{if !$prefix->id}} class=\'ipsTag_prefix\'{{endif}}><span>{$prefix->pre|raw}{{if !$prefix->id or $prefix->showtitle}}{$label}{{endif}}{$prefix->post|raw}</span></a>
				</li>
			{{endif}}
		{{endforeach}}
	</ul>
{{endif}}',
    ),
    3 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix > ul.ipsDataItem_lastPoster.ipsDataItem_withPhoto > li > a.ipsType_break.ipsContained',
      'type' => 'add_before',
      'content' => '{{if \IPS\Settings::i()->prefix_on_index and !empty( $lastPost[\'prefix\'] )}}
	{template="prefix" group="global" app="core" params="$lastPost[\'prefix_encoded\'], $lastPost[\'prefix\']"}
{{endif}}',
    ),
  ),
  'forumGridItem' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsDataItem.ipsGrid_span4.ipsAreaBackground_reset.cForumGrid.ipsClearfix > div.cForumGrid_info > div.ipsPhotoPanel.ipsPhotoPanel_tiny > div > ul.ipsList_reset > li > a.ipsType_break',
      'type' => 'add_before',
      'content' => '{{if \IPS\Settings::i()->prefix_on_index and !empty( $lastPost[\'prefix\'] )}}
	{template="prefix" group="global" app="core" params="$lastPost[\'prefix_encoded\'], $lastPost[\'prefix\']"}
{{endif}}',
    ),
    1 => 
    array (
      'selector' => 'div.ipsDataItem.ipsGrid_span4.ipsAreaBackground_reset.cForumGrid.ipsClearfix > div.ipsPad > div.ipsType_richText.ipsType_normal',
      'type' => 'add_inside_end',
      'content' => '{{if $forum->show_prefix_in_desc}}
	<ul class="ipsDataItem_subList ipsList_inline">
		{{foreach explode( \',\', $forum->allowed_prefixes ) as $label}}
			{{if ( $prefix = \IPS\Application::load(\'advancedtagsprefixes\')->getPrefixByTitle( $label ) ) !== FALSE}}
				<li>
					{{$url = str_replace( \'&\', \'&amp;\', \IPS\Http\Url::internal( "app=core&module=search&controller=search&type=forums_topic&nodes=" . $forum->id . "&tags=" . rawurlencode( $label ), null, "search", array(), 0 ) );}}
					<a href="{$url}" title="{lang="find_tagged_content" sprintf="$label"}"{{if !$prefix->id}} class=\'ipsTag_prefix\'{{endif}}><span>{$prefix->pre|raw}{{if !$prefix->id or $prefix->showtitle}}{$label}{{endif}}{$prefix->post|raw}</span></a>
				</li>
			{{endif}}
		{{endforeach}}
	</ul>
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */










}