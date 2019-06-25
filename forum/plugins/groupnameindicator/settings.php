//<?php

$groups = array();

foreach ( \IPS\Member\Group::groups() as $k => $v )
{
	if ( $k != \IPS\Settings::i()->guest_group )
	{
		$groups[ $k ] = $v->name;
	}
}

$form->add( new \IPS\Helpers\Form\Stack( 'gni_groups', (isset(\IPS\Settings::i()->gni_groups)) ? explode(",", \IPS\Settings::i()->gni_groups) : NULL, TRUE, array( 'stackFieldType' => 'Select', 'options' => $groups, 'parse' => 'normal' ) ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;