<?php


namespace IPS\bdmoods\modules\admin\manage;

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
		\IPS\Dispatcher::i()->checkAcpPermission( 'bdmoods_settings' );
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
        $options = array('Top Left','Top Right','Bottom Right', 'Bottom Center','Bottom Left');
        
		// This is the default method if no 'do' parameter is specified
        $form = new \IPS\Helpers\Form;
		$form->addHeader('bd_moods_general');
        $form->AddSidebar('
            <h2 class="ipsFieldRow_section">Support</h2>
             <ul class="ipsList_reset ipsPad_half ">
                <li class="ipsType_center"><a href="http://blistdevelopment.com.au/forum/" target="_blank" rel="noreferrer" class="ipsButton  ipsButton_small ipsButton_light">Forum</a> <a href="mailto:support@blistdevelopment.com.au" target="_blank" rel="noreferrer" class="ipsButton ipsButton_small ipsButton_light">Email</a></li>
            </ul>');
		$form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_userNavLink', \IPS\Settings::i()->bd_moods_userNavLink, TRUE ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_userDropLink', \IPS\Settings::i()->bd_moods_userDropLink, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Text( 'bd_moods_moodChooserIcon', \IPS\Settings::i()->bd_moods_moodChooserIcon, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Number( 'bd_moods_maxcustomtextlength', \IPS\Settings::i()->bd_moods_maxcustomtextlength, TRUE, array("min"=>1,"max"=>255), NULL, NULL, 'characters' ) );
		$form->addHeader('bd_moods_imageDisplay');
        $form->add( new \IPS\Helpers\Form\Select( 'bd_moods_displaypos', \IPS\Settings::i()->bd_moods_displaypos, TRUE, array('options'=>$options), NULL, NULL, NULL, 'bd_moods_displaypos' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'bd_moods_imgmaxwidth', \IPS\Settings::i()->bd_moods_imgmaxwidth, TRUE, array(), NULL, NULL, 'px' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'bd_moods_imgmaxheight', \IPS\Settings::i()->bd_moods_imgmaxheight, TRUE, array(), NULL, NULL, 'px' ) );
        $form->add( new \IPS\Helpers\Form\YesNo( 'bd_moods_showOnMobile', \IPS\Settings::i()->bd_moods_showOnMobile, TRUE ) );

        /* Handle submissions */
		if ( $values = $form->values() ) {
            $form->saveAsSettings();
        }
        
        \IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack('bd_moods_settings');
		\IPS\Output::i()->output = $form;
	}
	
	// Create new methods with the same name as the 'do' parameter which should execute it
}
