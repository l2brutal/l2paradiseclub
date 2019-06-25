//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook41 extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'header' => 
  array (
    0 => 
    array (
      'selector' => '#elClubFeatures_menu',
      'type' => 'add_inside_end',
      'content' => '{{if $club->approved AND ( $club->owner == \IPS\Member::loggedIn() OR member.isAdmin() )}}
	{{if settings.clubsenhancementsNewPage}}
      <li class=\'ipsMenu_item\'>
                  <a href="{$club->url()->setQueryString( \'do\', \'newHomePage\' )}" data-ipsDialog data-ipsDialog-title="{lang="clubsenhancementsNewHomePage"}">{lang="clubsenhancementsNewHomePage"}</a>
      </li>
	{{endif}}
	{{if settings.clubsenhancementsOwnerCanAddMembers}}
      <li class=\'ipsMenu_item\'>
                  <a href="{$club->url()->setQueryString( \'do\', \'addMembersGroup\' )}" data-ipsDialog data-ipsDialog-size=\'medium\' data-ipsDialog-title="{lang="clubsenhancementsAddMembers"}">{lang="clubsenhancementsAddMembersShort"}</a>
      </li>
	{{endif}}
	<li class=\'ipsMenu_item\'>
				<a href="{$club->url()->setQueryString( \'do\', \'changeOwner\' )}" data-ipsDialog data-ipsDialog-size=\'narrow\' data-ipsDialog-title="{lang="clubsenhancementsChangeOwner"}">{lang="clubsenhancementsChangeOwnerShort"}</a>
	</li>
	<li class=\'ipsMenu_item\'>
				<a href="{$club->url()->setQueryString( \'do\', \'changeType\' )}" data-ipsDialog data-ipsDialog-size=\'narrow\' data-ipsDialog-title="{lang="clubsenhancementsChangeType"}">{lang="clubsenhancementsChangeTypeShort"}</a>
	</li>
	<li class=\'ipsMenu_item\'>
				<a href="{$club->url()->setQueryString( \'do\', \'manageFeatures\' )}" data-ipsDialog data-ipsDialog-size=\'medium\' data-ipsDialog-title="{lang="clubsenhancementsManageFeatures"}">{lang="clubsenhancementsManageFeatures"}</a>
	</li>
{{endif}}',
    ),
    1 => 
    array (
      'selector' => '#tabs_club > ul[role=\'tablist\']',
      'type' => 'replace',
      'content' => '{{if settings.clubsenhancementsNewPage AND $club->new_home_page}}
	<ul role=\'tablist\'>
		{{if $club->type !== \IPS\Member\Club::TYPE_CLOSED || $club->canRead()}}
			<li>
				<a href=\'{$club->url()}\' class="ipsTabs_item {{if request.module == \'clubs\' && request.do != \'members\' && request.do != \'activity\'}} ipsTabs_activeItem{{endif}}" role="tab">
					{lang="club_home"}
				</a>
			</li>
			<li>
				<a href=\'{$club->url()->setQueryString(\'do\', \'activity\')}\' class="ipsTabs_item {{if request.module == \'clubs\' && request.do == \'activity\'}} ipsTabs_activeItem{{endif}}" role="tab">
									{lang="users_activity_feed"}
				</a>
			</li>
			{{if $club->type !== \IPS\Member\Club::TYPE_PUBLIC}}
				<li>
					<a href=\'{$club->url()->setQueryString(\'do\', \'members\')}\' class="ipsTabs_item {{if request.module == \'clubs\' && request.do == \'members\'}} ipsTabs_activeItem{{endif}}" role="tab">{lang="club_members"}
					</a>
				</li>
			{{endif}}
			{{if $club->canRead()}}
				{{foreach $club->nodes() as $nodeID => $node}}
					<li>
						<a href=\'{$node[\'url\']}\' class="ipsTabs_item {{if $container and get_class( $container ) === $node[\'node_class\'] and $container->_id == $node[\'node_id\']}}ipsTabs_activeItem{{endif}}" role="tab">{$node[\'name\']}
						</a>
					</li>
				{{endforeach}}
			{{endif}}
		{{endif}}
	</ul>
{{else}}
				<ul role=\'tablist\'>
					{{if $club->type !== \IPS\Member\Club::TYPE_CLOSED || $club->canRead()}}
						<li>
							<a href=\'{$club->url()}\' class="ipsTabs_item {{if request.module == \'clubs\' && request.do != \'members\'}} ipsTabs_activeItem{{endif}}" role="tab">
								{lang="club_home"}
							</a>
						</li>
						{{if $club->type !== \IPS\Member\Club::TYPE_PUBLIC}}
							<li>
								<a href=\'{$club->url()->setQueryString(\'do\', \'members\')}\' class="ipsTabs_item {{if request.module == \'clubs\' && request.do == \'members\'}} ipsTabs_activeItem{{endif}}" role="tab">
									{lang="club_members"}
								</a>
							</li>
						{{endif}}
						{{if $club->canRead()}}
							{{foreach $club->nodes() as $nodeID => $node}}
								<li>
									<a href=\'{$node[\'url\']}\' class="ipsTabs_item {{if $container and get_class( $container ) === $node[\'node_class\'] and $container->_id == $node[\'node_id\']}}ipsTabs_activeItem{{endif}}" role="tab">
										{$node[\'name\']}
									</a>
								</li>
							{{endforeach}}
						{{endif}}
					{{endif}}
				</ul>
{{endif}}',
    ),
    2 => 
    array (
      'selector' => '#elClubHeader_small > div.ipsPad > div.ipsSideMenu.ipsAreaBackground_reset > ul.ipsSideMenu_list',
      'type' => 'replace',
      'content' => '{{if settings.clubsenhancementsNewPage AND $club->new_home_page}}
	<ul class=\'ipsSideMenu_list\'>
		<li>
			<a href="{$club->url()}" class=\'ipsSideMenu_item\'>{lang="club_home"}</a>
		</li>
      	<li>
			<a href=\'{$club->url()->setQueryString(\'do\', \'activity\')}\' class="ipsSideMenu_item {{if request.module == \'clubs\' && request.do == \'activity\'}} ipsSideMenu_itemActive{{endif}}" role="tab">{lang="users_activity_feed"}</a>
		</li>
		{{if $club->type !== \IPS\Member\Club::TYPE_PUBLIC}}
			<li>
				<a href=\'{$club->url()->setQueryString(\'do\', \'members\')}\' class="ipsSideMenu_item {{if request.module == \'clubs\' && request.do == \'members\'}} ipsSideMenu_itemActive{{endif}}">
								{lang="club_members"}
					</a>
			</li>
		{{endif}}
		{{if $club->canRead()}}
			{{foreach $club->nodes() as $nodeID => $node}}
				<li>
					<a href=\'{$node[\'url\']}\' class="ipsSideMenu_item {{if $container and get_class( $container ) === $node[\'node_class\'] and $container->_id == $node[\'node_id\']}}ipsSideMenu_itemActive{{endif}}" role="tab">{$node[\'name\']}</a>
				</li>
			{{endforeach}}
		{{endif}}
	</ul>
{{else}}
				<ul class=\'ipsSideMenu_list\'>
					<li>
						<a href="{$club->url()}" class=\'ipsSideMenu_item\'>{lang="club_home"}</a>
					</li>
					{{if $club->type !== \IPS\Member\Club::TYPE_PUBLIC}}
						<li>
							<a href=\'{$club->url()->setQueryString(\'do\', \'members\')}\' class="ipsSideMenu_item {{if request.module == \'clubs\' && request.do == \'members\'}} ipsSideMenu_itemActive{{endif}}">
								{lang="club_members"}
							</a>
						</li>
					{{endif}}
					{{if $club->canRead()}}
						{{foreach $club->nodes() as $nodeID => $node}}
							<li>
								<a href=\'{$node[\'url\']}\' class="ipsSideMenu_item {{if $container and get_class( $container ) === $node[\'node_class\'] and $container->_id == $node[\'node_id\']}}ipsSideMenu_itemActive{{endif}}" role="tab">
									{$node[\'name\']}
								</a>
							</li>
						{{endforeach}}
					{{endif}}
				</ul>
{{endif}}',
    ),
  ),
  'members' => 
  array (
    0 => 
    array (
      'selector' => 'ul.ipsToolList.ipsToolList_horizontal.ipsClearfix.ipsSpacer_both',
      'type' => 'add_inside_end',
      'content' => '{{if ( $club->owner == \IPS\Member::loggedIn() AND settings.clubsenhancementsOwnerCanAddMembers ) OR member.isAdmin()}}
	<li class="ipsToolList_primaryAction">
        <a class="ipsButton ipsButton_medium ipsButton_important ipsButton_fullWidth" href="{$club->url()->setQueryString( \'do\', \'addMembersGroup\' )}" data-ipsdialog="" data-ipsdialog-title="{lang="clubsenhancementsAddMembersShort"}" data-ipsdialog-size="medium">
            {lang="clubsenhancementsAddMembersShort"}
        </a>
    </li>
{{endif}}
',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */

public function directory( $featuredClubs, $allClubs, $pagination, $baseUrl, $sortOption, $myClubsActivity, $mapMarkers=NULL )
{
	try
	{
		if( !\IPS\Settings::i()->clubsenhancementsShowMapClubDir )
		{
			$mapMarkers = NULL;
		}
	
		return parent::directory( $featuredClubs, $allClubs, $pagination, $baseUrl, $sortOption, $myClubsActivity, $mapMarkers );
	}
	catch ( \RuntimeException $e )
	{
		if ( method_exists( get_parent_class(), __FUNCTION__ ) )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
		else
		{
			throw $e;
		}
	}
}

}
