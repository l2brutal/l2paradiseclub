//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

abstract class hook64 extends _HOOK_CLASS_
{


	/**
	 * Return the language string key to use in search results
	 *
	 * @note Normally we show "(user) posted a (thing) in (area)" but sometimes this may not be accurate, so this is abstracted to allow
	 *	content classes the ability to override
	 * @param	array 		$authorData		Author data
	 * @param	array 		$articles		Articles language strings
	 * @param	array 		$indexData		Search index data
	 * @param	array 		$itemData		Data about the item
	 * @return	string
	 */
	static public function searchResultSummaryLanguage( $authorData, $articles, $indexData, $itemData )
	{
		try
		{
	
	      if (isset($authorData['member_group_id']))
	      {
	      	$authorData['name'] = \IPS\Theme::i()->getTemplate( 'global', 'core', 'front' )->userLinkFromData( $authorData['member_id'], $authorData['name'], NULL, $authorData['member_group_id'] );
	        
	        
	      }
	      
	      if (isset($itemData['author']['member_group_id']))
	      {
	      $itemData['author']['name'] = \IPS\Theme::i()->getTemplate( 'global', 'core', 'front' )->userLinkFromData( $itemData['author']['member_id'], $itemData['author']['name'], NULL, $itemData['author']['member_group_id'] );
	      }
	      
	      if( in_array( 'IPS\Content\Comment', class_parents( $indexData['index_class'] ) ) )
			{
				if( in_array( 'IPS\Content\Review', class_parents( $indexData['index_class'] ) ) )
				{
					if( isset( $itemData['author'] ) )
					{
						return \IPS\Member::loggedIn()->language()->addToStack( "user_other_activity_review", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $itemData['author']['name'], $articles['definite'] ) ) );
					}
					else
					{
						return \IPS\Member::loggedIn()->language()->addToStack( "user_own_activity_review", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $articles['indefinite'] ) ) );
					}
				}
				else
				{
					if( static::$firstCommentRequired )
					{
						if( $indexData['index_title'] )
						{
							return \IPS\Member::loggedIn()->language()->addToStack( "user_own_activity_item", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $articles['indefinite'] ) ) );
						}
						else
						{
							if( isset( $itemData['author'] ) )
							{
								return \IPS\Member::loggedIn()->language()->addToStack( "user_other_activity_reply", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $itemData['author']['name'], $articles['definite'] ) ) );
							}
							else
							{
								return \IPS\Member::loggedIn()->language()->addToStack( "user_own_activity_reply", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $articles['indefinite'] ) ) );
							}
						}
					}
					else
					{
						if( isset( $itemData['author'] ) )
						{
							return \IPS\Member::loggedIn()->language()->addToStack( "user_other_activity_comment", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $itemData['author']['name'], $articles['definite'] ) ) );
						}
						else
						{
							return \IPS\Member::loggedIn()->language()->addToStack( "user_own_activity_comment", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $articles['indefinite'] ) ) );
						}
					}
				}
			}
			else
			{
				if ( isset( static::$databaseColumnMap['author'] ) )
				{
					return \IPS\Member::loggedIn()->language()->addToStack( "user_own_activity_item", FALSE, array( 'htmlsprintf' => array( $authorData['name'], $articles['indefinite'] ) ) );
				}
				else
				{
					return \IPS\Member::loggedIn()->language()->addToStack( "generic_activity_item", FALSE, array( 'htmlsprintf' => array( $articles['definite_uc'] ) ) );
				}
			}
	      
	      
			return parent::searchResultSummaryLanguage( $authorData, $articles, $indexData, $itemData );
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
