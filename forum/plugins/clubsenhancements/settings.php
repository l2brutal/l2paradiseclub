//<?php

$form->addTab( 'clubsenhancementsGeneralSettings' );
$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsNewPage', \IPS\Settings::i()->clubsenhancementsNewPage, FALSE ) );

if( \IPS\Settings::i()->clubs_locations )
{
	$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsShowMapClubDir', \IPS\Settings::i()->clubsenhancementsShowMapClubDir, FALSE ) );
}

$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsShowAddedBy', \IPS\Settings::i()->clubsenhancementsShowAddedBy, FALSE ) );
$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsAddMembersItems', \IPS\Settings::i()->clubsenhancementsAddMembersItems, FALSE ) );

$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsOwnerCanAddMembers', \IPS\Settings::i()->clubsenhancementsOwnerCanAddMembers, FALSE ) );

$form->add( new \IPS\Helpers\Form\Number( 'clubsenhancementsRestrictFeaturesNr', \IPS\Settings::i()->clubsenhancementsRestrictFeaturesNr, FALSE, array( 'min' => 1, 'unlimited' => 0 ), NULL, NULL, NULL, 'clubsenhancementsRestrictFeaturesNr' ) );

$form->addTab( 'clubsenhancementsMemberRestrictions' );
$form->add( new \IPS\Helpers\Form\Number( 'clubsenhancementsRestrictClubsNr', \IPS\Settings::i()->clubsenhancementsRestrictClubsNr, FALSE, array( 'min' => 1, 'unlimited' => 0 ), NULL, NULL, NULL, 'clubsenhancementsRestrictClubsNr' ) );
$form->add( new \IPS\Helpers\Form\Number( 'clubsenhancementsMemberCanJoin', \IPS\Settings::i()->clubsenhancementsMemberCanJoin, FALSE, array( 'min' => 1, 'unlimited' => 0 ), NULL, NULL, NULL, 'clubsenhancementsMemberCanJoin' ) );

$form->addTab( 'clubsenhancementsForumsIntegration' );
$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsShowPostBadge', \IPS\Settings::i()->clubsenhancementsShowPostBadge, FALSE, array( 'togglesOn' => array( 'clubsenhancementsShowPostBadgeNr', 'clubsenhancementsShowPostBadgeDisplay' ) ) ) );
$form->add( new \IPS\Helpers\Form\Number( 'clubsenhancementsShowPostBadgeNr', \IPS\Settings::i()->clubsenhancementsShowPostBadgeNr, FALSE, array( 'min' => 1,  'unlimited' => 0 ), NULL, NULL, NULL, 'clubsenhancementsShowPostBadgeNr' ) );

$order = array(
	'RAND()' 		=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeDisplayRandom'),
	'name' 			=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeDisplayName'),
	'last_activity'	=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeLastActivity'),
);
$form->add( new \IPS\Helpers\Form\Select( 'clubsenhancementsShowPostBadgeDisplay', \IPS\Settings::i()->clubsenhancementsShowPostBadgeDisplay, FALSE, array( 'options' => $order, 'parse' => 'normal' ), NULL, NULL, NULL, 'clubsenhancementsShowPostBadgeDisplay' ) );

$form->addTab( 'clubsenhancementsProfileIntegration' );
$form->add( new \IPS\Helpers\Form\YesNo( 'clubsenhancementsShowProfileBadge', \IPS\Settings::i()->clubsenhancementsShowProfileBadge, FALSE, array( 'togglesOn' => array( 'clubsenhancementsShowProfileBadgeNr', 'clubsenhancementsShowProfileBadgeDisplay' ) ) ) );
$form->add( new \IPS\Helpers\Form\Number( 'clubsenhancementsShowProfileBadgeNr', \IPS\Settings::i()->clubsenhancementsShowProfileBadgeNr, FALSE, array( 'min' => 1, 'max' => 7 ), NULL, NULL, NULL, 'clubsenhancementsShowProfileBadgeNr' ) );
$order = array(
	'RAND()' 		=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeDisplayRandom'),
	'name' 			=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeDisplayName'),
	'last_activity'	=> \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsShowPostBadgeLastActivity'),
);
$form->add( new \IPS\Helpers\Form\Select( 'clubsenhancementsShowProfileBadgeDisplay', \IPS\Settings::i()->clubsenhancementsShowProfileBadgeDisplay, FALSE, array( 'options' => $order, 'parse' => 'normal' ), NULL, NULL, NULL, 'clubsenhancementsShowProfileBadgeDisplay' ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;