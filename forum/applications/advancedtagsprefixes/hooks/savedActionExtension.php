//<?php

class advancedtagsprefixes_hook_savedActionExtension extends _HOOK_CLASS_
{
	/**
	 * [Node] Add/Edit Form
	 *
	 * @param	\IPS\Helpers\Form	$form	The form
	 * @return	void
	 */
	public function form( &$form )
	{
		try
		{
			$return		= parent::form($form);
			
			$app		= \IPS\Application::load('advancedtagsprefixes');
			$options	= array(
				'0'			=> 'advancedtagsprefixes_mmod_remove',
			);
			
			foreach( $app->getPrefixCache() as $k => $v )
			{
				$options[ $v['prefix_id'] ] = $v['prefix_title'];
			}
			
			$form->add( new \IPS\Helpers\Form\Select( 'topic_prefix', !is_null( $this->topic_prefix ) ? $this->topic_prefix : '-1', FALSE, array( 'options' => $options, 'unlimited' => '-1', 'unlimitedLang' => 'advancedtagsprefixes_mmod_no_change' ), NULL, NULL, NULL, 'topic_prefix' ), 'topic_title_end' );
			$form->add( new \IPS\Helpers\Form\Text( 'topic_add_tags', $this->topic_add_tags, FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => NULL ), NULL, NULL, NULL, 'topic_add_tags' ), 'topic_prefix' );
			
			return $return;
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
	
	/**
	 * Run
	 *
	 * @param	\IPS\forums\Topic	$topic	The topic to run on
	 * @param	\IPS\Member|NULL	$member	Member running (NULL for currently logged in member)
	 * @return	void
	 */
	public function runOn( \IPS\forums\Topic $topic, \IPS\Member $member=NULL )
	{
		try
		{
			$return = call_user_func_array( 'parent::runOn', func_get_args() );
			if( $this->topic_prefix >= 0 or $this->topic_add_tags != '' )
			{
				$prefixChanged = FALSE;
				
			/**
			 * Get topic tags.
			 */
				$tags = array();
				
				if( $topic->tags() !== NULL )
				{
					$tags = $topic->tags();
				}
				
				if( $topic->prefix() !== NULL )
				{
					$tags['prefix'] = $topic->prefix();
				}
				
			/**
			 * Modify prefix, if need be.
			 */
				if( $this->topic_prefix > 0 )
				{
					if( !isset( $tags['prefix'] ) || $tags['prefix'] != $this->topic_prefix )
					{
						$prefixChanged	= TRUE;
					}
					
				// Remove old prefix and ensure it's not in the tag list
					if( isset( $tags['prefix'] ) )
					{
						$tags	= array_diff( $tags, array( $tags['prefix'] ) );
					}
					
				// Add new prefix
					$prefix	= \IPS\Application::load('advancedtagsprefixes')->getPrefixById( $this->topic_prefix );
					if( $prefix !== FALSE )
					{
						if( \IPS\Settings::i()->tags_force_lower )
						{
							$tags['prefix'] = mb_strtolower( $prefix->title );
						}
						else
						{
							$tags['prefix'] = $prefix->title;
						}
					}
				}
				elseif( $this->topic_prefix == 0 )
				{
					if( isset( $tags['prefix'] ) )
					{
						$prefixChanged = TRUE;
					}
					
				// Remove prefix entirely
					$tags = array_diff( $tags, array( $tags['prefix'] ) );
					
					unset( $tags['prefix'] );
				}
				
			/**
			 * Add to tag list.
			 */
				if( $this->topic_add_tags != '' )
				{
					$add = array_filter( explode( ',', $this->topic_add_tags ) );
					
					foreach( $add as $k => $tag )
					{
						$tag = \IPS\Settings::i()->tags_force_lower ? mb_strtolower( trim( $tag ) ) : trim( $tag );
						
						if( !in_array( $tag, $tags ) )
						{
							$tags[] = $tag;
						}
					}
				}
				
			/**
			 * Ensure order and remove duplicates - Added 3.1.7
			 */
				if( isset( $tags['prefix'] ) )
				{
					$prefix = $tags['prefix'];
					unset( $tags['prefix'] );
					$tags			= array_reverse( $tags );
					$tags['prefix']	= $prefix;
					$tags			= array_reverse( $tags );
				}
				
				$tags = array_filter( array_unique( $tags ) );
				
			/**
			 * Save the tags back to DB and cache.
			 */
				$topic->setTags( $tags );
				
				if( $prefixChanged === TRUE )
				{
				// Update container last-post info
					$container = $topic->container();
					$container->setLastComment();
					$container->save();
					
				// Update search index
					if( \IPS\Application::load('core')->long_version >= 101026 ) // Search index modified in 4.1.9
					{
						$post = \IPS\forums\Topic\Post::load( $topic->topic_firstpost );
						
						\IPS\Content\Search\Index::i()->index( $post );
					}
				}
			}
			
			return $return;
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
