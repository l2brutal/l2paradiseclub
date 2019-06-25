//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

class hook68 extends _HOOK_CLASS_
{
	/**
	 * Get group limits by priority
	 *
	 * @return	array
	 */
	public function getLimits()
	{
		try
		{
			$return = parent::getLimits();
			
		// Add excluded settings
			$return['exclude'][] = 'tb_but_forums';
			$return['exclude'][] = 'tb_but_bumpall';
			$return['exclude'][] = 'tb_but_day_limit';
			$return['exclude'][] = 'tb_but_time_limit';
			$return['exclude'][] = 'tb_but_last_limit';
			
		// Add in callback
			$return['callback']['tb_but_use'] = function( $a, $b )
			{
				$newData = array();
				
			// Usage enabled?
				if ( $b['tb_but_use'] )
				{
				// Primary group has usage disabled? Copy over values completely, and return right away then
					if ( ! $a['tb_but_use'] )
					{
						foreach( array( 'tb_but_use' , 'tb_but_forums', 'tb_but_bumpall', 'tb_but_day_limit', 'tb_but_time_limit', 'tb_but_last_limit' ) as $_k )
						{
							$newData[ $_k ] = $b[ $_k ];
						}
					}
				// Primary group can bump too, sort out differencies
					else
					{
						# Allowed forums
						if ( $a['tb_but_forums'] != '*' )
						{
							if ( $b['tb_but_forums'] == '*' )
							{
								$newData['tb_but_forums'] = '*';
							}
							elseif ( !empty($b['tb_but_forums']) )
							{
							// Merge and remove duplicate ids
								$newData['tb_but_forums'] = implode( ',', array_unique( array_filter( explode( ',', ($a['tb_but_forums'] . ',' .$b['tb_but_forums']) ) ) ) );
							}
						}
						
						# Bump all
						if ( $b['tb_but_bumpall'] )
						{
							$newData['tb_but_bumpall'] = $b['tb_but_bumpall'];
						}
						
						# Day limit
						if ( ! empty($a['tb_but_day_limit']) )
						{
							if( empty($b['tb_but_day_limit']) )
							{
								$newData['tb_but_day_limit'] = 0;
							}
							elseif ( $b['tb_but_day_limit'] > $a['tb_but_day_limit'] )
							{
								$newData['tb_but_day_limit'] = $b['tb_but_day_limit'];
							}
						}
						
						# Bump time limit
						if ( $b['tb_but_time_limit'] < $a['tb_but_time_limit'] )
						{
							$newData['tb_but_time_limit'] = $b['tb_but_time_limit'];
						}
						
						# Bump last limit
						if ( $b['tb_but_last_limit'] < $a['tb_but_last_limit'] )
						{
							$newData['tb_but_last_limit'] = $b['tb_but_last_limit'];
						}
					}
				}
				
				return count($newData) ? $newData : NULL;
			};
			
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