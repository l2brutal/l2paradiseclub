//<?php

/**
 * @brief		(TB) Bump Up Topics
 * @author		Terabyte
 * @link		http://www.invisionbyte.net/
 * @copyright	(c) 2006 - 2016 Invision Byte
 */

class hook71 extends _HOOK_CLASS_
{
	/**
	 * Bump topic
	 *
	 * @return	void
	 */
	public function bump()
	{
		try
		{
			\IPS\Session::i()->csrfCheck();
			
			try
			{
				$topic = \IPS\forums\Topic::loadAndCheckPerms( \IPS\Request::i()->id );
			}
			catch ( \OutOfRangeException $e )
			{
				\IPS\Output::i()->error( 'node_error', '2F173/TBBUT1', 404 );
			}
			
		// Permissions check
			$check = $topic->canBump( TRUE );
			
			if ( $check !== TRUE )
			{
				switch($check)
				{
					case 'group':
						$code = 2;
						break;
					case 'forum':
						$code = 3;
						break;
					case 'topic':
						$code = 4;
						break;
					case 'reply':
						$code = 5;
						break;
				}
				
				\IPS\Output::i()->error( "tb_but_error_{$check}", "2F173/TBBUT{$code}", 403 );
			}
			
		// Check limit errors
			switch( $topic->bumpLimits['type'] )
			{
				case 'day':
					\IPS\Member::loggedIn()->language()->words['tb_but_day_limit_tooltip'] = \IPS\Member::loggedIn()->language()->pluralize( \IPS\Member::loggedIn()->language()->get('tb_but_day_limit_tooltip'), array( 1 ) );
					
					\IPS\Output::i()->error( 'tb_but_day_limit_tooltip', "2F173/TBBUT6", 403 );
					break;
				
				# @TODO: update the time left wording in the error screen, it only shows seconds or minutes (even if you have to wait days/hours)!
				case 'post':
				case 'bump':
				// Figure out the timer thing
					if ( $topic->bumpLimits['timerValue'] >= 84600 )
					{
						$langKey  = 'tb_but_error_time_d';
						$langTime = intval($topic->bumpLimits['timerValue']/84600);
					}
					elseif ( $topic->bumpLimits['timerValue'] >= 3600 )
					{
						$langKey  = 'tb_but_error_time_h';
						$langTime = intval($topic->bumpLimits['timerValue']/3600);
					}
					elseif ( $topic->bumpLimits['timerValue'] >= 60 )
					{
						$langKey  = 'tb_but_error_time';
						$langTime = intval($topic->bumpLimits['timerValue']/60);
					}
					else
					{
						$langKey  = 'tb_but_error_time_s';
						$langTime = $topic->bumpLimits['timerValue'];
					}
					
					\IPS\Member::loggedIn()->language()->words[ $langKey ] = \IPS\Member::loggedIn()->language()->pluralize( \IPS\Member::loggedIn()->language()->get( $langKey ), array( $langTime ) );
					
					\IPS\Output::i()->error( $langKey, "2F173/TBBUT7", 403 );
					break;
			}
			
		// Still here? That means we have no errors and can finally update!
		// 1) Update last post data
			$topic->last_post = time();
			$topic->save();
			
		// 2) Update forum
			$topic->container()->setLastComment();
			$topic->container()->save();
			
		// 3) Mark topic as read
			$topic->markRead();
			
		// 4) Update member limits cache
			$bumpCache = \IPS\Member::loggedIn()->tb_but_cache ? json_decode( \IPS\Member::loggedIn()->tb_but_cache, TRUE ) : array( 'last' => 0, 'count' => 0 );
			
			$bumpCache['last']  = time();
			$bumpCache['count'] = ( $topic->_bumpLimits['todayTime'] == $topic->_bumpLimits['bumpTime'] ) ? $bumpCache['count']+1 : 1;
			
			\IPS\Member::loggedIn()->tb_but_cache = json_encode($bumpCache);
			\IPS\Member::loggedIn()->save();
			
		// 5) Update the search index (mainly for unread content)
			\IPS\Content\Search\Index::i()->index( $topic );
			
		// 6) Finally redirect to forum view
			\IPS\Output::i()->redirect( $topic->container()->url(), 'tb_but_topic_bumped' );
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