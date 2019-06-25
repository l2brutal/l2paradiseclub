<?php
/**
 * @brief		topInviters Widget
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
 * topInviters Widget
 */
class _topInviters extends \IPS\Widget\StaticCache
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'topInviters';
	
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

		$form->add( new \IPS\Helpers\Form\Number( 'is_number_to_show', isset( $this->configuration['is_number_to_show'] ) ? $this->configuration['is_number_to_show'] : 5, TRUE, array( 'max' => 25 ) ) );

		$form->add( new \IPS\Helpers\Form\Select( 'is_exclude_groups', $this->configuration['is_exclude_groups'] ? $this->configuration['is_exclude_groups'] : '*', FALSE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true, 'unlimited' => '*', 'unlimitedLang' => 'all_groups' ), NULL, NULL, NULL, 'is_exclude_groups' ) );

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
		if ( isset( $this->configuration['is_exclude_groups'] ) and is_array( $this->configuration['is_exclude_groups'] ) )
		{
			if( !\IPS\Member::loggedIn()->inGroup( $this->configuration['is_exclude_groups'] ) )
			{
				return '';
			}
		}
		
		/* Work out who has got the most reputation this week... */
		$topContributorsThisWeek = array();

		/* How many? */
		$limit = isset( $this->configuration['is_number_to_show'] ) ? $this->configuration['is_number_to_show'] : 5;
		
		/* Work out who has got the most reputation this week... */
		$topContributorsThisWeek = array();
		foreach ( \IPS\Db::i()->select( 'invite_sender_id, count(invite_id) as rep', 'invite_invites', array( 'invite_sender_id>0 AND invite_conv_date>?', \IPS\DateTime::create()->sub( new \DateInterval( 'P1W' ) )->getTimestamp() ) ) as $rep )
		{
			if ( !isset( $topContributorsThisWeek[ $rep['invite_sender_id'] ] ) )
			{
				$topContributorsThisWeek[ $rep['invite_sender_id'] ] = $rep['rep'];
			}
			else
			{
				$topContributorsThisWeek[ $rep['invite_sender_id'] ] += $rep['rep'];
			}
		}

		arsort( $topContributorsThisWeek );
		$topContributorsThisWeek = array_slice( $topContributorsThisWeek, 0, $limit, TRUE );
		
		/* Load their data */	
		foreach ( \IPS\Db::i()->select( '*', 'core_members', \IPS\Db::i()->in( 'member_id', array_keys( $topContributorsThisWeek ) ) ) as $member )
		{
			\IPS\Member::constructFromData( $member );
		}

		/* Display */
		return $this->output( $topContributorsThisWeek, $limit );
	}
}