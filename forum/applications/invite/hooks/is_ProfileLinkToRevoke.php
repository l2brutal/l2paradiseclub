//<?php

class invite_hook_is_ProfileLinkToRevoke extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'profileHeader' => 
  array (
    0 => 
    array (
      'selector' => '#elEditProfile',
      'type' => 'add_inside_start',
      'content' => '{{if \IPS\Settings::i()->is_on AND $member->group[\'is_canvite\'] AND \IPS\Member::loggedIn()->isAdmin()}}
	<li>
    	<a href="#elInviteSystem_menu" class="ipsButton ipsButton_overlaid" data-ipsMenu id=\'elInviteSystem\'>
        	<i class="fa fa-exclamation-triangle"> </i>
        	<span class="ipsResponsive_hidePhone ipsResponsive_inline">
            	{lang="module__invite_invite"}  <i class=\'fa fa-caret-down\'></i>
        	</span>
    	</a>
		<ul class=\'ipsMenu ipsMenu_auto ipsHide\' id=\'elInviteSystem_menu\'>
			{{if !$member->invite_revoke_access}}
				<li class=\'ipsMenu_item\'>
					<a href=\'{$member->url()->setQueryString( \'do\', \'inviteRevokeAccess\' )->csrf()}\'>{lang="invite_revoke_access"}</a>
				</li>
			{{else}}
				<li class=\'ipsMenu_item\'>
					<a href=\'{$member->url()->setQueryString( \'do\', \'inviteAllowAccess\' )->csrf()}\'>{lang="invite_revoke_access_stop"}</a>
				</li>
			{{endif}}
		</ul>
	</li>
{{endif}}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */






























}