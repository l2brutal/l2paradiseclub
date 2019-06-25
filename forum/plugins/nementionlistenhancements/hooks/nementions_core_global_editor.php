//<?php
/* To prevent PHP errors (extending class does not exist) revealing path */
if (!defined('\IPS\SUITE_UNIQUE_KEY')) {
    exit;
}

class hook29 extends _HOOK_CLASS_ {
    /* !Hook Data - DO NOT REMOVE */

    public static function hookData() {
        return array_merge_recursive(array(
            'mentionRow' =>
            array(
                0 =>
                array(
                    'selector' => 'li.ipsMenu_item.ipsCursor_pointer',
                    'type' => 'add_before',
                    'content' => ''
                    . '{{$nebanned = \IPS\Member::load($member->member_id)->isBanned();}}'
                    . '{{$nevalidating = \IPS\Member::load($member->member_id)->members_bitoptions[\'validating\'];}}'
                    . '{{if((\IPS\Settings::i()->nementions_include_banned OR (!\IPS\Settings::i()->nementions_include_banned AND !$nebanned)))}}'
                    . '{{if((\IPS\Settings::i()->nementions_include_validating OR (!\IPS\Settings::i()->nementions_include_validating AND !$nevalidating)))}}'
                    . '{{if((\IPS\Settings::i()->nementions_include_groups === "*" OR in_array($member->member_group_id, explode(",", \IPS\Settings::i()->nementions_include_groups))))}}'
                    . '{{$config = \IPS\Member::load($member->member_id)->notificationsConfiguration();}}'
                    . '{{if(\IPS\Settings::i()->nementions_group_indicator_type)}}'
                    . '{{$group_name = \IPS\Member\Group::load($member->member_group_id)->name;}}'
                    . '{{else}}'
                    . '{{$group_name = $member->groupName;}}'
                    . '{{endif}}',
                ),
                1 =>
                array(
                    'selector' => 'li.ipsMenu_item.ipsCursor_pointer',
                    'type' => 'add_after',
                    'content' => ''
                    . '{{endif}}'
                    . '{{endif}}'
                    . '{{endif}}',
                ),
                2 =>
                array(
                    'selector' => 'li.ipsMenu_item.ipsCursor_pointer > a',
                    'type' => 'add_inside_start',
                    'content' => ''
                    . '{{if(\IPS\Settings::i()->nementions_online_indicator)}}'
                    . '<span class="ipsPad_half">'
                    . '{{if($member->isOnline())}}'
                    . '<i class="fa fa-circle ipsOnlineStatus_online"></i>'
                    . '{{else}}'
                    . '<i class="fa fa-circle ipsOnlineStatus_offline"></i>'
                    . '{{endif}}'
                    . '</span>'
                    . '{{endif}}'
                    . '{{if(\IPS\Settings::i()->nementions_inline_indicator)}}'
                    . '<span class = "ipsPad_half">{{if(in_array(\'inline\', $config[\'mention\']))}}<i class="fa {{if(\IPS\Settings::i()->nementions_inline_indicator_enabled_icon === \'*\')}}{{$testing = \'fa-fw\';}}{{else}}{{$testing = \IPS\Settings::i()->nementions_inline_indicator_enabled_icon;}}{{endif}}{$testing}">&nbsp;</i>{{else}}<i class="fa {{if(\IPS\Settings::i()->nementions_inline_indicator_disabled_icon === \'*\')}}{{$testing = \'fa-fw\';}}{{else}}{{$testing = \IPS\Settings::i()->nementions_inline_indicator_disabled_icon;}}{{endif}}{$testing}">&nbsp;</i>{{endif}}</span>'
                    . '{{endif}}'
                    . '{{if(\IPS\Settings::i()->nementions_email_indicator)}}'
                    . '<span class="ipsPad_half">{{if(in_array(\'email\', $config[\'mention\']))}}<i class="fa {{if(\IPS\Settings::i()->nementions_email_indicator_enabled_icon === \'*\')}}{{$testing = \'fa-fw\';}}{{else}}{{$testing = \IPS\Settings::i()->nementions_email_indicator_enabled_icon;}}{{endif}}{$testing}">&nbsp;</i>{{else}}<i class="fa {{if(\IPS\Settings::i()->nementions_email_indicator_disabled_icon === \'*\')}}{{$testing = \'fa-fw\';}}{{else}}{{$testing = \IPS\Settings::i()->nementions_email_indicator_disabled_icon;}}{{endif}}{$testing}">&nbsp;</i>{{endif}}</span>'
                    . '{{endif}}',
                ),
                3 =>
                array(
                    'selector' => 'li.ipsMenu_item.ipsCursor_pointer > a > span.ipsPad_half[data-role=\'mentionname\']',
                    'type' => 'add_after',
                    'content' => ''
                    . '{{if(\IPS\Settings::i()->nementions_group_indicator)}}'
                    . '({$group_name|raw})'
                    . '{{endif}}',
                ),
            ),
                ), parent::hookData());
    }

    /* End Hook Data */
}
