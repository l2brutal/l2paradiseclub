//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class fmakeup_hook_fmu_badges extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'forumRow' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix > div.ipsDataItem_main > h4.ipsDataItem_title.ipsType_large.ipsType_break > a',
      'type' => 'add_after',
      'content' => '{{if (\IPS\Settings::i()->fmu_g_rules && $forum->show_rules > 0)}}
	<span class="ipsBadge ipsBadge_neutral ipsBadge_small ipsType_center" data-ipstooltip="" _title="{lang="fmu_g_rules_tooltip"}"><i class="fa fa-exclamation"></i></span> 
{{endif}}
{{if ($forum->fmakeup_status == 1 && $forum->fmakeup_text)}} <span class="ipsBadge ipsBadge_intermediary ipsBadge_{setting="fmu_badge_size"}" style="background-color:{$forum->fmakeup_color}"> {$forum->fmakeup_text}</span>
{{endif}}',
    ),
  ),
  'index' => 
  array (
    1 => 
    array (
      'selector' => 'section > ol.ipsList_reset.cForumList[data-controller=\'core.global.core.table, forums.front.forum.forumList\'] > li.cForumRow.ipsBox.ipsSpacer_bottom',
      'type' => 'replace',
      'content' => '<li data-categoryID=\'{$category->_id}\' class=\'cForumRow ipsBox ipsSpacer_bottom\'>
  {{if isset($category->fmakeup_border) || isset($category->fmakeup_bg) }}
	{{$style = "style=\"";}}
	{{if (isset($category->fmakeup_border) && \IPS\Settings::i()->fmu_border_on)}}
		{{$style .= "border: ".\IPS\Settings::i()->fmu_border_size."px solid ".$category->fmakeup_border.";"; }}
	{{endif}}
	{{if isset($category->fmakeup_bg)}}
		{{$style .= "background-color: ".$category->fmakeup_bg.";";}}
	{{endif}}
	{{$style .= "\"";}}
{{else}}
    {{$style="";}}
{{endif}}
				<h2 class="ipsType_sectionTitle ipsType_reset cForumTitle" {$style|raw}>
					<a href=\'#\' class=\'ipsPos_right ipsJS_show ipsType_noUnderline cForumToggle\' data-action=\'toggleCategory\' data-ipsTooltip title=\'{lang="toggle_this_category"}\'></a>
					<a href=\'{$category->url()}\'>{$category->_title}</a>
                  {{if ($category->fmakeup_status == 1 && $category->fmakeup_text)}} 
<span class="ipsBadge ipsBadge_intermediary ipsBadge_{setting="fmu_badge_size"}" style="background-color:{$category->fmakeup_color}"> {$category->fmakeup_text}</span>
{{endif}}
				</h2>
  {{$style = NULL;}}
				{{if theme.forum_layout === \'grid\' || $category->fmakeup_grid === 1}}
					<div class=\'ipsAreaBackground ipsPad\' data-role="forums">
						<div class=\'ipsGrid ipsGrid_collapsePhone\' data-ipsGrid data-ipsGrid-minItemSize=\'250\' data-ipsGrid-maxItemSize=\'500\' data-ipsGrid-equalHeights=\'row\'>
							{{foreach $category->children() as $forum}}
								{template="forumGridItem" group="index" app="forums" params="$forum"}
							{{endforeach}}
						</div>
					</div>
				{{else}}
					<ol class="ipsDataList ipsDataList_large ipsDataList_zebra ipsAreaBackground_reset" data-role="forums">
						{{foreach $category->children() as $forum}}
							{template="forumRow" group="index" app="forums" params="$forum"}
						{{endforeach}}
					</ol>
				{{endif}}
			</li>',
    ),
  ),
  'forumGridItem' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsDataItem.ipsGrid_span4.ipsAreaBackground_reset.cForumGrid.ipsClearfix > div.ipsPhotoPanel.ipsPhotoPanel_mini.ipsClearfix.ipsPad.ipsAreaBackground_light.cForumGrid_forumInfo > div > p.ipsType_reset',
      'type' => 'add_inside_end',
      'content' => '{{if (\IPS\Settings::i()->fmu_g_rules && $forum->show_rules > 0)}}
	<span class="ipsBadge ipsBadge_neutral ipsBadge_small ipsType_center" data-ipstooltip="" _title="{lang="fmu_g_rules_tooltip"}"><i class="fa fa-exclamation"></i></span> 
{{endif}}
{{if ($forum->fmakeup_status == 1 && $forum->fmakeup_text)}} <span class="ipsBadge ipsBadge_intermediary ipsBadge_{setting="fmu_badge_size"}" style="background-color:{$forum->fmakeup_color};float:right;"> {$forum->fmakeup_text}</span>
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
