//<?php

$form->addTab( 'General' );

$form->add( new \IPS\Helpers\Form\Select( 'cloak_groups', \IPS\Settings::i()->cloak_groups == '*' ? '*' : explode( ',', \IPS\Settings::i()->cloak_groups ), FALSE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => TRUE, 'unlimited' => '*', 'unlimitedLang' => 'All Groups' ) ) );

$form->add( new \IPS\Helpers\Form\Node( 'cloak_forums', ( \IPS\Settings::i()->cloak_forums == 0 ) ? 0 : \IPS\Settings::i()->cloak_forums, FALSE, array( 'class' => 'IPS\forums\Forum', 'multiple' => TRUE, 'zeroVal' => 'all' ) ) );

$form->add( new \IPS\Helpers\Form\Select( 'cloak_message_type', isset( \IPS\Settings::i()->cloak_message_type ) ? \IPS\Settings::i()->cloak_message_type : array(), FALSE, array( 'options' => [ 'inline' => 'Inline', 'box' => 'Message Box', 'invisible' => 'Invisible', 'custom' => 'Custom' ], 'toggles' => [ 'custom' => [ 'cloak_guest_message', 'cloak_user_message' ] ] ), NULL, NULL, NULL, 'cloak_message_type' ) );

$form->add( new \IPS\Helpers\Form\Text( 'cloak_guest_message', \IPS\Settings::i()->cloak_guest_message, FALSE, array(), NULL, NULL, NULL, 'cloak_guest_message' ) );

$form->add( new \IPS\Helpers\Form\Text( 'cloak_user_message', \IPS\Settings::i()->cloak_user_message, FALSE, array(), NULL, NULL, NULL, 'cloak_user_message' ) );

$form->addTab( 'Options' );

$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_links', \IPS\Settings::i()->cloak_links, FALSE, array() ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_images', \IPS\Settings::i()->cloak_images, FALSE, array() ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_attachments', \IPS\Settings::i()->cloak_attachments, FALSE, array() ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_code', \IPS\Settings::i()->cloak_code, FALSE, array() ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_spoilers', \IPS\Settings::i()->cloak_spoilers, FALSE, array() ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'cloak_quotes', \IPS\Settings::i()->cloak_quotes, FALSE, array() ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;