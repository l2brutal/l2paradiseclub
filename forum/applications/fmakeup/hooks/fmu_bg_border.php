//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class fmakeup_hook_fmu_bg_border extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'forumRow' => 
  array (
    0 => 
    array (
      'selector' => 'li.ipsDataItem.ipsDataItem_responsivePhoto.ipsClearfix',
      'type' => 'add_attribute',
      'attributes_add' => 
      array (
        0 => 
        array (
          'key' => 'style',
          'value' => 'FMU.BG.COLOR',
        ),
      ),
    ),
  ),
  'forumGridItem' => 
  array (
    0 => 
    array (
      'selector' => 'div.ipsDataItem.ipsGrid_span4.ipsAreaBackground_reset.cForumGrid.ipsClearfix > div.ipsPhotoPanel.ipsPhotoPanel_mini.ipsClearfix.ipsPad.ipsAreaBackground_light.cForumGrid_forumInfo',
      'type' => 'add_attribute',
      'attributes_add' => 
      array (
        0 => 
        array (
          'key' => 'style',
          'value' => 'FMU.BG.COLOR',
        ),
      ),
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */

    /* Trick to replace attribute */
    function forumRow( $forum, $isSubForum=FALSE, $table=NULL )
    {
	try
	{
	        $output = parent::forumRow( $forum, $isSubForum, $table );
	
	        // Start hidden content replacement check
	        if ($forum->fmakeup_status == 1) {
	            $hiddenReplacement = ($forum->fmakeup_border && \IPS\Settings::i()->fmu_border_on) ? "border: ".\IPS\Settings::i()->fmu_border_size."px solid ".$forum->fmakeup_border.";" : '';
	            $hiddenReplacement .= $forum->fmakeup_bg ? "background-color:".$forum->fmakeup_bg.";" : '';
	        }   else {
	            $hiddenReplacement = '';
	        }
	
	        // Finally replace it in
	        if ($forum->fmakeup_status == 1 && ($forum->fmakeup_bg !== NULL || $forum->fmakeup_border !== NULL)) {
	            $output = str_replace('FMU.BG.COLOR', $hiddenReplacement, $output);
	        } else {
	            $output = str_replace('style="FMU.BG.COLOR"', $hiddenReplacement, $output);
	        }
	
		    return $output;
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

	/* Trick to replace attribute */
	function forumGridItem( $forum, $isSubForum=FALSE, $table=NULL )
	{
		try
		{
			$output = parent::forumGridItem( $forum, $isSubForum, $table );
	
			// Start hidden content replacement check
			if ($forum->fmakeup_status == 1) {
				$hiddenReplacement = $forum->fmakeup_border ? "border: ".\IPS\Settings::i()->fmu_border_size."px solid ".$forum->fmakeup_border.";" : '';
				$hiddenReplacement .= $forum->fmakeup_bg ? "background-color:".$forum->fmakeup_bg.";" : '';
			}   else {
				$hiddenReplacement = '';
			}
	
			// Finally replace it in
			if ($forum->fmakeup_status == 1 && ($forum->fmakeup_bg !== NULL || $forum->fmakeup_border !== NULL)) {
				$output = str_replace('FMU.BG.COLOR', $hiddenReplacement, $output);
			} else {
				$output = str_replace('style="FMU.BG.COLOR"', $hiddenReplacement, $output);
			}
	
			return $output;
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
