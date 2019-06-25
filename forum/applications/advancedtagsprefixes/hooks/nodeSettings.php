//<?php

class advancedtagsprefixes_hook_nodeSettings extends _HOOK_CLASS_
{
	/**
	 * Get form
	 *
	 * @param	\IPS\Node\Model
	 * @return	\IPS\Helpers\Form
	 */
	protected function _addEditForm( \IPS\Node\Model $node )
	{
		try
		{
			$form = parent::_addEditForm( $node );
			
			if ( $node::isTaggable() === TRUE )
			{
			/**
			 * If there are multiple tabs, add another one for ours.
			 */
				if( count( $form->elements ) > 1 ) {
				// No icon: looks nice, but no way to tell if the existing tabs have icons. aarg.
					$form->addTab( 'advancedtagsprefixes_node_tab'/*, 'tags'*/ );
				}
				
				$form->addHeader('advancedtagsprefixes_node_tab');
				
				$isForum = ( $node instanceof \IPS\forums\Forum ) ? TRUE : FALSE;
				
				if( $isForum === TRUE ) {
				/**
				 * If forums, toggle our settings on the 'allow prefixes' and 'allow tags' settings.
				 */
					$tagFieldKeys		= array( 'bw_disable_prefixes', 'forum_tag_predefined', 'tag_mode', 'require_prefix', 'default_prefix', 'default_tags', 'show_prefix_in_desc', 'tag_min', 'tag_max' );
					$prefixFieldKeys	= array( 'require_prefix', 'default_prefix', 'show_prefix_in_desc', 'allowed_prefixes' );
					
					$form->add( new \IPS\Helpers\Form\YesNo( 'bw_disable_tagging', !$node->forums_bitoptions['bw_disable_tagging'], FALSE, array( 'togglesOn' => $tagFieldKeys ), NULL, NULL, NULL, 'bw_disable_tagging' ), NULL, 'posting_settings' );
					$form->add( new \IPS\Helpers\Form\YesNo( 'bw_disable_prefixes', !$node->forums_bitoptions['bw_disable_prefixes'], FALSE, array( 'togglesOn' => $prefixFieldKeys ), NULL, NULL, NULL, 'bw_disable_prefixes' ), NULL, 'posting_settings' );
				}
				
			/**
			 * Gather sources for settings
			 */
				$tagModes		= array(
					'inherit'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_inherit'),
					'open'		=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_open'),
					'closed'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_closed'),
					'prefix'	=> \IPS\Member::loggedIn()->language()->addToStack('advancedtagsprefixes_tagmode_prefix'),
				);
				
				$app			= \IPS\Application::load('advancedtagsprefixes');
				
				$allowedTags	= array();
				foreach( explode( ',', $node->tag_predefined ) as $v ) {
					$allowedTags[ $v ] = $v;
				}
				
				$allPrefixes	= array();
				foreach( $app->getPrefixCache() as $k => $v )
				{
					$prefix		= \IPS\advancedtagsprefixes\Prefix::constructFromData( $v );
					
					if( $prefix !== FALSE )
					{
						$allPrefixes[ $k ]	= $k;
					}
				}
				
				if( count( $allPrefixes ) < 2 )
				{
					$allPrefixes	= array( '' => '' ) + $allPrefixes;
				}
				
			/**
			 * Add our special settings
			 */
				if( $isForum === TRUE ) {
				// Adding input that may otherwise be missing (if \IPS\Settings::i()->tags_open_system)
					$form->add( new \IPS\Helpers\Form\Text( 'forum_tag_predefined', implode( ',', $allowedTags ), FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => 'forum_tag_predefined_unlimited' ), NULL, NULL, NULL, 'forum_tag_predefined' ), NULL, 'posting_settings' );
				}
				
				$form->add( new \IPS\Helpers\Form\YesNo( 'require_prefix', $node->require_prefix, FALSE, array(), NULL, NULL, NULL, 'require_prefix' ) );
				$form->add( new \IPS\Helpers\Form\Select( 'tag_mode', $node->tag_mode, FALSE, array( 'options' => $tagModes ), NULL, NULL, NULL, 'tag_mode' ) );
				$form->add( new \IPS\Helpers\Form\Text( 'default_tags', $node->default_tags, FALSE, array( 'autocomplete' => array( 'unique' => 'true' ), 'nullLang' => NULL ), NULL, NULL, NULL, 'default_tags' ) );
				$form->add( new \IPS\Helpers\Form\Text( 'allowed_prefixes', $node->allowed_prefixes, FALSE, array( 'autocomplete' => array( 'unique' => 'true', 'source' => $allPrefixes, 'freeChoice' => FALSE ) ), NULL, NULL, NULL, 'allowed_prefixes' ) );
				$form->add( new \IPS\Helpers\Form\Select( 'default_prefix', $node->default_prefix, FALSE, array( 'options' => array( '' => '' ) + $allPrefixes ), NULL, NULL, NULL, 'default_prefix' ) );
				$form->add( new \IPS\Helpers\Form\Number( 'tag_min', !is_null( $node->tag_min ) ? $node->tag_min : -1, FALSE, array( 'unlimited' => -1, 'unlimitedLang' => 'use_default' ), NULL, NULL, NULL, 'tag_min' ) );
				$form->add( new \IPS\Helpers\Form\Number( 'tag_max', !is_null( $node->tag_max ) ? $node->tag_max : -1, FALSE, array( 'unlimited' => -1, 'unlimitedLang' => 'use_default' ), NULL, NULL, NULL, 'tag_max' ) );
			}
			
			return $form;
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
