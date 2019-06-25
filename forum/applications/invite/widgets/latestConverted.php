<?php
/**
 * @brief		latestConverted Widget
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	invite
 * @since		06 Sep 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * latestConverted Widget
 */
class _latestConverted extends \IPS\Widget\StaticCache
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'latestConverted';
	
	/**
	 * @brief	App
	 */
	public $app = 'invite';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '';
	
	/**
	 * Initialise this widget
	 *
	 * @return void
	 */ 
	public function init()
	{
		$this->template( array( \IPS\Theme::i()->getTemplate( 'widgets', $this->app, 'front' ), $this->key ) );
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

 		$form->add( new \IPS\Helpers\Form\Number( 'is_nr', isset( $this->configuration['is_nr'] ) ? $this->configuration['is_nr'] : 5, TRUE ) );

		$form->add( new \IPS\Helpers\Form\Select( 'is_visibleto', $this->configuration['is_visibleto'] ? $this->configuration['is_visibleto'] : '*', FALSE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true, 'unlimited' => '*', 'unlimitedLang' => 'all_groups' ), NULL, NULL, NULL, 'is_visibleto' ) );

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
		if ( isset( $this->configuration['is_visibleto'] ) and is_array( $this->configuration['is_visibleto'] ) )
		{
			if( !\IPS\Member::loggedIn()->inGroup( $this->configuration['is_visibleto'] ) )
			{
				return '';
			}
		}

		/* How many? */
		$limit = isset( $this->configuration['is_nr'] ) ? $this->configuration['is_nr'] : 5;

		$converted = array();

		foreach ( \IPS\Db::i()->select( '*', 'invite_invites', 'invite_conv_member_id>0' ) as $members )
		{
			$converted[ $members['invite_conv_member_id'] ] = $members;
		}

		arsort( $converted );
		$converted = array_slice( $converted, 0, $limit, TRUE );

		/* Load their data */	
		foreach ( \IPS\Db::i()->select( '*', 'core_members', \IPS\Db::i()->in( 'member_id', array_keys( $converted ) ) ) as $member )
		{
			\IPS\Member::constructFromData( $member );
		}

		/* Display */
		return $this->output( $converted );
	}
}