//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class fmakeup_hook_css extends _HOOK_CLASS_
{


	/**
	 * Base CSS
	 *
	 * @return	void
	 */
	static public function baseCss()
	{
		try
		{
	      	$baseCss = call_user_func_array( 'parent::baseCss', func_get_args() );
	     	\IPS\Output::i()->cssFiles = array_merge( \IPS\Output::i()->cssFiles, \IPS\Theme::i()->css( 'imakeup.css', 'fmakeup' ) );
	
			return $baseCss;
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
