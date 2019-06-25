//<?php

class advancedtagsprefixes_hook_addPrefixToRss extends _HOOK_CLASS_
{
	/**
	 * Run Import
	 * 
	 * I'm a bad bad person. But there's no other way to do this. Deal with it.
	 * 
	 * Corrent with IPS 4.1.13.3
	 *
	 * @return	void
	 * @throws	\IPS\Http\Url\Exception
	 */
	public function run()
	{
		try
		{
			/* Skip this if the member is restricted from posting */
			if( \IPS\Member::load( $this->mid )->restrict_post or \IPS\Member::load( $this->mid )->members_bitoptions['unacknowledged_warnings'] )
			{
				return;
			}
	
			$previouslyImportedGuids = iterator_to_array( \IPS\Db::i()->select( 'rss_imported_guid', 'forums_rss_imported', array( 'rss_imported_impid=?', $this->id ) ) );
			
			$request = \IPS\Http\Url::external( $this->url )->request();
			if ( $this->auth )
			{
				$request = $request->login( $this->auth_user, $this->auth_pass );
			}
			$request = $request->get();
			
			$container = \IPS\forums\Forum::load( $this->forum_id );
			
			$i = 0;
			$inserts = array();
			$request = $request->decodeXml();
	
			if( !( $request instanceof \IPS\Xml\RssOne ) AND !( $request instanceof \IPS\Xml\Rss ) AND !( $request instanceof \IPS\Xml\Atom ) )
			{
				throw new \RuntimeException( 'rss_import_invalid' );
			}
	
			foreach ( $request->articles( $this->id ) as $guid => $article )
			{
				if ( !in_array( $guid, $previouslyImportedGuids ) )
				{
					$topic = \IPS\forums\Topic::createItem( \IPS\Member::load( $this->mid ), NULL, $article['date'], $container, $this->topic_hide );
					
	/** Changes here **/
				// $topic->title = $this->topic_pre . $article['title'];
					$topic->title = $article['title'];
	/** Changes there **/
	
					if ( !$this->topic_open )
					{
						$topic->state = 'closed';
					}
					$topic->save();
					
	/** Changes here **/
					$origMid = \IPS\Member::loggedIn()->member_id;
					\IPS\Member::loggedIn()->member_id = $this->mid;
					
					$topic->setTags( array( 'prefix' => $this->topic_pre ) );
					
					\IPS\Member::loggedIn()->member_id = $origMid;
	/** Changes there **/
					
					/* Add to search index */
					\IPS\Content\Search\Index::i()->index( $topic );
	
					$readMoreLink = '';
					if ( $article['link'] and $this->showlink )
					{
						$rel = array();
	
						if( \IPS\Settings::i()->posts_add_nofollow )
						{
							$rel['nofollow'] = 'nofollow';
						}
						 
						if( \IPS\Settings::i()->links_external )
						{
							$rel['external'] = 'external';
						}
	
						$linkRelPart = '';
						if ( count ( $rel ) )
						{
							$linkRelPart = 'rel="' .  implode($rel, ' ') . '"';
						}
	
						$readMoreLink = "<p><a href='{$article['link']}' {$linkRelPart}>{$this->showlink}</a></p>";
					}
					
					$member  = \IPS\Member::load( $this->mid );
					$content = \IPS\Text\Parser::parseStatic( $article['content'] . $readMoreLink, TRUE, NULL, $member, 'forums_Forums', TRUE, !(bool) $member->group['g_dohtml'] );
					
					$post = \IPS\forums\Topic\Post::create( $topic, $content, TRUE, NULL, \IPS\forums\Topic\Post::incrementPostCount( $container ), $member, $article['date'] );
					$topic->topic_firstpost = $post->pid;
					$topic->save();
					
					$inserts[] = array(
						'rss_imported_guid'	=> $guid,
						'rss_imported_tid'	=> $topic->tid,
						'rss_imported_impid'=> $this->id
					);
					
					$i++;
					
					if ( $i >= 10 )
					{
						break;
					}
				}
			}
			
			if( count( $inserts ) )
			{
				\IPS\Db::i()->insert( 'forums_rss_imported', $inserts );
			}
	
			$this->last_import = time();
			$this->save();
	
			$container->setLastComment();
			$container->save();
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