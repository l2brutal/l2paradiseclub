<?php


namespace IPS\invite\modules\admin\invite;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * settings
 */
class _settings extends \IPS\Dispatcher\Controller
{
	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );

		$form = new \IPS\Helpers\Form;

		$form->addTab( 'is_tab_general' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_on', \IPS\Settings::i()->is_on, FALSE, array( 'togglesOn' => array('is_requireinvite', 'is_restoreinvitation', 'is_showicon', 'is_show_inviter_email', 'is_expireinvite', 'is_contentcountperinvite', 'is_showmoremenu', "{$form->id}_header_is_header_invitations", "{$form->id}_tab_is_tab_invitations", "{$form->id}_tab_is_tab_registration" ) ) ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_showmoremenu', \IPS\Settings::i()->is_showmoremenu, FALSE, array(), NULL, NULL, NULL, 'is_showmoremenu' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_showicon', \IPS\Settings::i()->is_showicon, FALSE, array( 'togglesOn' => array('is_showicon_nr' ) ), NULL, NULL, NULL, 'is_showicon' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_showicon_nr', \IPS\Settings::i()->is_showicon_nr, FALSE, array(), NULL, NULL, NULL, 'is_showicon_nr' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_show_inviter_email', \IPS\Settings::i()->is_show_inviter_email, FALSE, array(), NULL, NULL, NULL, 'is_show_inviter_email' ) );
		
		$form->addTab( 'is_tab_invitations' );
		
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_restoreinvitation', \IPS\Settings::i()->is_restoreinvitation, FALSE, array(), NULL, NULL, NULL, 'is_restoreinvitation' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'is_expireinvite', \IPS\Settings::i()->is_expireinvite, FALSE, array( 'unlimited' => 0, 'unlimitedLang' => 'never' ), NULL, \IPS\Member::loggedIn()->language()->addToStack('after'), \IPS\Member::loggedIn()->language()->addToStack('days'), 'is_expireinvite' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'is_contentcountperinvite', \IPS\Settings::i()->is_contentcountperinvite, FALSE, array( 'unlimited' => 0, 'unlimitedLang' => 'never' ), NULL, \IPS\Member::loggedIn()->language()->addToStack('is_contentcountperinvite_ae'), \IPS\Member::loggedIn()->language()->addToStack('members_member_posts'), 'is_contentcountperinvite' ) );

		$form->addTab( 'is_tab_registration' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_requireinvite', \IPS\Settings::i()->is_requireinvite, FALSE, array(), NULL, NULL, NULL, 'is_requireinvite' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_emailfrominvite', \IPS\Settings::i()->is_emailfrominvite, FALSE, array(), NULL, NULL, NULL, 'is_emailfrominvite' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_registrationgroup_toggle', \IPS\Settings::i()->is_registrationgroup_toggle, FALSE, array( 'togglesOn' => array( 'is_registrationgroup' ) ), NULL, NULL, NULL, 'is_registrationgroup_toggle' ) );
		$form->add( new \IPS\Helpers\Form\Select( 'is_registrationgroup', array_filter( explode( ',', \IPS\Settings::i()->is_registrationgroup ) ), FALSE, array( 'options' => \IPS\Member\Group::groups( TRUE, FALSE ), 'parse' => 'normal' ), NULL, NULL, NULL, 'is_registrationgroup' ) );		

		$form->add( new \IPS\Helpers\Form\YesNo( 'is_registration_earninvitations', \IPS\Settings::i()->is_registration_earninvitations, FALSE, array( 'togglesOn' => array( 'is_registration_earninvitations_nr' ) ), NULL, NULL, NULL, 'is_registration_earninvitations' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'is_registration_earninvitations_nr', \IPS\Settings::i()->is_registration_earninvitations_nr, FALSE, array(), NULL, NULL, NULL, 'is_registration_earninvitations_nr' ) );


		if ( $values = $form->values( TRUE ) )
		{
			$form->saveAsSettings( $values );
			\IPS\Member::clearCreateMenu();
			\IPS\Session::i()->log( 'acplogs__invite_settings' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
		\IPS\Output::i()->output = $form;
	}
}