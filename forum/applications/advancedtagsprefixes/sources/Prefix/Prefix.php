<?php

namespace IPS\advancedtagsprefixes;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Prefix Record
 */
class _Prefix extends \IPS\Patterns\ActiveRecord
{
	public static $databaseTable	= 'advancedtagsprefixes_prefixes';
	public static $databasePrefix	= 'prefix_';
	public static $databaseColumnId	= 'id';
	
	protected static $databaseIdFields = array( 'prefix_id', 'prefix_title' );
	
	protected static $multitons;
	
	public static $application		= 'advancedtagsprefixes';
	public static $module			= 'manage';
	
	protected static $all			= '*';
	
	/**
	 * Magic Method: To String
	 * Returns group name
	 *
	 * @return	string
	 */
	public function __toString()
	{
		return $this->title;
	}
	
	public function format()
	{
		return $this->pre . ( $this->showtitle ? $this->title : '' ) . $this->post;
	}
	
	/**
	 * Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function buildForm( &$form )
	{
		$form->attributes['data-controller'] = 'advancedtagsprefixes.admin.prefixes.editPrefix';
		
		$form->addTab('advancedtagsprefixes_prefix_settings');
		
		$form->add( new \IPS\Helpers\Form\Text( 'prefix_title',			$this->id ? $this->title : '', TRUE, array(), NULL, NULL, '<div id="atpPrefixPreview" class="ipsTags_inline"></div>', 'atpPrefixTitle' ) );
		$form->add( new \IPS\Helpers\Form\Text( 'prefix_pre',			$this->id ? $this->pre : '<span class="ipsBadge" style="background:red;">', FALSE, array(), NULL, NULL, NULL, 'atpPrefixFormattingPre' ) );
		$form->add( new \IPS\Helpers\Form\Text( 'prefix_post',			$this->id ? $this->post : '</span>', FALSE, array(), NULL, NULL, NULL, 'atpPrefixFormattingPost' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'prefix_showtitle',	$this->id ? $this->showtitle : TRUE, FALSE, array(), NULL, NULL, NULL, 'atpPrefixIncludeTitle' ) );
		$form->add( new \IPS\Helpers\Form\Select( 'prefix_groups',		$this->id ? $this->groups : static::$all, FALSE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => TRUE, 'unlimited' => static::$all, 'unlimitedLang' => 'all' ) ) );
	}
	
	/**
	 * Process create/edit form
	 *
	 * @param	array				$values	Values from form
	 * @return	void
	 */
	public function processForm( $values )
	{
		/**
		 * Save all the things!
		 * This is admin-only, so be cool.
		 */
		foreach( $values as $k => $v )
		{
			$k = str_replace( 'prefix_', '', $k );
			
			if( $this->$k != $v )
			{
				$this->$k = $v;
			}
		}
		
		if( \IPS\Settings::i()->tags_force_lower )
		{
			$this->title = mb_strtolower( $this->title );
		}
		
		\IPS\Application::load('advancedtagsprefixes')->flushPrefixCache();
	}
	
	/**
	 * Contextual permission check, fun stuffs
	 */
	public function canBeUsedFor( \IPS\Node\Model $container=NULL, $member=NULL )
	{
		/**
		 * Check group permissions
		 */
		if( $this->groups !== static::$all )
		{
			$member = \IPS\Member::loggedIn();
			
			if( !$member->inGroup( $this->groups ) )
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Group permissions pack/unpack
	 */
	public function get_groups()
	{
		if( !isset( $this->_data['groups'] ) or is_null( $this->_data['groups'] ) or $this->_data['groups'] == static::$all or $this->_data['groups'] == '' )
		{
			return static::$all;
		}
		
		return explode( ',', $this->_data['groups'] );
	}
	
	public function set_groups( $val )
	{
		if( is_array( $val ) )
		{
			$val = implode( ',', $val );
		}
		
		$this->_data['groups']		= $val;
		$this->changed['groups']	= $val;
	}
}
