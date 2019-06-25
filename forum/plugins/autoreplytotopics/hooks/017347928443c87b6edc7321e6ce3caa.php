//<?php

class hook30 extends _HOOK_CLASS_
{
	/**
	 * Process created object AFTER the object has been created
	 *
	 * @param	\IPS\Content\Comment|NULL	$comment	The first comment
	 * @param	array						$values		Values from form
	 * @return	void
	 */
	protected function processAfterCreate( $comment, $values )
	{
		try
		{
			$topic	= \IPS\forums\Topic::loadAndCheckPerms( $comment->topic_id );
			$forum	= \IPS\forums\Forum::load( $topic->forum_id );
	
			if ( $forum->autoreply_onoff )
			{
				$content 	= $forum->autoreply_text;
				$postCount	= $forum->autoreply_postcount ? NULL : FALSE;
				$post	 	= \IPS\forums\Topic\Post::create( $topic, $content, FALSE, NULL, $postCount, \IPS\Member::load( $forum->autoreply_authorid ) );
	
				if ( $forum->autoreply_closetopic )
				{
					\IPS\Db::i()->update( 'forums_topics', array( 'state' => 'closed' ), array( 'tid=?', $comment->topic_id ) );
				}
			}
	
			return parent::processAfterCreate( $comment, $values );
		}
		catch ( \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
}