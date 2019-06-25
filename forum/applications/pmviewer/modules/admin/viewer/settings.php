<?php


namespace IPS\pmviewer\modules\admin\viewer;

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

		$form->addHeader( 'menu__pmviewer_viewer_conversations' );
		$form->add( new \IPS\Helpers\Form\Number( 'pmviewer_messagesperpage', \IPS\Settings::i()->pmviewer_messagesperpage, FALSE, array( 'min' => 5, 'max' => 50 ) ) );

		$form->addHeader( 'menu__pmviewer_viewer_logs' );
		$form->add( new \IPS\Helpers\Form\Number( 'pmviewer_logentriesperpage', \IPS\Settings::i()->pmviewer_logentriesperpage, FALSE, array( 'min' => 5, 'max' => 50 ) ) );

		$form->addHeader( 'pmviewer_systemmessages' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'pmviewer_hiddenmessages', \IPS\Settings::i()->pmviewer_hiddenmessages ) );

		$form->addHeader( 'pmviewer_monitoring' );
		$form->add( new \IPS\Helpers\Form\YesNo( 'pmviewer_monitoring_enable', \IPS\Settings::i()->pmviewer_monitoring_enable, FALSE, array('togglesOn' => array( 'pmviewer_monitoring_keywords', 'pmviewer_monitoring_groups' ) ) ) );
		
		$form->add( new \IPS\Helpers\Form\Stack( 'pmviewer_monitoring_keywords', \IPS\Settings::i()->pmviewer_monitoring_keywords ? explode( ',', \IPS\Settings::i()->pmviewer_monitoring_keywords ) : array(), FALSE, array( 'placeholder' => \IPS\Member::loggedIn()->language()->addToStack('pmviewer_keyword_something') ), NULL, NULL, NULL, 'pmviewer_monitoring_keywords' ) );

		$form->add( new \IPS\Helpers\Form\Select( 'pmviewer_monitoring_groups', \IPS\Settings::i()->pmviewer_monitoring_groups == '*' ? "*" : explode( ',', \IPS\Settings::i()->pmviewer_monitoring_groups ), FALSE, array( 'options' => \IPS\Member\Group::groups( TRUE, FALSE ), 'parse' => 'normal', 'multiple' => true ), NULL, NULL, NULL, 'pmviewer_monitoring_groups' ) );

		if ( $values = $form->values( TRUE ) )
		{
			$form->saveAsSettings( $values );
			\IPS\Session::i()->log( 'acplogs__pmviewer_settings' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
		\IPS\Output::i()->output = $form;
	}
}