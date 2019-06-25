//<?php

class advancedtagsprefixes_hook_addPrefixToTitle extends _HOOK_CLASS_
{
	protected function _setBreadcrumbAndTitle( $item, $link=TRUE )
	{
		try
		{
			parent::_setBreadcrumbAndTitle( $item, $link );
			
			if( $item instanceof \IPS\Content\Tags and \IPS\Settings::i()->prefix_in_title == 1 )
			{
				$prefix = $item->prefix();
				
				if( !empty( $prefix ) )
				{
					\IPS\Output::i()->title = sprintf( '[%s] %s', $prefix, \IPS\Output::i()->title );
				}
			}
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
