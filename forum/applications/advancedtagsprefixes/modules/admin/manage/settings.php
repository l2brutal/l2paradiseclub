<?php


namespace IPS\advancedtagsprefixes\modules\admin\manage;

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
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );
		parent::execute();
	}
	
	/**
	 * Form
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('settings');
		
		
		$form = new \IPS\Helpers\Form;
		
		$form->add( new \IPS\Helpers\Form\YesNo( 'prefix_exclude_supers',		\IPS\Settings::i()->prefix_exclude_supers, FALSE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'prefix_in_title',				\IPS\Settings::i()->prefix_in_title, FALSE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'prefix_on_index',				\IPS\Settings::i()->prefix_on_index, FALSE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'prefix_tags_on_topic_list',	\IPS\Settings::i()->prefix_tags_on_topic_list, FALSE ) );
		
		
		if ( $values = $form->values() )
		{
			$form->saveAsSettings();
			\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_settings' );
		}

		\IPS\Output::i()->output = $form;
	}
}