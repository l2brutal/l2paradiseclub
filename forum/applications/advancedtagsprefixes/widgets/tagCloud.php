<?php
/**
 * @brief		tagCloud Widget
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	advancedtagsprefixes
 * @since		18 Oct 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\advancedtagsprefixes\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * tagCloud Widget
 */
class _tagCloud extends \IPS\Widget\StaticCache
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'tagCloud';
	
	/**
	 * @brief	App
	 */
	public $app = 'advancedtagsprefixes';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '';
	
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
		
		/**
		 * Settings
		 * 
		 * Select: Tag vs. prefix vs. both
		 * Select: Source node
		 * Minimum uses
		 * Max displayed
		 * Days back to include
		 */
		
		$cloudModes		= array(
			'tag'		=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_cloudmode_tag'),
			'prefix'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_cloudmode_prefix'),
		);
		
		$displayModes	= array(
			'cloud'		=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_displaymode_cloud'),
			'list'		=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_displaymode_list'),
		);
		
		// Load possible node types from the tags table. Yes, this will mean options won't be available until tags are created in them.
		$nodeTypes		= array();
		foreach ( \IPS\Db::i()->select( 'tag_meta_app, tag_meta_area', 'core_tags', NULL, NULL, NULL, array( 'tag_meta_app', 'tag_meta_area' ) ) as $node )
		{
			$nodeTypes[ $node['tag_meta_app'] . ':' . $node['tag_meta_area'] ] = ucfirst( $node['tag_meta_app'] ) . ': ' . ucfirst( $node['tag_meta_area'] );
		}
		
		$form->add( new \IPS\Helpers\Form\Text( 'block_title', isset( $this->configuration['block_title'] ) ? $this->configuration['block_title'] : \IPS\Member::loggedIn()->language()->addToStack('block_tagCloud'), TRUE ) );
		
		$form->add( new \IPS\Helpers\Form\Select( 'display_mode', isset( $this->configuration['display_mode'] ) ? $this->configuration['display_mode'] : 'cloud', FALSE, array( 'options' => $displayModes ) ) );
		$form->add( new \IPS\Helpers\Form\Select( 'cloud_mode', isset( $this->configuration['cloud_mode'] ) ? $this->configuration['cloud_mode'] : 'all', FALSE, array( 'options' => $cloudModes, 'unlimited' => 'all', 'unlimitedLang' => 'all' ) ) );
		$form->add( new \IPS\Helpers\Form\Select( 'nodes',      isset( $this->configuration['nodes'] )      ? $this->configuration['nodes']      : 'all', FALSE, array( 'options' => $nodeTypes, 'unlimited' => 'all', 'unlimitedLang' => 'all' ) ) );
		
		$form->add( new \IPS\Helpers\Form\Number( 'min_instances',  isset( $this->configuration['min_instances'] )  ? $this->configuration['min_instances']  :  5, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Number( 'number_to_show', isset( $this->configuration['number_to_show'] ) ? $this->configuration['number_to_show'] : 25, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Number( 'history_threshold', isset( $this->configuration['history_threshold'] ) ? $this->configuration['history_threshold'] : '-1', TRUE, array( 'unlimited' => '-1', 'unlimitedLang' => 'all', 'endSuffix' => 'days' ) ) );
		
		$form->add( new \IPS\Helpers\Form\YesNo( 'format_prefixes', isset( $this->configuration['format_prefixes'] ) ? $this->configuration['format_prefixes'] : 0, FALSE ) );
		
		$form->add( new \IPS\Helpers\Form\Text( 'exclude_tags', isset( $this->configuration['exclude_tags'] ) ? $this->configuration['exclude_tags'] : '', FALSE, array( 'autocomplete' => array( 'unique' => 'true', 'freeChoice' => TRUE ) ) ) );
		
		return $form;
	} 
	
	/**
	 * Render the widget
	 *
	 * @return	string
	 */
	public function render()
	{
		if ( !isset( $this->configuration['nodes'] ) )
		{
			return '';
		}
		
		$displayMode = isset( $this->configuration['display_mode'] ) ? $this->configuration['display_mode'] : 'cloud';
		
		/**
		 * Search for matching tags
		 */
		$where		= array();
		$searchKey	= NULL;
		
		if ( $this->configuration['nodes'] != 'all' )
		{
			$node		= explode( ':', $this->configuration['nodes'] );
			$where[]	= array( 'tag_meta_app=? and tag_meta_area=?', $node[0], $node[1] );
			
			/**
			 * Try to determine the content type for search routing... this should not be so difficult.
			 */
			$className	= '\\IPS\\' . $node[0] . '\\' . ucfirst( $node[1] );
			if( class_exists( $className ) ) {
				$searchKey = mb_strtolower( str_replace( '\\', '_', mb_substr( $className::$contentItemClass, 4 ) ) );
			}
		}
		
		if ( $this->configuration['history_threshold'] > 0 )
		{
			$where[]	= array( 'tag_added>?', time() - ( (int)$this->configuration['history_threshold'] * 86400 ) );
		}
		
		if ( $this->configuration['cloud_mode'] == 'tag' )
		{
			$where[]	= array( 'tag_prefix=0' );
		}
		else if ( $this->configuration['cloud_mode'] == 'prefix' )
		{
			$where[]	= array( 'tag_prefix=1' );
		}
		
		if( isset( $this->configuration['exclude_tags'] ) )
		{
			if( !is_array( $this->configuration['exclude_tags'] ) )
			{
				$this->configuration['exclude_tags'] = explode( ',', $this->configuration['exclude_tags'] );
			}
			
			foreach( $this->configuration['exclude_tags'] as $k => $v )
			{
				$this->configuration['exclude_tags'][ $k ] = preg_replace( '/[^\w\s]/', '', $v );
			}
			
			$this->configuration['exclude_tags'] = array_filter( $this->configuration['exclude_tags'] );
			
			$where[]	= array( "binary tag_text not in ('" . implode( "','", $this->configuration['exclude_tags'] ) . "')" );
		}
		
		if ( count( $where ) < 1 )
		{
			$where = NULL;
		}
		
		$select	= \IPS\Db::i()->select(
			'tag_text, count(tag_text) as count', 'core_tags',
			$where,
			'count(tag_text) DESC',
			$this->configuration['number_to_show'],
			'tag_text',
			array( 'count(tag_text) > ?', (int)$this->configuration['min_instances'] )
		);
		
		/**
		 * Build and output
		 */
		if ( count( $select ) )
		{
			$min	= 99999;
			$max	= 0;
			$tags	= array();
			
			$app	= \IPS\Application::load('advancedtagsprefixes');
			
			// Data-set pass
			foreach ( $select as $tag )
			{
				$tags[ $tag['tag_text'] ] = array(
					'label'		=> $tag['tag_text'],
					'count'		=> $tag['count'],
					'tag_text'	=> $tag['tag_text'],
				);
				
				// Add prefix formatting if enabled
				if ( isset( $this->configuration['format_prefixes'] ) and $this->configuration['format_prefixes'] ) {
					$prefix = $app->getPrefixByTitle( $tag['tag_text'] );
					if ( $prefix !== FALSE ) {
						$tags[ $tag['tag_text'] ]['label'] = $prefix->pre . ( $prefix->showtitle ? $tag['tag_text'] : '' ) . $prefix->post;
					}
				}
				
				$min = $tag['count'] < $min ? $tag['count'] : $min;
				$max = $tag['count'] > $max ? $tag['count'] : $max;
			}
			
			// Sorting pass
			if( $displayMode != 'list' ) {
				uasort( $tags, array( $this, '_sortTagListAlpha' ) );
			}
			
			// Scaling pass: Scale popularity 0-1
			$interval = $max > $min ? $max - $min : 1;
			foreach ( $tags as $tag => $data )
			{
				$tags[ $tag ]['scale'] = ( $data['count'] - $min ) / $interval;
			}
			
			return $this->output( $this->configuration['block_title'], $tags, $searchKey, $displayMode );
		}
		else
		{
			return '';
		}
	}
	
	protected function _sortTagListAlpha( $a, $b )
	{
		return strnatcasecmp( $a['tag_text'], $b['tag_text'] );
	}
}
