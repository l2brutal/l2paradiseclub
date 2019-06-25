<?php


namespace IPS\advancedtagsprefixes\modules\admin\manage;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * tags
 */
class _tags extends \IPS\Dispatcher\Controller
{
	/**
	 * Tag data cache for rows
	 *
	 * @var array
	 */
	protected $tagCache = array();

	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tags_manage' );
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
			\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( sprintf( \IPS\Member::loggedIn()->language()->get('search_results_in_nodes'), mb_strtolower( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tags') ) ), 'information' );
		}
		
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tags_header'), 'info', NULL, FALSE, TRUE );
		
		$tagRows	= array();
		$tags		= \IPS\DB::i()->select( 'distinct binary tag_text as tag_text, tag_meta_app, tag_meta_area', 'core_tags', null, '`tag_text` ASC' );
		foreach( $tags as $tag )
		{
			$tagRows[] = $tag;
		}
		
		$table = new \IPS\Helpers\Table\Custom( $tagRows, \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tags' ) );
		$table->include			= array( 'tag_text', 'tag_meta_app', 'tag_meta_area', 'uses', 'last_use' );
		$table->quickSearch 	= 'tag_text';
		$table->langPrefix 		= 'advancedtagsprefixes_';
		$table->mainColumn 		= 'tag_text';
		$table->sortBy 			= $table->sortBy ?: 'tag_text';
		$table->sortDirection 	= $table->sortDirection ?: 'asc';
		$table->noSort			= array( 'uses', 'last_use' );
		$table->limit           = 50;
		
		$table->parsers = array(
			'uses'			=> function( $val, $row )
			{
				$where = array(
					array( 'binary tag_text=?', $row['tag_text'] ),
					array( 'tag_meta_app=?', $row['tag_meta_app'] ),
					array( 'tag_meta_area=?', $row['tag_meta_area'] ),
				);
				
				$counts = \IPS\DB::i()->select( 'count(tag_text) as uses, max(tag_added) as last_use', 'core_tags', $where, NULL, 1, 'tag_text' );
				$result = $counts->first();

                $key = $this->getTagKey( $row );
                $this->tagCache[ $key ] = $result;

                return $result['uses'];
			},
			'last_use'		=> function( $val, $row )
			{
				$key = $this->getTagKey( $row );
				if( isset( $this->tagCache[ $key ] ) ) {
					$val = $this->tagCache[ $key ]['last_use'];
				}
				
				$date	= \IPS\DateTime::ts( $val );
				
				return $date->localeDate() . ' ' . $date->localeTime( FALSE ) ;
			},
		);
		
		$table->rowButtons = function( $row )
		{
			$tableParams = '&sortby='.(\IPS\Request::i()->sortby)
							.'&sortdirection='.(\IPS\Request::i()->sortdirection)
							.'&page='.(\IPS\Request::i()->page)
							.'&quicksearch='.(\IPS\Request::i()->quicksearch);
			
			$return = array();
			
			$return['search'] = array(
				'icon'		=> 'search',
				'title'		=> 'search',
				'link'		=> \IPS\Http\Url::internal( "app=core&module=search&controller=search&tags=" . rawurlencode($row['tag_text']), 'front', 'tags' ),
			);
			
			if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tags_edit' ) )
			{
				$editLink = \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tags&do=edit&tag_text_from=' . rawurlencode($row['tag_text']) . $tableParams );
				
				if ( isset( \IPS\Request::i()->searchResult ) )
				{
					$editLink = $editLink->setQueryString( 'searchResult', \IPS\Request::i()->searchResult );
				}
				
				if( isset( $row['tag_meta_app'] ) )
				{
					$editLink = $editLink->setQueryString( 'tag_meta_app', $row['tag_meta_app'] );
				}
				
				if( isset( $row['tag_meta_area'] ) )
				{
					$editLink = $editLink->setQueryString( 'tag_meta_area', $row['tag_meta_area'] );
				}
				
				$return['edit'] = array(
					'icon'		=> 'pencil',
					'title'		=> 'edit',
					'link'		=> $editLink,
				);
			}
			
			if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tags_delete' ) )
			{
				$deleteLink = \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tags&do=delete&tag=' . rawurlencode($row['tag_text']) . $tableParams );
				
				if( isset( $row['tag_meta_app'] ) )
				{
					$deleteLink = $deleteLink->setQueryString( 'tag_meta_app', $row['tag_meta_app'] );
				}
				
				if( isset( $row['tag_meta_area'] ) )
				{
					$deleteLink = $deleteLink->setQueryString( 'tag_meta_area', $row['tag_meta_area'] );
				}
				
				$return['delete'] = array(
					'icon'		=> 'times-circle',
					'title'		=> 'delete',
					'link'		=> $deleteLink,
					'data'		=> array( 'delete' => '' ),
				);
			}
			
			return $return;
		};
		
		
		\IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_app_title');
		
		\IPS\Output::i()->title   = \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_manage_tags');
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'advancedtagsprefixes', (string) $table );
	}
	
	/**
	 * Add/Edit Prefix
	 *
	 * @return	void
	 */
	public function edit()
	{
		if( !isset( \IPS\Request::i()->tag_text_from ) or empty( \IPS\Request::i()->tag_text_from ) )
		{
			\IPS\Output::i()->error( 'node_error', '1P101/1', 404, '' );
		}
		
		\IPS\Request::i()->tag_text_from = rawurldecode( \IPS\Request::i()->tag_text_from );
		
		\IPS\Dispatcher::i()->checkAcpPermission( 'tags_edit' );
		
		
		/* Build form */
		$form = new \IPS\Helpers\Form('advancedtagsprefixes_tags_edit');
		
		$form->attributes['data-controller'] = 'advancedtagsprefixes.admin.tags.editTag';
		$form->hiddenValues['tag_text_from'] = \IPS\Request::i()->tag_text_from;
		$form->hiddenValues['tag_meta_app']  = \IPS\Request::i()->tag_meta_app;
		$form->hiddenValues['tag_meta_area'] = \IPS\Request::i()->tag_meta_area;
		$form->hiddenValues['sortby'] = \IPS\Request::i()->sortby;
		$form->hiddenValues['sortdirection'] = \IPS\Request::i()->sortdirection;
		$form->hiddenValues['page'] = \IPS\Request::i()->page;
		$form->hiddenValues['quicksearch'] = \IPS\Request::i()->quicksearch;
		
		$form->add( new \IPS\Helpers\Form\Text( 'tag_text_to', \IPS\Request::i()->tag_text_from, TRUE, array(), NULL, NULL, NULL, 'atpTagTextTo' ) );
		
		
		/* Handle submission */
		if ( $values = $form->values() )
		{
			try
			{
				$tagTextFrom	= \IPS\Request::i()->tag_text_from;
				$tagTextTo		= \IPS\Request::i()->tag_text_to;
				$tagMetaApp		= \IPS\Request::i()->tag_meta_app ?: NULL;
				$tagMetaArea	= \IPS\Request::i()->tag_meta_area ?: NULL;
				
				if( empty( $tagTextFrom ) )
				{
					throw new \OutOfRangeException;
				}
				elseif( empty( $tagTextTo ) )
				{
					throw new \OutOfRangeException;
				}
				
				$affectedCount = \IPS\Application::load('advancedtagsprefixes')->updateTags( $tagTextFrom, $tagTextTo, $tagMetaApp, $tagMetaArea );
				
				\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tags_updated', array( $tagTextFrom => FALSE, $tagTextTo => FALSE, $affectedCount => FALSE ) );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '1P101/2', 404, '' );
			}
			
			$tableParams = '&sortby='.(\IPS\Request::i()->sortby)
							.'&sortdirection='.(\IPS\Request::i()->sortdirection)
							.'&page='.(\IPS\Request::i()->page)
							.'&quicksearch='.(\IPS\Request::i()->quicksearch);
			
			/* And redirect */
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tags' . $tableParams ), 'saved' );
		}
		
		/* Display */
		\IPS\Output::i()->jsFiles		= array_merge( \IPS\Output::i()->jsFiles, \IPS\Output::i()->js( 'admin_tags.js', 'advancedtagsprefixes', 'admin' ) );
		\IPS\Output::i()->title			= \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tags_edit');
		\IPS\Output::i()->breadcrumb[]	= array( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tags' ), \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_manage_tags') );
		\IPS\Output::i()->breadcrumb[]	= array( NULL, \IPS\Output::i()->title );
		\IPS\Output::i()->output		= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
	}
	
	/**
	 * Delete tags
	 *
	 * @return	void
	 */
	public function delete()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tags_delete' );
		
		try
		{
			$tag			= rawurldecode( \IPS\Request::i()->tag );
			$affectedCount	= \IPS\Application::load('advancedtagsprefixes')->deleteTags( $tag );
			
			\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tags_deleted', array( $tag => FALSE, $affectedCount => FALSE ) );
		}
		catch ( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'node_error', '2P101/3', 404, '' );
		}
		
		$tableParams = '&sortby='.(\IPS\Request::i()->sortby)
						.'&sortdirection='.(\IPS\Request::i()->sortdirection)
						.'&page='.(\IPS\Request::i()->page)
						.'&quicksearch='.(\IPS\Request::i()->quicksearch);
		
		/* And redirect */
		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=advancedtagsprefixes&module=manage&controller=tags" . $tableParams ) );
	}
	
	/**
	 * Get a unique key for the given tag info.
	 *
	 * @param  array $row
	 * @return string
	 */
	protected function getTagKey( $row )
	{
		return md5( $row['tag_text'] . '-' . $row['tag_meta_app'] . '-' . $row['tag_meta_area'] );
	}
	
	// Create new methods with the same name as the 'do' parameter which should execute it
}