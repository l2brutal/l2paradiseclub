<?php
/**
 * @brief		Categories
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	Downloads
 * @since		27 Sep 2013
 */

namespace IPS\downloads\modules\admin\downloads;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Categories
 */
class _categories extends \IPS\Node\Controller
{
	/**
	 * Node Class
	 */
	protected $nodeClass = 'IPS\downloads\Category';
	
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'categories_manage' );
		parent::execute();
	}
	
	/**
	 * Recalculate Downloads
	 *
	 * @return	void
	 */
	protected function recountDownloads()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'categories_recount_downloads' );		
	
		try
		{
			$category = \IPS\downloads\Category::load( \IPS\Request::i()->id );
			
			\IPS\Db::i()->update( 'downloads_files', array( 'file_downloads' => \IPS\Db::i()->select( 'COUNT(*)', 'downloads_downloads', array( 'dfid=file_id' ) ) ), array( 'file_cat=?', $category->id ) );
			\IPS\Session::i()->log( 'acplogs__downloads_recount_downloads', array( $category->_title => FALSE ) );
		
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=downloads&module=downloads&controller=categories&do=form&id=" . \IPS\Request::i()->id ), 'clog_recount_done' );
		}
		catch ( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'node_error', '2D180/1', 404, '' );
		}
	}

	protected function form()
	{
		parent::form();

		if ( \IPS\Request::i()->id )
		{
			\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('edit_category')  . ': ' . \IPS\Output::i()->title;
		}
		else
		{
			\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('add_category');
		}
	}
}