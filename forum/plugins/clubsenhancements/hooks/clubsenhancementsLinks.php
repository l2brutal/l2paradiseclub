//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

abstract class hook42 extends _HOOK_CLASS_
{
	/**
	 * [Node] Get buttons to display in tree
	 * Example code explains return value
	 *
	 * @code
	 	array(
	 		array(
	 			'icon'	=>	'plus-circle', // Name of FontAwesome icon to use
	 			'title'	=> 'foo',		// Language key to use for button's title parameter
	 			'link'	=> \IPS\Http\Url::internal( 'app=foo...' )	// URI to link to
	 			'class'	=> 'modalLink'	// CSS Class to use on link (Optional)
	 		),
	 		...							// Additional buttons
	 	);
	 * @endcode
	 * @param	string	$url		Base URL
	 * @param	bool	$subnode	Is this a subnode?
	 * @return	array
	 */
	public function getButtons( $url, $subnode=FALSE )
	{
		try
		{
			$parent = parent::getButtons( $url, $subnode );
			
			if ( !( get_called_class() AND \IPS\IPS::classUsesTrait( get_called_class(), 'IPS\Content\ClubContainer' ) ) )
			{
				return $parent;
			}
	
			$merge	= array();
			$merge['convert_club'] = array(
				'icon'	=> 'comments',
				'title'	=> 'clubsenhancementsConvertToClubFeature',
				'link'	=> $url->setQueryString( array( 'do' => 'convertToClub', 'id' => $this->_id ) ),
				'data'	=> array( 'ipsDialog' => '', 'ipsDialog-size' => 'medium', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('clubsenhancementsConvertToClubFeature' ) )
			);
	
			array_splice( $parent, 4, 0, $merge );
	
			return $parent;
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
