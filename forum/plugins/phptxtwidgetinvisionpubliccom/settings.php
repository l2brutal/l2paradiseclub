//<?php

$form->add( new \IPS\Helpers\Form\YesNo( 'sod_php_txt_widget_active_php', \IPS\Settings::i()->sod_php_txt_widget_active_php ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;