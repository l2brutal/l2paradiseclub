<?php


namespace IPS\advancedtagsprefixes\modules\admin\manage;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * prefixes
 */
class _prefixes extends \IPS\Dispatcher\Controller
{	
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'prefixes_manage' );
		parent::execute();
	}
	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	public function manage()
	{
		if ( isset( \IPS\Request::i()->searchResult ) )
		{
			\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( sprintf( \IPS\Member::loggedIn()->language()->get('search_results_in_nodes'), mb_strtolower( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_prefixes') ) ), 'information' );
		}
		
		$table = new \IPS\Helpers\Table\Db( 'advancedtagsprefixes_prefixes', \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes' ) );
		$table->include 		= array( 'prefix_title', 'prefix_formatted', 'prefix_groups' );
		$table->quickSearch 	= array( 'prefix_title', 'prefix_title' );
		$table->langPrefix 		= 'advancedtagsprefixes_';
		$table->mainColumn 		= 'prefix_title';
		$table->sortBy 			= $table->sortBy ?: 'prefix_title';
		$table->sortDirection 	= $table->sortDirection ?: 'asc';
		
		$table->parsers = array(
			'prefix_formatted'		=> function( $val, $row )
			{
				$prefix = \IPS\advancedtagsprefixes\Prefix::constructFromData( $row );
				
				return $prefix->format();
			},
			'prefix_groups'	=> function( $val, $row )
			{
				if( !empty( $val ) and $val != '*' )
				{
					$vals = explode( ',', $val );
					
					return count( $vals );
				}
				
				return 'All';
			},
		);
		
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'prefixes_add' ) )
		{
			$table->rootButtons = array(
				'add'	=> array(
					'icon'		=> 'plus',
					'title'		=> 'advancedtagsprefixes_add_new_prefix',
					'link'		=> \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes&do=form' ),
				)
			);
		}
		
		$table->rowButtons = function( $row )
		{
			$return = array();
			
			if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'prefixes_edit' ) )
			{
				$editLink = \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes&do=form&id=' . $row['prefix_id'] );
				
				if ( isset( \IPS\Request::i()->searchResult ) )
				{
					$editLink = $editLink->setQueryString( 'searchResult', \IPS\Request::i()->searchResult );
				}
				
				$return['edit'] = array(
					'icon'		=> 'pencil',
					'title'		=> 'edit',
					'link'		=> $editLink,
				);
			}
			
			if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'prefixes_add' ) )
			{
				$return['copy'] = array(
					'icon'		=> 'files-o',
					'title'		=> 'copy',
					'link'		=> \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes&do=copy&id=' ) . $row['prefix_id'],
				);
			}
			
			if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'prefixes_delete' ) )
			{
				$return['delete'] = array(
					'icon'		=> 'times-circle',
					'title'		=> 'delete',
					'link'		=> \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes&do=delete&id=' ) . $row['prefix_id'],
					'data'		=> array( 'delete' => '' ),
				);
			}
			
			return $return;
		};
		
		
		\IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_app_title');
		
		\IPS\Output::i()->title   = \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_manage_prefixes');
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'advancedtagsprefixes', (string) $table );
	}
	
	/**
	 * Add/Edit Prefix
	 *
	 * @return	void
	 */
	public function form()
	{
		/* Load group */
		try
		{
			$prefix = \IPS\advancedtagsprefixes\Prefix::load( \IPS\Request::i()->id );
			
			\IPS\Dispatcher::i()->checkAcpPermission( 'prefixes_edit' );
		}
		catch ( \OutOfRangeException $e )
		{
			$prefix = new \IPS\advancedtagsprefixes\Prefix;
			
			\IPS\Dispatcher::i()->checkAcpPermission( 'prefixes_add' );
		}
		
		/* Build form */
		$form = new \IPS\Helpers\Form( ( !$prefix->id ? 'advancedtagsprefixes_add_new_prefix' : $prefix->id ) );
		
		$prefix->buildForm( $form );
		
		/* Handle submission */
		if ( $values = $form->values() )
		{
			$new = ( !$prefix->id ) ? TRUE : FALSE;
			
			$prefix->processForm( $values );
			$prefix->save();
			
			\IPS\Session::i()->log( ( $new ) ? 'acplogs__advancedtagsprefixes_prefix_created' : 'acplogs__advancedtagsprefixes_prefix_edited', array( $prefix->title => FALSE ) );
			
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes' ), 'saved' );
		}
		
		/* Display */
		\IPS\Output::i()->jsFiles = array_merge( \IPS\Output::i()->jsFiles, \IPS\Output::i()->js( 'admin_prefixes.js', 'advancedtagsprefixes', 'admin' ) );
		\IPS\Output::i()->title	 		= ( $prefix->id ? $prefix->title : \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_add_new_prefix') );
		\IPS\Output::i()->breadcrumb[]	= array( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=prefixes' ), \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_manage_prefixes') );
		\IPS\Output::i()->breadcrumb[]	= array( NULL, \IPS\Output::i()->title );
		\IPS\Output::i()->output 		= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
	}
	
	/**
	 * Copy Prefix
	 *
	 * @return	void
	 */
	public function copy()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'prefixes_add' );
	
		/* Load Prefix */
		try
		{
			$prefix = \IPS\advancedtagsprefixes\Prefix::load( \IPS\Request::i()->id );
		}
		catch ( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'node_error', '2P100/1', 404, '' );
		}
		
		/* Copy */
		$newPrefix = clone $prefix;
		\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_prefix_copied', array( $newPrefix->title => FALSE ) );
		
		/* And redirect */
		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=advancedtagsprefixes&module=manage&controller=prefixes&do=form&id={$newPrefix->id}" ) );
	}
	
	/**
	 * Delete Prefix
	 *
	 * @return	void
	 */
	public function delete()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'prefixes_delete' );
		
		/* Load group */
		try
		{
			$prefix = \IPS\advancedtagsprefixes\Prefix::load( \IPS\Request::i()->id );
			\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_prefix_deleted', array( $prefix->title => FALSE ) );
			$prefix->delete();
			
			\IPS\Application::load('advancedtagsprefixes')->flushPrefixCache();
		}
		catch ( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'node_error', '2P100/2', 404, '' );
		}
		
		/* And redirect */
		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=advancedtagsprefixes&module=manage&controller=prefixes" ) );
	}
}