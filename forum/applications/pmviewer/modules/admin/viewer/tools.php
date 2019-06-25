<?php


namespace IPS\pmviewer\modules\admin\viewer;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * tools
 */
class _tools extends \IPS\Dispatcher\Controller
{
	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_manage' );

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__pmviewer_viewer_tools');

		$this->hideAllConversations();
		$this->unhideAllConversations();
	}

	protected function hideAllConversations()
	{
		/* Create Support Topics */
 		$form = new \IPS\Helpers\Form( 'hide', 'pmviewer_hide_all_conversations' );
		$form->addHeader('pmviewer_hide_all_conversations');
		$form->addMessage('pmviewer_hide_all_conversations_desc');
		$form->hiddenValues['something'] = 'something';

		if ( $values = $form->values() )
		{
			$msgs = array();

			foreach( \IPS\Db::i()->select( '*', 'core_message_topics', 'mt_is_hidden = 0' ) as $conversations )
			{
				$msgs[] = $conversations['mt_id'];
			}

			$_SESSION['hide_all_conversations'] = $msgs;

			/* Admin log */
			\IPS\Session::i()->log( 'pmviewer_all_conversations_hidden' );

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=tools&do=doHideAllConversations' ) );
		}

		\IPS\Output::i()->output .= $form;
	}

	public function doHideAllConversations()
	{
		$cycle 	= 50;

		\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'pmviewer_hiding_all_conversations' );

		\IPS\Output::i()->output = new \IPS\Helpers\MultipleRedirect( \IPS\Http\Url::internal('app=pmviewer&module=viewer&controller=tools&do=doHideAllConversations'),
		function( $data ) use ( $cycle )
		{
			$where[] = \IPS\Db::i()->in( 'mt_id', $_SESSION['hide_all_conversations'] );
	
			$select = \IPS\Db::i()->select( '*', 'core_message_topics', array( implode( ' AND ', $where ) ), 'mt_id ASC', array( is_array( $data ) ? $data['done'] : 0, $cycle ), NULL, NULL, \IPS\Db::SELECT_SQL_CALC_FOUND_ROWS );

			$total	= $select->count( TRUE );

			if ( !$select->count() )
			{
				return NULL;
			}

			if( !is_array( $data ) )
			{
				$data = array( 'total' => $total, 'done' => 0 );
			}

			foreach( $select as $row )
			{
				try
				{
					try
					{
						$conversation = \IPS\core\Messenger\Conversation::load( $row['mt_id'] );
					}
					catch( \Exception $e ){}

					\IPS\Db::i()->update( 'core_message_topics', array( 'mt_is_hidden' => 1 ), 'mt_id=' . $conversation->id );
				}
				catch( \Exception $e ) {}
			}

			$data['done'] += $cycle;

			return array( $data, \IPS\Member::loggedIn()->language()->addToStack( 'pmviewer_hiding_all_conversations' ),( $data['done'] / $data['total'] ) * 100 );
		}, function()
		{
			/* Finished */
			$_SESSION['hide_all_conversations'] = NULL;

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=tools&do=manage' ), 'completed' );
		} );
	}

	protected function unhideAllConversations()
	{
		/* Create Support Topics */
 		$form = new \IPS\Helpers\Form( 'unhide', 'pmviewer_unhide_all_conversations' );
		$form->addHeader('pmviewer_unhide_all_conversations');
		$form->addMessage('pmviewer_unhide_all_conversations_desc');
		$form->hiddenValues['something'] = 'something2';

		if ( $values = $form->values() )
		{
			$msgs = array();

			foreach( \IPS\Db::i()->select( '*', 'core_message_topics', 'mt_is_hidden = 1' ) as $conversation )
			{
				$msgs[] = $conversation['mt_id'];
			}

			$_SESSION['unhide_all_conversations'] = $msgs;

			/* Admin log */
			\IPS\Session::i()->log( 'pmviewer_all_conversations_unhidden' );
			
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=tools&do=doUnhideAllConversations' ) );
		}

		\IPS\Output::i()->output .= $form;
	}

	public function doUnhideAllConversations()
	{
		$cycle 	= 50;

		\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'pmviewer_unhiding_all_conversations' );

		\IPS\Output::i()->output = new \IPS\Helpers\MultipleRedirect( \IPS\Http\Url::internal('app=pmviewer&module=viewer&controller=tools&do=doUnhideAllConversations'),
		function( $data ) use ( $cycle )
		{
			$where[] = \IPS\Db::i()->in( 'mt_id', $_SESSION['unhide_all_conversations'] );

			$select = \IPS\Db::i()->select( '*', 'core_message_topics', array( implode( ' AND ', $where ) ), 'mt_id ASC', array( is_array( $data ) ? $data['done'] : 0, $cycle ), NULL, NULL, \IPS\Db::SELECT_SQL_CALC_FOUND_ROWS );

			$total	= $select->count( TRUE );

			if ( !$select->count() )
			{
				return NULL;
			}

			if( !is_array( $data ) )
			{
				$data = array( 'total' => $total, 'done' => 0 );
			}

			foreach( $select as $row )
			{
				try
				{
					try
					{
						$conversation = \IPS\core\Messenger\Conversation::load( $row['mt_id'] );
					}
					catch( \Exception $e ){}

					\IPS\Db::i()->update( 'core_message_topics', array( 'mt_is_hidden' => 0 ), 'mt_id=' . $conversation->id );
				}
				catch( \Exception $e ) {}
			}

			$data['done'] += $cycle;

			return array( $data, \IPS\Member::loggedIn()->language()->addToStack( 'pmviewer_unhiding_all_conversations' ),( $data['done'] / $data['total'] ) * 100 );
		}, function()
		{
			/* Finished */
			$_SESSION['unhide_all_conversations'] = NULL;

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=tools&do=manage' ), 'completed' );
		} );
	}
}