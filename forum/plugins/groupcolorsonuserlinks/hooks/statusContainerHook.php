//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook60 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'statusContainer' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsStreamItem.ipsStreamItem_contentBlock.ipsAreaBackground_reset.ipsPad[data-role=\'activityItem\'] > div.ipsStreamItem_container > div.ipsStreamItem_header.ipsPhotoPanel.ipsPhotoPanel_mini',
      'type' => 'replace',
      'content' => '<div class=\'ipsStreamItem_header ipsPhotoPanel ipsPhotoPanel_mini\'>
			<span class=\'ipsStreamItem_contentType\' data-ipsTooltip title=\'{lang="status_update"}\'><i class=\'fa fa-user\'></i></span>
			{{if $authorData}}
				{template="userPhotoFromData" group="global" app="core" params="$authorData[\'member_id\'], $authorData[\'name\'], $authorData[\'members_seo_name\'], \IPS\Member::photoUrl( $authorData ), ( $condensed ? \'tiny\' : \'mini\' )"}
			{{else}}
				{template="userPhoto" group="global" app="core" params="$status->author(), ( $condensed ? \'tiny\' : \'mini\' )"}
			{{endif}}
			<div>
				<h2 class=\'ipsType_reset ipsStreamItem_title {{if $condensed}}ipsStreamItem_titleSmall{{endif}} ipsType_break\'>
					{{if $status->member_id != $status->author()->member_id}}
						<ul class=\'ipsList_inline ipsList_noSpacing\'>
							<li>
								<strong>
									{{if $authorData}}
										{template="userLinkFromData" group="global" app="core" params="$authorData[\'member_id\'], $authorData[\'name\'], $authorData[\'members_seo_name\'], $authorData[\'member_group_id\']"}
									{{else}}
										{template="userLink" app="core" group="global" location="front" params="$status->author()"}
									{{endif}}
								</strong>
							</li>
							<li>
								 <i class=\'fa fa-angle-right\'></i> 
							</li>
							<li>
								<strong>
									{{if $profileOwnerData}}
										{template="userLinkFromData" group="global" app="core" params="$profileOwnerData[\'member_id\'], $profileOwnerData[\'name\'], $profileOwnerData[\'members_seo_name\'], $profileOwnerData[\'member_group_id\']"}
									{{else}}
										{member="link()" id="$status->member_id" raw="true"}
									{{endif}}
								</strong>
							</li>
						</ul>
					{{else}}
						<strong>
							{{if $authorData}}
								{template="userLinkFromData" group="global" app="core" params="$authorData[\'member_id\'], $authorData[\'name\'], $authorData[\'members_seo_name\'], $authorData[\'member_group_id\']"}
							{{else}}
								{template="userLink" app="core" group="global" location="front" params="$status->author()"}
							{{endif}}
						</strong>
					{{endif}}
					{{if $status->hidden()}}
						<span class="ipsBadge ipsBadge_icon ipsBadge_warning" data-ipsTooltip title=\'{lang="hidden"}\'><i class=\'fa fa-eye-slash\'></i></span>
					{{endif}}
				</h2>
				{{if $condensed}}
					<ul class=\'ipsList_inline ipsStreamItem_stats ipsType_light\'>
						<li>
							<a href=\'{$status->url()}\' class=\'ipsType_blendLinks\'><i class=\'fa fa-clock-o\'></i> {datetime="$status->date"}</a>
						</li>
					</ul>
					<p class="ipsStreamItem_status ipsType_reset">
						{{if $status->member_id == $status->author()->member_id}}
							{{if $authorData}}
								{lang="member_posted_status_self" htmlsprintf="$authorData[\'formattedname\']"}
							{{else}}
								{lang="member_posted_status_self" sprintf="$status->author()->name"}
							{{endif}}
						{{else}}
							{{if $authorData}}
								{lang="member_posted_status_other" htmlsprintf="$authorData[\'formattedname\'], $profileOwnerData[\'name\']"}
							{{else}}
								{lang="member_posted_status_other" sprintf="$status->author()->name, \IPS\Member::load( $status->member_id )->name"}
							{{endif}}
						{{endif}}
					</p>
				{{endif}}
			</div>
		</div>',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


}
