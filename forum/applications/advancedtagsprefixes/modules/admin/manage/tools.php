<?php


namespace IPS\advancedtagsprefixes\modules\admin\manage;

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
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_manage' );
		parent::execute();
	}

	/**
	 * Show management tools and forms
	 *
	 * @return	void
	 */
	protected function manage()
	{
		$output							= '';
		\IPS\Output::i()->title	 		= \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools');
		
		\IPS\Application::load('advancedtagsprefixes')->flushPrefixCache();
		
		/**
		 * Check some common configuration errors.
		 */
		$tagSettingsUrl	= \IPS\Http\Url::internal( 'app=core&module=settings&controller=posting&tab=tags' );
		$warnings		= array();
		
		if( \IPS\Settings::i()->tags_enabled == 0 )
		{
			$warnings[]	= sprintf( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tools_warn_tags_disabled'), $tagSettingsUrl );
		}
		
		if( \IPS\Settings::i()->tags_can_prefix == 0 )
		{
			$warnings[]	= sprintf( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tools_warn_prefixes_disabled'), $tagSettingsUrl );
		}
		
		if( count( $warnings ) > 0 )
		{
			$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( implode( '<br />', $warnings ), 'error', NULL, FALSE, TRUE );
		}
		
		/**
		 * Repair tool
		 */
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tools_repair' ) )
		{
			if( isset( \IPS\Data\Store::i()->advancedtagsprefixes_repairToolResults ) )
			{
				$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( implode( '<br />', \IPS\Data\Store::i()->advancedtagsprefixes_repairToolResults ), 'info', NULL, FALSE, TRUE );
				
				unset( \IPS\Data\Store::i()->advancedtagsprefixes_repairToolResults );
			}
			
			$form = new \IPS\Helpers\Form( 'advancedtagsprefixes_tools_repair', NULL, \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=repair' ) );
			$form->addHeader('advancedtagsprefixes_tools_repair');
			$form->addDummy( NULL, \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools_repair_header') );
			$form->addButton( 'advancedtagsprefixes_tools_repair_button', 'submit', NULL, 'ipsButton ipsButton_negative' );
			$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
		}
		
		/**
		 * Lowercase tags tool
		 */
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tools_lower' ) )
		{
			$form = new \IPS\Helpers\Form( 'advancedtagsprefixes_tools_lowercase', 'advancedtagsprefixes_tools_lowercase_button', \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=lower' ) );
			$form->addHeader('advancedtagsprefixes_tools_lowercase');
			$form->addDummy( NULL, \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools_lowercase_header') );
			$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
		}
		
		/**
		 * Rebuild tag cache tool
		 */
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tools_rebuild' ) )
		{
			$form = new \IPS\Helpers\Form( 'advancedtagsprefixes_tools_rebuild', 'advancedtagsprefixes_tools_rebuild_button', \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=rebuild' ) );
			$form->addHeader('advancedtagsprefixes_tools_rebuild');
			$form->addDummy( NULL, \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools_rebuild_header') );
			$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
		}
		
		/**
		 * Search and Replace tool
		 */
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'advancedtagsprefixes', 'manage', 'tools_massadd' ) )
		{
			$form = $this->_buildMassAddForm();
			
			$output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
		}
		
		
		/* Display */
		\IPS\Output::i()->breadcrumb[]	= array( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools') );
		\IPS\Output::i()->output 		= $output;
	}
	
	/**
	 * Convert all tags to lowercase.
	 */
	public function lower()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_lower' );
		
		\IPS\DB::i()->update( 'core_tags', 'tag_text=lower(tag_text)' );
		\IPS\DB::i()->update( 'core_tags_cache', 'tag_cache_text=lower(tag_cache_text)' );
		\IPS\DB::i()->update( 'advancedtagsprefixes_prefixes', 'prefix_title=lower(prefix_title)' );
		\IPS\DB::i()->update( 'advancedtagsprefixes_node_settings', 'default_prefix=lower(default_prefix), default_tags=lower(default_tags), allowed_prefixes=lower(allowed_prefixes)' );
		\IPS\DB::i()->update( 'forums_forums', 'default_prefix=lower(default_prefix), default_tags=lower(default_tags), last_prefix=lower(last_prefix), tag_predefined=lower(tag_predefined)' );
		\IPS\DB::i()->update( 'forums_topic_mmod', 'topic_add_tags=lower(topic_add_tags)' );
		
		if( \IPS\Application::load('core')->long_version >= 101026 ) { // Search index modified in 4.1.9
			\IPS\DB::i()->update( 'core_search_index_tags', 'index_tag=lower(index_tag)' );
		}
		
		\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tools_lower', array() );
		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), 'acplogs__advancedtagsprefixes_tools_lower' );
	}
	
	/**
	 * Attempt to identify and fix a number of common database errors
	 * including missing columns [from a botched upgrade] and collation
	 * conflicts.
	 */
	public function repair()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_repair' );
		
		$pre	= \IPS\DB::i()->prefix;
		$did	= array();
		$did[]	= "<b>Looking for things to fix...</b>";
		
		try
		{
			if( !\IPS\DB::i()->checkForTable('advancedtagsprefixes_prefixes') )
			{
				// Missing main table. How does this even happen?
				$did[] = "Attempting to create missing advancedtagsprefixes_prefixes table.";
			}
			else
			{
				$did[] = "Table advancedtagsprefixes_prefixes appears fine.";
			}
			
			/**
			 * Repair and DB schema differences... easy cover-all.
			 */
			$app = \IPS\Application::load('advancedtagsprefixes');
			$app->installDatabaseSchema();
			$app->flushPrefixCache();
			
			$did[] = "If any other database schema errors existed, they should be fixed.";
			
			if( $app->getPrefixCache() === array() )
			{
				if( \IPS\DB::i()->checkForTable('topic_prefixes') )
				{
					$did[] = "No data in the prefixes table. Found an old table. Attempting to migrate data...";
					
					try
					{
						\IPS\DB::i()->query( "insert into {$pre}advancedtagsprefixes_prefixes (select * from {$pre}topic_prefixes)" );
						
						$app->flushPrefixCache();
						
						$did[] = sprintf( "Migrated %s prefixes.", count( $app->getPrefixCache() ) );
					}
					catch( Exception $e )
					{
						$did[] = "Unsuccessful. " . $e->getMessage();
					}
				}
			}
			
			/**
			 * Check collations
			 */
			$r			= \IPS\DB::i()->query( "show full columns from {$pre}advancedtagsprefixes_prefixes where Field='prefix_title'" )->fetch_assoc();
			$collOurs	= $r['Collation'];
			
			$r			= \IPS\DB::i()->query( "show full columns from {$pre}forums_forums where Field='tag_predefined'" )->fetch_assoc();
			$collTheirs	= $r['Collation'];
			
			$did[] = "Prefixes collation appears to be $collOurs... forums collation is $collTheirs.";
			
			if( $collOurs != $collTheirs )
			{
				$did[]		= "Mismatch! Converting prefixes collation to $collTheirs.";
				$charset	= substr( $collTheirs, 0, strpos( $collTheirs, '_' ) );
				
				\IPS\DB::i()->query( "ALTER TABLE {$pre}advancedtagsprefixes_prefixes
					MODIFY prefix_title VARCHAR(255) CHARACTER SET {$charset} COLLATE {$collTheirs},
					MODIFY prefix_pre VARCHAR(255) CHARACTER SET {$charset} COLLATE {$collTheirs},
					MODIFY prefix_post VARCHAR(255) CHARACTER SET {$charset} COLLATE {$collTheirs},
					MODIFY prefix_groups MEDIUMTEXT CHARACTER SET {$charset} COLLATE {$collTheirs}" );
			}
			else
			{
				$did[]		= "Everything seems okay.";
			}
			
			$did[]		= "If you still have problems, go ask for support.";
		}
		catch( Exception $e )
		{
			$did[] = 'ERROR: ' . $e->getMessage();
			$did[] = 'Please copy this message and seek support.';
		}
		
		// I'm sure this is a bad approach...but oh well. Deal with it.
		\IPS\Data\Store::i()->advancedtagsprefixes_repairToolResults = $did;
		
		/**
		 * Show page and messages
		 */
		\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tools_repair', array() );
		\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), 'acplogs__advancedtagsprefixes_tools_repair' );
	}
	
	/**
	 * Rebuild the tag cache.
	 * Cache generation is based on related functionality in \IPS\Content\Item::setTags().
	 */
	public function rebuild()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_rebuild' );
		
		$start		= intval( \IPS\Request::i()->st );
		$total		= intval( \IPS\Request::i()->total );
		$sum		= $start;
		$perCycle	= 100;
		
		/**
		 * Clear existing cache entries
		 */
		if( $start == 0 )
		{
			// Make sure all lookup IDs are correct, before all else.
			\IPS\DB::i()->update( 'core_tags', 'tag_aai_lookup=md5(concat(tag_meta_app,";",tag_meta_area,";",tag_meta_id))' );
			
			// Then clear out the existing tags cache.
			\IPS\DB::i()->delete( 'core_tags_cache' );
			
			foreach( \IPS\DB::i()->select( 'count(`tag_id`)', 'core_tags' ) as $row )
			{
				$total = $row;
				break;
			}
		}
		
		/**
		 * Fetch a batch of tag keys
		 */
		$keys		= \IPS\DB::i()->select( array( 'tag_aai_lookup', 'tag_meta_app', 'tag_meta_area', 'tag_meta_id', 'tag_id' ), 'core_tags', NULL, '`tag_id` ASC', array( $start, $perCycle ) );
		$keyGroup	= array();
		foreach( $keys as $key )
		{
			$sum++;
			
			$keyGroup[ $key['tag_aai_lookup'] ] = $key;
		}
		
		/**
		 * Fetch all associated tags
		 */
		$tags		= \IPS\DB::i()->select( '*', 'core_tags', 'tag_aai_lookup in("' . implode( '","', array_keys( $keyGroup ) ) . '")', '`tag_added` DESC' );
		$tagGroups	= array();
		foreach( $tags as $tag )
		{
			if( empty( $tag['tag_text'] ) )
			{
				continue;
			}
			
			if( !isset( $tagGroups[ $tag['tag_aai_lookup'] ] ) )
			{
				$tagGroups[ $tag['tag_aai_lookup'] ] = array( 'tags' => array(), 'prefix' => NULL );
			}
			
			if( $tag['tag_prefix'] == 1 )
			{
				// Rare case... if there are multiple prefixes, demote all but the latest to tags.
				if( !is_null( $tagGroups[ $tag['tag_aai_lookup'] ]['prefix'] ) )
				{
					$tagGroups[ $tag['tag_aai_lookup'] ]['tags'][] = $tagGroups[ $tag['tag_aai_lookup'] ]['prefix'];
				}
				
				$tagGroups[ $tag['tag_aai_lookup'] ]['prefix'] = $tag['tag_text'];
			}
			else
			{
				$tagGroups[ $tag['tag_aai_lookup'] ]['tags'][] = $tag['tag_text'];
			}
		}
		
		/**
		 * Insert cache entries
		 */
		foreach( $tagGroups as $key => $tags )
		{
			\IPS\Db::i()->insert( 'core_tags_cache', array(
				'tag_cache_key'		=> $key,
				'tag_cache_text'	=> json_encode( $tags ),
				'tag_cache_date'	=> time(),
			), TRUE, TRUE );
			
			/**
			 * While we're at it, update the search index... nbd. -I mean, wait, what?
			 * 
			 * This is like 'I don't even what is this' level.
			 */
			if( \IPS\Application::load('core')->long_version >= 101026 ) // Search index modified in 4.1.9
			{
				if( !is_null( $tags['prefix'] ) )
				{
					array_unshift( $tags['tags'], $tags['prefix'] );
				}
				
				$newTagsKeyed = array();
				foreach( $tags['tags'] as $tag )
				{
					$newTagsKeyed[ $tag ] = 1;
				}
				
				$indexTags = \IPS\DB::i()->select(
					'c.index_id, t.index_tag',
					array( 'core_search_index', 'c' ),
					array(
						array( 'index_class like ?', '%' . $keyGroup[ $key ]['tag_meta_app'] . '%' ),
						array( 'index_class like ?', '%' . $keyGroup[ $key ]['tag_meta_area'] . '%' ),
						array( 'index_item_id=?', $keyGroup[ $key ]['tag_meta_id'] ),
					)
				);
				
				$indexTags->join( array( 'core_search_index_tags', 't' ), 't.index_id=c.index_id' );
				
				$indexId = null;
				
				$existingTagsKeyed = array();
				foreach( $indexTags as $tag )
				{
					if( !is_null( $tag['index_tag'] ) )
					{
						$existingTagsKeyed[ $tag['index_tag'] ] = $tag['index_id'];
					}
					
					$indexId = $tag['index_id'];
				}
				
				$toAdd		= array_diff_key( $newTagsKeyed, $existingTagsKeyed );
				$toDelete	= array_diff_key( $existingTagsKeyed, $newTagsKeyed );
				
				if( !empty( $toAdd ) && !is_null( $indexId ) )
				{
					$values = array();
					foreach( $toAdd as $k => $v )
					{
						$values[] = array(
							'index_id'	=> $indexId,
							'index_tag'	=> $k,
						);
					}
					
					\IPS\DB::i()->insert(
						'core_search_index_tags',
						$values,
						FALSE,
						TRUE
					);
				}
				
				if( !empty( $toDelete ) )
				{
					foreach( $toDelete as $k => $v )
					{
						\IPS\DB::i()->delete(
							'core_search_index_tags',
							array(
								array( 'index_id=?', $v ),
								array( 'index_tag=?', (string)$k ),
							)
						);
					}
				}
			}
		}
		
		// Log on first iteration
		if( $start == 0 )
		{
			\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tools_recache_log', array() );
		}
		
		// Last iteration - all done.
		if( $sum < ( $start + $perCycle ) )
		{
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), 'advancedtagsprefixes_tools_recache_msg' );
		}
		else
		{
			// If not done, show the update screen.
			$message = sprintf( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tools_recache_msg_intermediate'), $sum, $total );
			
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=rebuild&total=' . $total . '&st=' . $sum ), $message, 302, TRUE );
		}
	}
	
	/**
	 * Run the given topic search and preview the matches. This is yikers.
	 */
	public function previewAdd()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_massadd' );
		
		$form	= $this->_buildMassAddForm( TRUE );
		$values	= $this->_getMassAddInput( $form );
		
		$topics	= array();
		$search	= $this->_buildMassAddSearchQuery( $values );
		foreach( $search as $topic )
		{
			$topics[] = $topic;
		}
		
		$query = $values;
		$query['atp_addTags']		= is_array( $query['atp_addTags'] ) ? implode( ',', $query['atp_addTags'] ) : $query['atp_addTags'];
		$query['atp_searchForums']	= is_array( $query['atp_searchForums'] ) ? implode( ',', $query['atp_searchForums'] ) : $query['atp_searchForums'];
		
		$table = new \IPS\Helpers\Table\Custom( $topics, \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=previewAdd&' . http_build_query( $query ) ) );
		$table->include 		= array( 'title', 'name_seo', 'starter_name', 'start_date' );
		$table->noSort 			= array( 'title', 'name_seo', 'starter_name', 'start_date' );
		$table->langPrefix 		= 'advancedtagsprefixes_tools_massadd_';
		
		$table->parsers = array(
			'start_date'		=> function( $val, $row )
			{
				return \IPS\DateTime::ts( $val )->localeDate();
			},
		);
		
		
		$what	= array();
		if( !empty( $values['atp_addTags'] ) )
		{
			if( count( $values['atp_addTags'] ) > 1 )
			{
				$what[] = "tags '" . implode( "', '", $values['atp_addTags'] ) . "'";
			}
			else
			{
				$what[] = "a '" . implode( "', '", $values['atp_addTags'] ) . "' tag";
			}
		}
		
		if( $values['atp_addPrefix'] != '-1' )
		{
			$what[] = "a '" . $values['atp_addPrefix'] . "' prefix";
		}
		
		$what		= implode( " and ", $what );
		
		$count		= count( $topics );
		$perCycle	= intval( $values['atp_perCycle'] );
		$message	= sprintf( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tools_massadd_preview_msg'), $what, $count, $perCycle, ceil( $count / $perCycle ) );
		
		$form->hiddenValues['atp_total'] = $count;
		
		
		\IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_app_title');
		
		\IPS\Output::i()->title   = \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools_massadd');
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->message( $message, 'info', NULL, FALSE, TRUE );
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( \IPS\Output::i()->title, $form );
		\IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'advancedtagsprefixes', (string) $table );
	}
	
	/**
	 * Run the given topic search and process the given changes on the result set. (Add tags/prefix, remove title bit, etc.)
	 */
	public function processAdd()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'tools_massadd' );
		
		$form	= $this->_buildMassAddForm( TRUE );
		$values	= $this->_getMassAddInput( $form );
		
		if( empty( $values['atp_offset'] ) )
		{
			$values['atp_offset'] = 0;
		}
		
		$topics = array();
		$search = $this->_buildMassAddSearchQuery( $values );
		foreach( $search as $topic )
		{
			$topics[] = $topic;
		}
		
		if( count( $topics ) > 0 and ( $values['atp_addPrefix'] != '' or count( $values['atp_addTags'] ) > 0 ) )
		{
			foreach( $topics as $r )
			{
				/**
				 * Get tags
				 */
				$topic	= \IPS\forums\Topic::constructFromData( $r, FALSE );
				$tags	= $topic->tags();
				if( is_null( $tags ) )
				{
					$tags = array();
				}
				
				$tags['prefix']	= $topic->prefix();
				
				/**
				 * Add in the addition(s) and send back.
				 */
				if( $values['atp_addPrefix'] != '-1' )
				{
					$tags['prefix'] = $values['atp_addPrefix'];
				}
				
				if( count( $values['atp_addTags'] ) > 0 )
				{
					$tags = array_merge( $tags, $values['atp_addTags'] );
				}
				
				/**
				 * Try to make sure no blank tags make it through.
				 */
				$tags = array_filter( array_unique( $tags ) );
				
				/**
				 * Save our amazing work.
				 */
				$topic->setTags( $tags );
				
				unset( $topic );
			}
		}
		
		if( !empty( $values['atp_removeText'] ) )
		{
			/**
			 * Do a search-and-replace on topic titles to strip $remove.
			 */
			\IPS\DB::i()->update(
				'forums_topics',
				"title=trim( replace( replace(title, '" . mb_strtolower( addslashes( $values['atp_removeText'] ) ) . "', '') , '" . addslashes( $values['atp_removeText'] ) . "', '') ), title_seo=trim( leading '-' from replace(title_seo, '" . addslashes( \IPS\Http\Url::seoTitle( $values['atp_removeText'] ) ) . "', '') )",
				array( 'title like ?', $values['atp_removeText'] . '%' ),
				array(),
				$values['atp_perCycle']
			);
		}
		
		/**
		 * Log on first iteration.
		 */
		if( $values['atp_offset'] == 0 )
		{
			\IPS\Session::i()->log( 'acplogs__advancedtagsprefixes_tools_massadd_log', array() );
		}
		
		/**
		 * IF last iteration, all done.
		 */
		if( count( $topics ) < $values['atp_perCycle'] or $values['atp_offset'] + count( $topics ) >= $values['atp_total'] )
		{
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), 'advancedtagsprefixes_tools_massadd_done' );
		}
		else
		{
			/**
			 * If not done, show the update screen and run it again.
			 */
			$message = sprintf( \IPS\Member::loggedIn()->language()->get('advancedtagsprefixes_tools_massadd_msg_intermediate'), $values['atp_offset'] + count( $topics ), $values['atp_total'] );
			
			$values['atp_offset']		= $values['atp_offset'] + count( $topics );
			$values['atp_addTags']		= is_array( $values['atp_addTags'] ) ? implode( ',', $values['atp_addTags'] ) : $values['atp_addTags'];
			$values['atp_searchForums']	= is_array( $values['atp_searchForums'] ) ? implode( ',', $values['atp_searchForums'] ) : $values['atp_searchForums'];
			
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=processAdd&' . http_build_query( $values ) ), $message, 302, TRUE );
		}
	}
	
	/**
	 * Build and return the mass add form.
	 */
	protected function _buildMassAddForm( $confirm=FALSE )
	{
		if( $confirm === TRUE )
		{
			$form = new \IPS\Helpers\Form( 'advancedtagsprefixes_tools_massadd', NULL, \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=processAdd' ) );
			$form->addHeader('advancedtagsprefixes_tools_massadd');
			$form->addButton( 'advancedtagsprefixes_tools_massadd_confirm_button', 'submit', NULL, 'ipsButton ipsButton_negative' );
			$form->add( new \IPS\Helpers\Form\YesNo( 'atp_showForm',		FALSE, FALSE, array( 'togglesOn' => array( 'atp_searchTerm', 'atp_searchFirstPost', 'atp_searchForums', 'atp_addPrefix', 'atp_addTags', 'atp_removeText', 'atp_perCycle', 'atp_includePinned', 'atp_includeLocked' ) ), NULL, NULL, NULL, 'atp_showForm' ) );
		}
		else {
			$form = new \IPS\Helpers\Form( 'advancedtagsprefixes_tools_massadd', 'advancedtagsprefixes_tools_massadd_preview_button', \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools&do=previewAdd' ) );
			$form->addHeader('advancedtagsprefixes_tools_massadd');
			$form->addDummy( NULL, \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tools_massadd_header') );
		}
		
		$prefixes = array();
		foreach( \IPS\Application::load('advancedtagsprefixes')->getPrefixCache() as $label => $prefix )
		{
			$prefixes[ $label ] = $label;
		}
		
		\IPS\Request::i()->atp_addTags = is_array( \IPS\Request::i()->atp_addTags ) ? implode( ',', \IPS\Request::i()->atp_addTags ) : \IPS\Request::i()->atp_addTags;
		
		$form->add( new \IPS\Helpers\Form\Text( 'atp_searchTerm',			\IPS\Request::i()->atp_searchTerm ?: NULL, FALSE, array(), NULL, NULL, NULL, 'atp_searchTerm' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'atp_searchFirstPost',		\IPS\Request::i()->atp_searchFirstPost ?: TRUE, FALSE, array(), NULL, NULL, NULL, 'atp_searchFirstPost' ) );
		$form->add( new \IPS\Helpers\Form\Node( 'atp_searchForums',			\IPS\Request::i()->atp_searchForums ?: 0, FALSE, array( 'class' => 'IPS\forums\Forum', 'multiple' => TRUE, 'zeroVal' => 'all' ), NULL, NULL, NULL, 'atp_searchForums' ) );
		$form->add( new \IPS\Helpers\Form\Select( 'atp_addPrefix',			\IPS\Request::i()->atp_addPrefix ?: -1, FALSE, array( 'options' => $prefixes, 'unlimited' => -1, 'unlimitedLang' => 'advancedtagsprefixes_mmod_no_change' ), NULL, NULL, NULL, 'atp_addPrefix' ) );
		$form->add( new \IPS\Helpers\Form\Text( 'atp_addTags',				\IPS\Request::i()->atp_addTags ?: NULL, FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => 'none' ), NULL, NULL, NULL, 'atp_addTags' ) );
		$form->add( new \IPS\Helpers\Form\Text( 'atp_removeText',			\IPS\Request::i()->atp_removeText ?: NULL, FALSE, array( 'nullLang' => 'none' ), NULL, NULL, NULL, 'atp_removeText' ) );
		$form->add( new \IPS\Helpers\Form\Number( 'atp_perCycle',			\IPS\Request::i()->atp_perCycle ?: 50, TRUE, array(), NULL, NULL, NULL, 'atp_perCycle' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'atp_includePinned',		\IPS\Request::i()->atp_includePinned ?: TRUE, FALSE, array(), NULL, NULL, NULL, 'atp_includePinned' ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'atp_includeLocked',		\IPS\Request::i()->atp_includeLocked ?: TRUE, FALSE, array(), NULL, NULL, NULL, 'atp_includeLocked' ) );
		
		return $form;
	}
	
	/**
	 * Take form input, turn into and return topic search query.
	 */
	protected function _buildMassAddSearchQuery( $values )
	{
		$where = array();
		
		if( mb_strlen( trim( $values['atp_searchTerm'] ) ) > 1 )
		{
			if( $values['atp_searchFirstPost'] == TRUE )
			{
				$where[] = array( '(title like ? OR p.post like ?)', '%' . $values['atp_searchTerm'] . '%', '%' . $values['atp_searchTerm'] . '%' );
			}
			else {
				$where[] = array( 'title like ?', '%' . $values['atp_searchTerm'] . '%' );
			}
		}
		
		if( isset( $values['atp_includeLocked'] ) and $values['atp_includeLocked'] != TRUE )
		{
			$where[] = array( 'state != ?', 'closed' );
		}
		
		if( $values['atp_searchForums'] != 0 )
		{
			$forums = $values['atp_searchForums'];
			
			if( !is_array( $forums ) )
			{
				$forums = array_filter( explode( ',', $forums ) );
			}
			
			array_walk( $forums, 'intval' );
			
			if( count( $forums ) > 0 )
			{
				$where[] = 'forum_id in(' . implode( ',', $forums ) . ')';
			}
		}
		
		if( isset( $values['atp_includePinned'] ) and $values['atp_includePinned'] != TRUE )
		{
			$where[] = 'pinned=0';
		}
		
		if( count( $where ) == 0 )
		{
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=advancedtagsprefixes&module=manage&controller=tools' ), 'advancedtagsprefixes_tools_error' );
		}
		
		$search = \IPS\DB::i()->select(
			'tid, forum_id, title, starter_name, start_date, name_seo',
			array( 'forums_topics', 't' ),
			$where,
			'tid desc',
			( isset( $values['atp_offset'] ) ? array( $values['atp_offset'], $values['atp_perCycle'] ) : NULL )
		);
		
		$search->join( array( 'forums_forums', 'f' ), 'f.id=t.forum_id' );
		
		if( $values['atp_searchFirstPost'] == TRUE )
		{
			$search->join( array( 'forums_posts', 'p' ), 'p.pid=t.topic_firstpost' );
		}
		
		return $search;
	}
	
	/**
	 * Get input from form or from request. You know, whichever.
	 */
	protected function _getMassAddInput( &$form )
	{
		$values	= $form->values();
		
		if( empty( $values ) )
		{
			/**
			 * Take input and put it in the same format as $form->values(). Consistency is cool.
			 */
			$inputKeys	= array( 'atp_searchTerm', 'atp_searchFirstPost', 'atp_searchForums', 'atp_addPrefix', 'atp_addTags', 'atp_removeText', 'atp_perCycle', 'atp_includePinned', 'atp_includeLocked', 'atp_offset', 'atp_total' );
			$values		= array();
			
			foreach( $inputKeys as $key )
			{
				$values[ $key ] = \IPS\Request::i()->$key;
				
				if( $key == 'atp_addTags' or $key == 'atp_searchForums' )
				{
					$values[ $key ] = array_filter( explode( ',', urldecode( $values[ $key ] ) ) );
				}
				elseif( $key == 'atp_addPrefix' )
				{
					$values[ $key ] = urldecode( $values[ $key ] );
				}
			}
		}
		else
		{
			if( is_array( $values['atp_searchForums'] ) )
			{
				// Convert Forum nodes to IDs. eesh.
				$forums = array();
				foreach( $values['atp_searchForums'] as $forum )
				{
					if( $forum instanceof \IPS\forums\Forum )
					{
						$forums[] = $forum->id;
					}
				}
				
				$values['atp_searchForums'] = $forums;
			}
			
			if( \IPS\Request::i()->atp_total )
			{
				$values['atp_total'] = \IPS\Request::i()->atp_total;
			}
		}
		
		if( !empty( $values['atp_addTags'] ) )
		{
			if( !is_array( $values['atp_addTags'] ) )
			{
				$values['atp_addTags'] = explode( ',', $values['atp_addTags'] );
			}
		}
		
		return $values;
	}
}
