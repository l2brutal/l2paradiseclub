//<?php

class advancedtagsprefixes_hook_formatPrefix extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'prefix' => 
  array (
    0 => 
    array (
      'selector' => 'a.ipsTag_prefix',
      'type' => 'replace',
      'content' => '',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */


	function prefix( $encoded, $text )
	{
		try
		{
			if(!$text)
			{
				return '';
			}
			
			$prefix = \IPS\Application::load('advancedtagsprefixes')->getPrefixByTitle( $text );
			
			if( !( $prefix instanceof \IPS\advancedtagsprefixes\Prefix ) )
			{
				$prefix = new \IPS\advancedtagsprefixes\Prefix;
			}
			
			$return = '<a href="';
			$return .= str_replace( '&', '&amp;', \IPS\Http\Url::internal( "app=core&module=search&controller=search&tags={$encoded}", null, "search", array(), 0 ) );
			$return .= '" title="';
			$sprintf = array($text);
			$return .= \IPS\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'find_tagged_content', \IPS\HTMLENTITIES, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );
			$return .= '" style="white-space:nowrap"><span>';
			
			if( !$prefix->id )
			{
				$prefix->pre = '<span class="ipsTag_prefix" style="display:inline-block">';
				$prefix->post = '</span>';
			}
			
			$return .= $prefix->pre;
			if( !$prefix->id or $prefix->showtitle )
			{
				$return .= htmlentities( $text, ENT_QUOTES | \IPS\HTMLENTITIES, 'UTF-8', FALSE );
			}
			$return .= $prefix->post;
			
			$return .= '</span></a>';
			
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