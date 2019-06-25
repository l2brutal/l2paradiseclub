//<?php

 		/* Client ID */
 $form->add( new \IPS\Helpers\Form\Text( 'tw_client_id', \IPS\Settings::i()->tw_client_id, TRUE ) );
if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;