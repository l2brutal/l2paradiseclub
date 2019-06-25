//<?php

class bdmoods_hook_Member extends _HOOK_CLASS_
{
	public function get_moodTitle() {
		try
		{
			try
			{
		      $mood = \IPS\bdmoods\Mood::load($this->bdm_mood);
		      if ($mood->mood_id) {
		        if ($this->bdm_moodtext=="" ) {
		                return $mood->mood_title;
		        }
		        else {
		                return (!$this->group['g_bdm_canUseCustom'] ? $mood->mood_title: $this->bdm_moodtext);
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