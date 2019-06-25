//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

class hook70 extends _HOOK_CLASS_
{
	/**
	 * Can bump?
	 *
	 * @param	bool				$return		If TRUE returns a string of the failed permission check
	 * @param	\IPS\Member\NULL	$member		The member (NULL for currently logged in member)
	 * @return	bool
	 */
	public function canBump( $return=FALSE, $member=NULL )
	{
		try
		{
			$member = $member ?: \IPS\Member::loggedIn();
			
		// Can use bump?
			if ( $member->member_id AND $member->group['tb_but_use'] )
			{
			// Can bump in all forums or select forums?
				if ( $member->group['tb_but_forums'] == '*' OR in_array( $this->container()->id, explode(',',$member->group['tb_but_forums']) ) )
				{
				// Can bump all or topic starter?
					if ( $member->group['tb_but_bumpall'] OR $this->starter_id == $member->member_id )
					{
					// Can reply?
						if ( $this->canComment( $member ) )
						{
							return TRUE;
						}
						else
						{
							$error  = 'reply';
						}
					}
					else
					{
						$error = 'topic';
					}
				}
				else
				{
					$error = 'forum';
				}
			}
			else
			{
				$error  = 'group';
			}
			
			return $return ? $error : FALSE;
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
	 * Bump limits data
	 */
	public $_bumpLimits = NULL;
	
	/**
	 * Get bump limits data
	 *
	 * @param	\IPS\Member\NULL	$member	The member (NULL for currently logged in member)
	 * @return	array
	 */
	public function get_bumpLimits( $member=NULL )
	{
		try
		{
			$member = $member ?: \IPS\Member::loggedIn();
			
		// Setup any limits yet?
			if ( $this->_bumpLimits === NULL )
			{
				# Set some values
				$this->_bumpLimits = array( 'type' => '', 'tooltip' => '', 'timerValue' => 0, 'timerText' => '' );
				
				$bumpCache = $member->tb_but_cache ? json_decode( $member->tb_but_cache, TRUE ) : array( 'last' => 0, 'count' => 0 );
				
			// Parse date data setting the timezone to get a proper offset
				$date = \IPS\DateTime::create()->setTimezone( new \DateTimeZone( \IPS\Member::loggedIn()->timezone ) );
				
			// I have to save the strings into 2 different variables.
			// Comparing them directly returns TRUE for some odd reason
				$this->_bumpLimits['todayTime'] = $date->fullYearLocaleDate();
				$this->_bumpLimits['bumpTime']  = $date->setTimestamp( $bumpCache['last'] )->fullYearLocaleDate();
				
				# Daily limit
				if ( $member->group['tb_but_day_limit'] AND $bumpCache['last'] AND $bumpCache['count'] AND $bumpCache['count'] >= $member->group['tb_but_day_limit'] AND $this->_bumpLimits['todayTime'] == $this->_bumpLimits['bumpTime'] )
				{
					$this->_bumpLimits['type']    = 'day';
					$this->_bumpLimits['tooltip'] = \IPS\Member::loggedIn()->language()->pluralize( \IPS\Member::loggedIn()->language()->get('tb_but_day_limit_tooltip'), array( $member->group['tb_but_day_limit'] ) );
				}
				
				# Last post limit
				if ( empty($this->_bumpLimits['type']) AND $member->group['tb_but_last_limit'] AND $this->last_post )
				{
					$timeCheck = $this->last_post + ($member->group['tb_but_last_limit']*60) - time();
					
					if ( $timeCheck > 0 )
					{
						$this->_bumpLimits['type']       = 'post';
						$this->_bumpLimits['timerValue'] = $timeCheck;
						$this->_bumpLimits['timerText']  = $this->getBumpTimerText( $timeCheck );
						$this->_bumpLimits['tooltip']    = \IPS\Member::loggedIn()->language()->addToStack('tb_but_last_limit_tooltip');
					}
				}
				
				# Last bump limit
				if ( empty($this->_bumpLimits['type']) AND $member->group['tb_but_time_limit'] AND !empty($bumpCache['last']) )
				{
					$timeCheck = $bumpCache['last'] + ($member->group['tb_but_time_limit']*60) - time();
					
					if ( $timeCheck > 0 )
					{
						$this->_bumpLimits['type']       = 'bump';
						$this->_bumpLimits['timerValue'] = $timeCheck;
						$this->_bumpLimits['timerText']  = $this->getBumpTimerText( $timeCheck );
						$this->_bumpLimits['tooltip']    = \IPS\Member::loggedIn()->language()->addToStack('tb_but_time_limit_tooltip');
					}
				}
			}
			
			return $this->_bumpLimits;
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
	
	private function getBumpTimerText( $countdown )
	{
		try
		{
			# Convert to string
			$start = new \IPS\DateTime("@0");
			$end   = new \IPS\DateTime("@{$countdown}");
			
			list( $days, $hours, $minutes, $seconds ) = explode( '-', $start->diff($end)->format('%a-%h-%i-%s') );
			
			# No days? Skip them. Got days and no hours? Be sure to still display it. Other things as usual.
			return ( $days ? $days.':' : '' ) . ( ($days or $hours) ? ( $days ? str_pad( $hours, 2, '0', STR_PAD_LEFT ) : $hours ).':' : '' ) . str_pad( $minutes, 2, '0', STR_PAD_LEFT ) . ':' . str_pad( $seconds, 2, '0', STR_PAD_LEFT );
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