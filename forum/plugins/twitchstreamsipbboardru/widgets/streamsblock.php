<?php
/**
 * @brief		streamsblock Widget
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	twstreams
 * @since		21 Jul 2016
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\plugins\twitchstreamsipbboardru\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * streamsblock Widget
 */
class _streamsblock extends \IPS\Widget
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'streamsblock';
	
	/**
	 * @brief	App
	 */
	public $app = '';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '6';
	
	/**
	 * Initialise this widget
	 *
	 * @return void
	 */ 
	public function init()
	{
		// Use this to perform any set up and to assign a template that is not in the following format:
		// $this->template( array( \IPS\Theme::i()->getTemplate( 'widgets', $this->app, 'front' ), $this->key ) );
		// If you are creating a plugin, uncomment this line:
		$this->template( array( \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' ), $this->key ) );
		// And then create your template at located at plugins/<your plugin>/dev/html/streamsblock.phtml

		if( !is_array( $this->configuration ) || empty($this->configuration ) )
		{
			$this->configuration = array(
				'tw_twitch_login'		=> 0,
				'tw_enable_offline'		=> 1,
				'tw_enable_featured'	=> 1,
				'tw_enable_title'		=> 1,
				'tw_featured_list'		=> array( 'dota2ti', 'tyrant_riut' ),
				'tw_ajax_time'			=> 15,
				'tw_offline_title'		=> 'No Streams Available'
				);
		}
		parent::init();
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

 		// $$form->add( new \IPS\Helpers\Form\XXXX( .... ) );
 		/* TWLM Switch */
 		$form->add( new \IPS\Helpers\Form\YesNo( 'tw_twitch_login', ( isset( $this->configuration['tw_twitch_login'] ) ? $this->configuration['tw_twitch_login'] : NULL), TRUE ) );
 		
 		/* Enable Offline Tab */
 		$form->add( new \IPS\Helpers\Form\YesNo( 'tw_enable_offline', ( isset( $this->configuration['tw_enable_offline'] ) ? $this->configuration['tw_enable_offline'] : NULL), TRUE ) );
 		
 		/* Enable Featured Tab */
 		$form->add( new \IPS\Helpers\Form\YesNo( 'tw_enable_featured', (isset( $this->configuration['tw_enable_featured'] ) ? $this->configuration['tw_enable_featured'] : NULL), TRUE ) );
 		
 		/* Enable Title */
 		$form->add( new \IPS\Helpers\Form\YesNo( 'tw_enable_title', (isset( $this->configuration['tw_enable_title'] ) ? $this->configuration['tw_enable_title'] : NULL ), TRUE ) );

 		/* Client ID */
 		$form->add( new \IPS\Helpers\Form\Text( 'tw_client_id', (isset( $this->configuration['tw_client_id'] ) ? $this->configuration['tw_client_id'] : NULL ), TRUE ) );
 		
 		/* Ajax Timer */
 		$form->add( new \IPS\Helpers\Form\Number( 'tw_ajax_time', $this->configuration['tw_ajax_time'], FALSE, array( 'min' => 15 ) ) );
 		
 		/* Block Offline Message */
 		$form->add( new \IPS\Helpers\Form\Text( 'tw_offline_title', (isset( $this->configuration['tw_offline_title'] ) ? $this->configuration['tw_offline_title'] : NULL ), TRUE ) );
 		
 		/* Games Limiter */
 		$form->add( new \IPS\Helpers\Form\Text( 'tw_games_limit', ( isset( $this->configuration['tw_games_limit'] ) ? $this->configuration['tw_games_limit'] : NULL) , FALSE, array( 'autocomplete' => array( 'freechoice' => TRUE, 'unique' => TRUE, 'forceLower' => TRUE, 'prefix' => FALSE ) ) ) );
 		
 		/* Featured List */
 		$form->add( new \IPS\Helpers\Form\Text( 'tw_featured_list', ( isset( $this->configuration['tw_featured_list'] ) ? $this->configuration['tw_featured_list'] : NULL ), FALSE, array( 'autocomplete' => array( ' freechoice' => TRUE, 'maxItems' => 5, 'unique' => TRUE, 'forceLower' => TRUE, 'prefix' => FALSE ) ) ) );

 		 /* Regular Streams List */
 		$form->add( new \IPS\Helpers\Form\Text( 'tw_streams_list', ( isset( $this->configuration['tw_streams_list'] ) ? $this->configuration['tw_streams_list'] : NULL) , FALSE, array( 'autocomplete' => array( 'freechoice' => TRUE, 'unique' => TRUE, 'forceLower' => TRUE, 'prefix' => FALSE ) ) ) );

 		/* Permissions */
 		$form->add( new \IPS\Helpers\Form\Select( 'tw_visible_to', $this->configuration['tw_visible_to'], FALSE, array('options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true, 'unlimited' => 'all', 'unlimitedLang' => 'all_groups' ), NULL, NULL, NULL, 'tw_visible_to' ) );

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
 		/* INIT */			
 		$str_indb = array();
 		$twitch = 'https://twitch.tv/';

 		/* Build Streamer List */
 		$twStreamers = $this->buildStreamList( $values );

		/* Users already in Database */
		foreach( \IPS\Db::i()->select( array('tw_name', 'tw_featStream'), 'tw_streams', 'tw_name IS NOT NULL' ) as $value )
		{
			$str_indb[] = $value['tw_name'];
		}

		/* Delete Users No Longer on Lists */
		foreach ($str_indb as $delete )
		{	
			/* Deletes any streamers no longer on lists */
			if( is_array( $twStreamers) )
			{
				if( isset($twStreamers['featured']) && isset($twStreamers['non-featured'] ) )
				{
					/* Deletes streamer if not on any of the lists */
					if( !in_array($delete, $twStreamers['featured'] ) && !in_array($delete, $twStreamers['non-featured'] ) )		
					{
						\IPS\Db::i()->delete('tw_streams', array( 'tw_name=?', $delete ) );
					}
					/* Updates a streamer if they are no longer a featured streamer */
					if( !in_array($delete, $twStreamers['featured'] ) && in_array($delete, $twStreamers['non-featured'] ) )
					{
						\IPS\Db::i()->update( 'tw_streams', array( 'tw_featStream' => 0 ), array( 'tw_name=?', $delete ) );
					}
					/* Updates a streamer in the database if they are placed as a featured streamer */
					if( in_array($delete, $twStreamers['featured'] ) && !in_array($delete, $twStreamers['non-featured'] ) )
					{
						\IPS\Db::i()->update( 'tw_streams', array( 'tw_featStream' => 1 ), array( 'tw_name=?', $delete ) );
					}
				}
			}
		}
		/* Prepares to insert users into the database (Non-featured) */
		if( is_array($twStreamers['non-featured'] ) )
		{
			foreach( $twStreamers['non-featured'] as $streamer )
			{
				if( !in_array($streamer, $str_indb) )
				{
					$query[] = array(
						'tw_name'		=> $streamer,
						'tw_url'		=> $twitch . $streamer,
						'tw_featStream'	=> 0
					);
				}
			}
		}
		/* Prepares to insert featured users into the database */
		if( is_array( $twStreamers['featured' ] ) )
		{
			foreach( $twStreamers['featured'] as $featStreamer )
			{
				if(!in_array($featStreamer, $str_indb) )
				{
					$query[] = array(
						'tw_name'		=> $featStreamer,
						'tw_url'		=> $twitch . $featStreamer,
						'tw_featStream'	=> 1
					);
				}
			}
		}
		if( isset( $query ) )
		{
			try
			{
				\IPS\Db::i()->insert( 'tw_streams', $query );

			}
			catch (\IPS\Db\Exception $e)
			{

			}
		}

 		return $values;
 	}

	/**
	 * Render a widget
	 *
	 * @return	string
	 */
	public function render()
	{
		/* New in version 2.0 (Ajax)*/
		$plugins = new \IPS\core\modules\front\system\plugins;

		$streamers = $plugins->refreshstreams( $this->configuration );

		if( $this->configuration['tw_visible_to'] != 'all' AND isset( $this->configuration['tw_visible_to'] ) and is_array( $this->configuration['tw_visible_to'] ) )
		{
			if( ! \IPS\Member::loggedIn()->inGroup( $this->configuration['tw_visible_to'] ) )
			{
				return '';
			}
		}
		if( count( $streamers ) )
		{
			return $this->output( $streamers, $this->configuration );
		}
		
		return '';
		// Use $this->output( $foo, $bar ); to return a string generated by the template set in init() or manually added via $widget->template( $callback );
		// Note you MUST route output through $this->output() rather than calling \IPS\Theme::i()->getTemplate() because of the way widgets are cached
	}

	public function buildStreamList( $values )
	{
		/* INIT */
		$twStreamers['featured'] = array();
		$twStreamers['non-featured'] = array();
		$customStreams = $values['tw_streams_list'];
		$featuredStreams = $values['tw_featured_list'];

 		/* Gather List of Users */
		if( $values['tw_twitch_login'] )
		{
			foreach( \IPS\Db::i()->select(array('tw_name', 'tw_id'), 'core_members', 'tw_name IS NOT NULL' ) as $user )
			{
				if( in_array( $user['tw_name'], $featuredStreams ) )
				{
					$twStreamers['featured'][] = $user['tw_name'];
				}
				else
				{
					$twStreamers['non-featured'][] = $user['tw_name'];
				}
			}
			if( is_array($featuredStreams ) )
			{
				foreach( $featuredStreams as $featuredStream )
				{
					if( !in_array( $featuredStream, $twStreamers ) )
					{
						$twStreamers['featured'][] = $featuredStream;
					}
				}
			}
			if( is_array($customStreams) )
			{
				foreach( $customStreams as $customStream )
				{
					if( !in_array($customStream, $twStreamers) )
					{
						$twStreamers['non-featured'][] = $customStream;
					} 
				}
			}

		} else {

			if( is_array( $customStreams ) )
			{
				foreach( $customStreams as $customStream )
				{
					$twStreamers['non-featured'][] = $customStream;
				}
			}
			if( is_array($featuredStreams ) )
			{
				foreach( $featuredStreams as $featuredStream )
				{
					if( !in_array( $featuredStream, $twStreamers ) )
					{
						$twStreamers['featured'][] = $featuredStream;
					}
				}
			}

		}
		return $twStreamers;
	}
}