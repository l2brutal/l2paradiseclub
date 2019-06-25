<?php
/**
 * @brief		sodPhpWidget Widget
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	sodPhpTxtWidget
 * @since		10 Mar 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\plugins\phptxtwidgetinvisionpubliccom\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * sodPhpWidget Widget
 */
class _sodPhpWidget extends \IPS\Widget
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'sodPhpWidget';
	
	/**
	 * @brief	App
	 */
	public $app = '';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '7';
	
	/**
	 * Initialise this widget
	 *
	 * @return void
	 */ 
	public function init()
	{
		parent::init();

		if(!\IPS\Settings::i()->sod_php_txt_widget_active_php)
			throw new \Exception("PHP widget is not available");

		$this->template( array( \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' ), $this->key ) );
	}
	
	/**
	 * Specify widget configuration
	 *
	 * @param	null|\IPS\Helpers\Form	$form	Form object
	 * @return	null|\IPS\Helpers\Form
	 */
	public function configuration( &$form=null )
	{
 		if ( $form === null )
		{
	 		$form = new \IPS\Helpers\Form;
 		}

 		$form->add( new \IPS\Helpers\Form\Text( 'sod_php_widget_title', isset( $this->configuration['sod_php_widget_title'] ) ? $this->configuration['sod_php_widget_title'] : '', FALSE ) );
		$form->add( new \IPS\Helpers\Form\Text( 'sod_php_widget_desc', isset( $this->configuration['sod_php_widget_desc'] ) ? $this->configuration['sod_php_widget_desc'] : '', FALSE ) );
		$form->add( new \IPS\Helpers\Form\TextArea( 'sod_php_widget_text', isset( $this->configuration['sod_php_widget_text'] ) ? $this->configuration['sod_php_widget_text'] : '', TRUE, array('rows' => 7) ) );
		$form->add( new \IPS\Helpers\Form\Select( 'sod_php_widget_who_can_see', isset( $this->configuration['sod_php_widget_who_can_see'] ) ? $this->configuration['sod_php_widget_who_can_see'] : '', TRUE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true, 'unlimited' => '*', 'unlimitedLang' => 'everyone' ) ) );

 		return $form;
 	} 
 	
 	 /**
 	 * Ran before saving widget configuration
 	 *
 	 * @param	array	$values	Values from form
 	 * @return	array
 	 */
 	public function preConfig( $values )
 	{
 		return $values;
 	}

	/**
	 * Render a widget
	 *
	 * @return	string
	 */
	public function render()
	{
		$this->configuration['sod_php_widget_title'] 			= isset($this->configuration['sod_php_widget_title']) ?$this->configuration['sod_php_widget_title']: '';
		$this->configuration['sod_php_widget_desc'] 			= isset($this->configuration['sod_php_widget_desc']) ?$this->configuration['sod_php_widget_desc']: '';
		$this->configuration['sod_php_widget_text'] 			= isset($this->configuration['sod_php_widget_text']) ?$this->configuration['sod_php_widget_text']: '';
		$this->configuration['sod_php_widget_who_can_see'] 		= isset($this->configuration['sod_php_widget_who_can_see']) ?$this->configuration['sod_php_widget_who_can_see']: '';

		if ( $this->configuration['sod_php_widget_who_can_see'] != '*' && !\IPS\Member::loggedIn()->inGroup( $this->configuration['sod_php_widget_who_can_see'], TRUE ) || empty($this->configuration['sod_php_widget_text']) ) {
			return '';
		}

		ob_start();
		echo eval('?>' . $this->configuration['sod_php_widget_text']);
		$buffer = ob_get_contents();
		@ob_end_clean();

		return $this->output( $this->configuration['sod_php_widget_title'], $buffer, $this->configuration['sod_php_widget_desc'] );
	}
}