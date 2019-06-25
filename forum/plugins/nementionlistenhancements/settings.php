//<?php
$form->addTab('General');
$form->add(new \IPS\Helpers\Form\Select('nementions_include_groups', \IPS\Settings::i()->nementions_include_groups == '*' ? '*' : explode(',', \IPS\Settings::i()->nementions_include_groups), FALSE, array('options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => TRUE, 'unlimited' => '*', 'unlimitedLang' => 'All Groups')));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_include_banned', \IPS\Settings::i()->nementions_include_banned, FALSE, array()));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_include_validating', \IPS\Settings::i()->nementions_include_validating, FALSE, array()));
$form->addTab('Display');
$form->add(new \IPS\Helpers\Form\YesNo('nementions_online_indicator', \IPS\Settings::i()->nementions_online_indicator, FALSE, array()));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_group_indicator', \IPS\Settings::i()->nementions_group_indicator, FALSE, array('togglesOn' => array('nementions_group_indicator_type'))));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_group_indicator_type', \IPS\Settings::i()->nementions_group_indicator_type, FALSE, array('togglesOn' => array('nementions_group_indicator_format')), NULL, NULL, NULL, 'nementions_group_indicator_type'));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_inline_indicator', \IPS\Settings::i()->nementions_inline_indicator, FALSE, array('togglesOn' => array('nementions_inline_indicator_enabled_icon', 'nementions_inline_indicator_disabled_icon'))));
$form->add(new \IPS\Helpers\Form\Text('nementions_inline_indicator_enabled_icon', \IPS\Settings::i()->nementions_inline_indicator_enabled_icon, TRUE, array(), NULL, NULL, NULL, 'nementions_inline_indicator_enabled_icon'));
$form->add(new \IPS\Helpers\Form\Text('nementions_inline_indicator_disabled_icon', \IPS\Settings::i()->nementions_inline_indicator_disabled_icon, TRUE, array(), NULL, NULL, NULL, 'nementions_inline_indicator_disabled_icon'));
$form->add(new \IPS\Helpers\Form\YesNo('nementions_email_indicator', \IPS\Settings::i()->nementions_email_indicator, FALSE, array('togglesOn' => array('nementions_email_indicator_enabled_icon', 'nementions_email_indicator_disabled_icon'))));
$form->add(new \IPS\Helpers\Form\Text('nementions_email_indicator_enabled_icon', \IPS\Settings::i()->nementions_email_indicator_enabled_icon, TRUE, array(), NULL, NULL, NULL, 'nementions_email_indicator_enabled_icon'));
$form->add(new \IPS\Helpers\Form\Text('nementions_email_indicator_disabled_icon', \IPS\Settings::i()->nementions_email_indicator_disabled_icon, TRUE, array(), NULL, NULL, NULL, 'nementions_email_indicator_disabled_icon'));

if ($values = $form->values()) {
    //Just a little bit of value modification
    //If the admin has set the indicators to be enabled, but then sets the icon for BOTH enabled and disabled to '*' then that is the same as disabling the indicator
    //So we set the indicator to disabled too.
    if ($values['nementions_inline_indicator_enabled_icon'] === '*' AND $values['nementions_inline_indicator_disabled_icon'] === '*') {
        unset($values['nementions_inline_indicator']);
        $values['nementions_inline_indicator'] = '0';
    }
    if ($values['nementions_email_indicator_enabled_icon'] === '*' AND $values['nementions_email_indicator_disabled_icon'] === '*') {
        unset($values['nementions_email_indicator']);
        $values['nementions_email_indicator'] = '0';
    }
    if (!$values['nementions_include_groups'] === '*') {
        $values['nementions_include_groups'] = implode(",", $values['nementions_include_groups']);
    }
    $form->saveAsSettings($values);
    return TRUE;
}

return $form;
