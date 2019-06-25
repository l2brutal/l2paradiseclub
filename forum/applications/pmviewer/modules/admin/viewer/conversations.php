<?php


namespace IPS\pmviewer\modules\admin\viewer;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * conversations
 */
class _conversations extends \IPS\Dispatcher\Controller
{	
	/**
	 * Conversations list
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'conversations_manage' );
		$hidden = \IPS\Member::loggedIn()->group['g_pmviewer_viewhidden'] ? 'mt_is_hidden IN( 0,1 )' : 'mt_is_hidden = 0';
		$system = \IPS\Settings::i()->pmviewer_hiddenmessages ? ' AND mt_is_system = 0' : ' AND mt_is_system IN( 0,1 )';

		$page = (intval(\IPS\Request::i()->page) > 0) ? intval(\IPS\Request::i()->page) : 1; // +
		$perPage = \IPS\Settings::i()->pmviewer_messagesperpage; // +

		/* Get iterator */
		$iterator	= \IPS\Db::i()->select(
				'core_message_topic_user_map.*, core_message_topics.*, core_members.*',
				'core_message_topic_user_map',
				 $hidden . $system,
				'mt_last_post_time DESC',
				// NULL // -
				array((($page - 1) * $perPage), $perPage), // +
				NULL, // +
				NULL, // +
				\IPS\Db::SELECT_SQL_CALC_FOUND_ROWS // +
			)->join(
				'core_message_topics',
				'core_message_topic_user_map.map_topic_id=core_message_topics.mt_id AND core_message_topics.mt_starter_id <> core_message_topic_user_map.map_user_id AND core_message_topic_user_map.map_user_active=1'
		)->join(
				'core_members',
				'core_message_topic_user_map.map_user_id=core_members.member_id'
		);

		/* Build the message list */
		$conversations = array();

		foreach ( $iterator as $row )
		{
			$conversation = \IPS\core\Messenger\Conversation::load( $row['mt_id'] );

			foreach( $conversation->maps() as $map )
			{
				$user = \IPS\Member::load( $map['map_user_id'] );

				if( $user->group['g_pmviewer_protectedgroup'] )
				{
					continue 2;
				}

				$conversations[ $row['mt_id'] ] = $row;
			}
		}

		/* Create the table */
		$table = new \IPS\Helpers\Table\Custom( $conversations, \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations' ) );

		$pages = ceil($iterator->count(TRUE) / $perPage); // +
		$table->pages = $pages; // +
		
		/* Column stuff */
		$table->include = array( 'mt_starter_id', 'mt_title', 'map_user_id', 'mt_replies', 'mt_last_post_time' );

		$table->mainColumn = 'mt_last_post_time';
		$table->widths = array( 'mt_starter_id' => '18', 'mt_title' => '30', 'mt_to_member_id' => 18, 'mt_date' => '15', 'mt_replies' => '7' );

		/* Sort stuff */
		$table->sortBy = $table->sortBy ?: 'mt_date';
		$table->sortDirection = $table->sortDirection ?: 'desc';

		/* Search */
		$table->quickSearch = array( array( 'name', \IPS\Member::load(  $row['mt_starter_id']->name ) ), 'mt_starter_id' );

		$table->parsers = array(
			'mt_title' => function( $val, $row )
			{
				$system = '';
				$hidden = '';

				if ( $row['mt_is_system'] == 1 )
				{
					$system = '<span class="ipsBadge ipsBadge_style6">' . \IPS\Member::loggedIn()->language()->addToStack('mt_is_system').'</span> ';
				}

				if ( $row['mt_is_hidden'] == 1 )
				{
					$GLOBALS['hidden'] = 1;
					$hidden = '<span class="ipsBadge ipsBadge_style2">' . \IPS\Member::loggedIn()->language()->addToStack('mt_filter_hidden').'</span> ';
				}
				else
				{
					$GLOBALS['hidden'] = 0;
				}

				$GLOBALS['id'] = $row['mt_id'];
				return $system . $hidden . '<a href="' .\IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=view&id=' . $row['mt_id'] ) . '">'.$row['mt_title'].'</a>';
			},
			'mt_last_post_time'	=> function( $val, $row )
			{
				$date	= \IPS\DateTime::ts( $val );

				return $date->localeDate() . ' ' . $date->localeTime( FALSE );
			},
			'mt_starter_id' => function( $val, $row )
			{
				$member = \IPS\Member::load( $val );
				return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( $member, 'tiny' ) . '  ' . "<a href='" . \IPS\Http\Url::internal( 'app=core&module=members&controller=members&do=edit&id=' ) . $val . "'>" . $member->name . "</a>";
			},
			'mt_id' => function( $val, $row )
			{
				return $val;
			},
			'map_user_id' => function( $val, $row )
			{
				if( $row['mt_to_count'] == 2 )
				{
					$member2 = \IPS\Member::load( $val );
					return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( $member2, 'tiny' ) . '  ' . "<a href='" . \IPS\Http\Url::internal( 'app=core&module=members&controller=members&do=edit&id=' ) . $val . "'>" . $member2->name . "</a>";
				}
				else
				{
					return \IPS\Member::loggedIn()->language()->addToStack( 'messenger_participants', FALSE, array( 'pluralize' => array( $row['mt_to_count'] - 1 ) ) );
				}
			},
		);

		/* Row buttons */
		$table->rowButtons = function( $row ) use ( $id )
		{
			$return = array();
		
			$return['view'] = array(
						'icon'		=> 'search',
						'title'		=> 'view',
						'link'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=view&id=' . $GLOBALS['id'] )
			);

			if( \IPS\Member::loggedIn()->group['g_pmviewer_hideunhide'] )
			{
				if( $GLOBALS['hidden'] == 1 )
				{
					$return['unhide'] = array(
								'icon'		=> 'eye',
								'title'		=> 'unhide',
								'link'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=unhide&from=list&id=' ) . $GLOBALS['id'],
								'data'		=> array( 'confirm' => '' ),
					);
				}
				else
				{
					$return['hide'] = array(
								'icon'		=> 'eye-slash',
								'title'		=> 'hide',
								'link'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=hide&from=list&id=' ) . $GLOBALS['id'],
								'data'		=> array( 'confirm' => '' ),
					);
				}
			}
		
			return $return;
		};

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__pmviewer_viewer_conversations');

		/* Display */
		\IPS\Output::i()->output	= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}

	/**
	 * View conversations
	 *
	 * @return	void
	 */
	protected function view()
	{
		/* Are we looking at a message? */
		$conversation 	= NULL;
		$protected 		= 0;

		if ( \IPS\Request::i()->id )
		{
			try
			{
				$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '2C137/2', 403, '' );
			}
		}

		foreach( $conversation->maps() as $map )
		{
			$member = \IPS\Member::load( $map['map_user_id'] );

			if( $member->group['g_pmviewer_protectedgroup'] )
			{
				$protected = 1;
			}
		}
		
		if ( $protected )
		{
			\IPS\Output::i()->error( 'pmviewer_protected_conversation', '2C137/2', 403, '' );	
		}

		if( !\IPS\Member::loggedIn()->group['g_pmviewer_viewhidden'] )
		{
			\IPS\Output::i()->error( 'pmviewer_cant_view_hidden', '2C137/2', 403, '' );
		}

		\IPS\Output::i()->title = $conversation->is_hidden ? \IPS\Member::loggedIn()->language()->addToStack('mt_filter_hidden') . ' ' . \IPS\Member::loggedIn()->language()->addToStack('mt_conversation') . ': ' . $conversation->title : \IPS\Member::loggedIn()->language()->addToStack('mt_conversation') . ': ' . $conversation->title;

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_view_conversation', array( $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		/* Display */
		\IPS\Output::i()->output	= \IPS\Theme::i()->getTemplate( 'view' )->viewConversation( $conversation );
	}

	/**
	 * Join conversations
	 *
	 * @return	void
	 */
	protected function join()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_managemembers'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		if ( \IPS\Request::i()->id )
		{
			try
			{
				$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '2C137/2', 403, '' );
			}
		}

		$form = new \IPS\Helpers\Form( 'form', 'mt_join_button' );
		$form->class = 'ipsForm_vertical ipsPad';
		$form->add( new \IPS\Helpers\Form\Member( 'mt_member_id', \IPS\Member::loggedIn()->name, TRUE, array() ) );

		if ( $values = $form->values() )
		{
			$conversation->authorize( $values['mt_member_id'] );

			/* Admin log */
			\IPS\Session::i()->log( 'pmviewer_join_conversation', array( $values['mt_member_id']->name => TRUE, $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $conversation->id ), 'mt_member_joined' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('mt_join_desc');
		\IPS\Output::i()->output = $form;		
	}

	/**
	 * Hide conversations
	 *
	 * @return	void
	 */
	protected function hide()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_hideunhide'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		if ( \IPS\Request::i()->id )
		{
			try
			{
				$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '2C137/2', 403, '' );
			}
		}

		\IPS\Db::i()->update( 'core_message_topics', array( 'mt_is_hidden' => 1 ), 'mt_id=' . $conversation->id );

		if( isset( \IPS\Request::i()->from ) AND \IPS\Request::i()->from == 'list' )
		{
			$url = \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations" );
		}
		else
		{
			$url = \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $conversation->id );	
		}

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_hide_conversation', array( $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		\IPS\Output::i()->redirect( $url, 'mt_conversation_hidden' );
	}

	/**
	 * Unhide conversations
	 *
	 * @return	void
	 */
	protected function unhide()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_hideunhide'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		if ( \IPS\Request::i()->id )
		{
			try
			{
				$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '2C137/2', 403, '' );
			}
		}

		\IPS\Db::i()->update( 'core_message_topics', array( 'mt_is_hidden' => 0 ), 'mt_id=' . $conversation->id );

		if( isset( \IPS\Request::i()->from ) AND \IPS\Request::i()->from == 'list' )
		{
			$url = \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations" );
		}
		else
		{
			$url = \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $conversation->id );	
		}

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_unhide_conversation', array( $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		\IPS\Output::i()->redirect( $url, 'mt_conversation_unhidden' );
	}

	/**
	 * Edit post
	 *
	 * @return	void
	 */
	protected function edit()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_editposts'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$post = \IPS\DB::i()->select( '*', 'core_message_posts',  array( 'msg_id=?', \IPS\Request::i()->id ) )->first();

		$form = new \IPS\Helpers\Form( 'form', 'edit' );
		$form->class = 'ipsForm_vertical ipsPad';
		$form->add( new \IPS\Helpers\Form\Editor( 'pmviewer_msg', $post['msg_post'], TRUE, array( 'app' => 'core', 'key' => 'Messaging', 'autoSaveKey' => 'acp-pmviewer-msgid-' . \IPS\Request::i()->id ) ) );

		if ( $values = $form->values() )
		{
			$toLog = array(
				'eh_member_id'			=> \IPS\Member::loggedIn()->member_id,
				'eh_conversation_id'	=> $post['msg_topic_id'],
				'eh_post_id'			=> $post['msg_id'],
				'eh_edit_time'			=> time(),
				'eh_original_text'		=> $post['msg_post'],
				'eh_modified_text'		=> ""
			);

			$id = \IPS\Db::i()->insert( "pmviewer_edithistory", $toLog );

			\IPS\Db::i()->update( 'core_message_posts', array( 'msg_post' => $values['pmviewer_msg'], 'msg_edited_pmviewer' => 1 ), 'msg_id=' . \IPS\Request::i()->id );
			\IPS\Db::i()->update( 'pmviewer_edithistory', array( 'eh_modified_text' => $values['pmviewer_msg'] ), 'eh_id=' . $id );

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $post['msg_topic_id'] ), 'mt_post_edited' );
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('mt_edit_msg');
		\IPS\Output::i()->output = $form;
	}

	/**
	 * Remove participant
	 *
	 * @return	void
	 */
	protected function block()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_managemembers'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id OR !\IPS\Request::i()->mid )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
		$member = \IPS\Member::load( \IPS\Request::i()->mid );
		$conversation->deauthorize( $member, TRUE );
		\IPS\core\Messenger\Conversation::rebuildMessageCounts( $member );

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_block_conversation', array( $member->name => TRUE, $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . \IPS\Request::i()->id ), 'pmviewer_member_removed' );
	}

	/**
	 * Add back the participant
	 *
	 * @return	void
	 */
	protected function unblock()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_managemembers'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id OR !\IPS\Request::i()->mid )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$conversation = \IPS\core\Messenger\Conversation::load( \IPS\Request::i()->id );
		$member = \IPS\Member::load( \IPS\Request::i()->mid );
		$conversation->authorize( $member, TRUE );
		\IPS\core\Messenger\Conversation::rebuildMessageCounts( $member );

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_unblock_conversation', array( $member->name => TRUE, $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . \IPS\Request::i()->id ), 'mt_member_joined' );
	}

	/**
	 * Show revisions of a post in a conversation
	 *
	 * @return	void
	 */
	protected function revisions()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_editposts'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$post = \IPS\DB::i()->select( '*', 'pmviewer_edithistory',  array( 'eh_post_id=?', \IPS\Request::i()->id ) )->first();

		require_once \IPS\ROOT_PATH . "/system/3rd_party/Diff/class.Diff.php";

		$table = new \IPS\Helpers\Table\Db( 'pmviewer_edithistory', \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=revisions&id=" . \IPS\Request::i()->id ), array( 'eh_post_id=?', \IPS\Request::i()->id ) );

		$table->joins = array(
			array(
				'select'	=> 'core_message_posts.*',
				'from'		=> 'core_message_posts',
				'where'		=> 'core_message_posts.msg_id=pmviewer_edithistory.eh_post_id'
			),
			array(
				'select'	=> 'core_members.*',
				'from'		=> 'core_members',
				'where'		=> 'core_members.member_id=pmviewer_edithistory.eh_member_id'
			)
		);

		$table->title 		= \IPS\Member::loggedIn()->language()->addToStack('pmviewer_revisions');
		$table->include 	= array( 'eh_member_id', 'eh_edit_time', 'eh_original_text', 'eh_modified_text' );
		$table->mainColumn 	= 'eh_edit_time';
		$table->noSort		= array( 'eh_member_id', 'eh_edit_time', 'eh_original_text', 'eh_modified_text' );
		$table->limit		= 3;

		$table->sortBy = $table->sortBy ?: 'eh_edit_time';
		$table->sortDirection = $table->sortDirection ?: 'desc';

		/* Parsers */
		$table->parsers = array(
			'eh_member_id' => function( $val, $row )
			{
				$member = \IPS\Member::load( $val );
				return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( $member, 'tiny' ) . '  ' . "<a href='" . \IPS\Http\Url::internal( 'app=core&module=members&controller=members&do=edit&id=' ) . $row['eh_member_id'] . "'>" . $row['name'] . "</a>";
			},
			'eh_edit_time' => function( $val )
			{
				$date	= \IPS\DateTime::ts( $val );

				return $date->localeDate() . ' ' . $date->localeTime( FALSE );
			},
			'eh_original_text' => function( $val, $row )
			{
				return $val;
			},
			'eh_modified_text' => function( $val, $row )
			{
				return $val;
			}
		);

		$table->widths = array( 'eh_member_id' => '20', 'eh_edit_time' => '15', 'eh_original_text' => '28', 'eh_modified_text' => '28' );

		$table->rowButtons = function( $row )
		{
			$return = array();

			/* There isn't a separate permission option because literally the only thing you can do with email templates is edit the */
			$return['delete'] = array(
				'icon'		=> 'times-circle',
				'title'		=> 'delete_revision',
				'link'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=deleterevision&id=' ) . $row['eh_id'],
				'data'		=> array( 'delete' => '' ),
			);

			$return['revert'] = array(
				'icon'		=> 'undo',
				'title'		=> 'revert_revision',
				'link'		=> \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=conversations&do=restoreRevision&id=' ) . $row['eh_id'],
				'data'		=> array( 'confirm' => '', 'confirmMessage' => \IPS\Member::loggedIn()->language()->addToStack('pmviewer_revision_restore'), 'confirmIcon' => 'question', 'confirmButtons' => json_encode( array( 'ok' => \IPS\Member::loggedIn()->language()->addToStack('restore'), 'cancel' => \IPS\Member::loggedIn()->language()->addToStack('cancel') ) ) ),
			);

			return $return;
		};

		\IPS\Output::i()->title   = \IPS\Member::loggedIn()->language()->addToStack('pmviewer_revisions');
		\IPS\Output::i()->output .= (string) $table;

		\IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}

	/**
	 * Restore a revision of a post in a conversation
	 *
	 * @return	void
	 */
	protected function restoreRevision()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_editposts'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$post = \IPS\DB::i()->select( '*', 'pmviewer_edithistory',  array( 'eh_id=?', \IPS\Request::i()->id ) )->first();

		\IPS\Db::i()->update( 'core_message_posts', array( 'msg_post' => $post['eh_original_text'] ), 'msg_id = '. $post['eh_post_id'] );

		\IPS\Db::i()->delete( 'pmviewer_edithistory', array( 'eh_id=?', \IPS\Request::i()->id ) );

		if( !$this->countRevisions( $post['eh_post_id'] ) )
		{
			\IPS\Db::i()->update( 'core_message_posts', array( 'msg_edited_pmviewer' => 0 ), 'msg_id = '. $post['eh_post_id'] );
		}

		$conversation = \IPS\core\Messenger\Conversation::load( $post['eh_conversation_id'] );

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_revision_restored', array( $post['eh_post_id'] => TRUE, $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $conversation->id ), 'pmviewer_revision_restored_short' );
	}

	/**
	 * Delete a revision of a post in a conversation
	 *
	 * @return	void
	 */
	protected function deleteRevision()
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_editposts'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !\IPS\Request::i()->id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		$post = \IPS\DB::i()->select( '*', 'pmviewer_edithistory',  array( 'eh_id=?', \IPS\Request::i()->id ) )->first();

		\IPS\Db::i()->delete( 'pmviewer_edithistory', array( 'eh_id=?', \IPS\Request::i()->id ) );

		$conversation 	= \IPS\core\Messenger\Conversation::load( $post['eh_conversation_id'] );

		/* Admin log */
		\IPS\Session::i()->log( 'pmviewer_revision_deleted', array( $post['eh_post_id'] => TRUE, $conversation->id => TRUE, $conversation->title => TRUE ), FALSE );

		if( !$this->countRevisions( $post['eh_post_id'] ) )
		{
			\IPS\Db::i()->update( 'core_message_posts', array( 'msg_edited_pmviewer' => 0 ), 'msg_id = '. $post['eh_post_id'] );
		}

		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=pmviewer&module=viewer&controller=conversations&do=view&id=" . $conversation->id ), 'pmviewer_revision_deleted_short' );
	}

	/**
	 * Count revisions of a post in a conversation
	 *
	 * @return	void
	 */
	protected function countRevisions( $id )
	{
		if( !\IPS\Member::loggedIn()->group['g_pmviewer_editposts'] )
		{
			\IPS\Output::i()->error( 'pmviewer_no_permission', '2C137/2', 403, '' );
		}

		if ( !$id )
		{
			\IPS\Output::i()->error( 'node_error', '2B221/1', 404, '' );
		}

		try
		{
			$count = \IPS\Db::i()->select( 'COUNT(*)', 'pmviewer_edithistory', array( 'eh_post_id=?', $id ) )->first();
		}
		catch( \UnderflowException $e )
		{
			$count = 0;
		}

		return $count;
	}
}