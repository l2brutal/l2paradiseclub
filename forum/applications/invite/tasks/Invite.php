<?php
/**
 * @brief		Invite Task
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	invite
 * @since		05 Sep 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Invite Task
 */
class _Invite extends \IPS\Task
{
	/**
	 * Execute
	 *
	 * If ran successfully, should return anything worth logging. Only log something
	 * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
	 * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
	 * Tasks should execute within the time of a normal HTTP request.
	 *
	 * @return	mixed	Message to log or NULL
	 * @throws	\IPS\Task\Exception
	 */
	public function execute()
	{
		/* Expire old invites */
		if( \IPS\Settings::i()->is_expireinvite > 0 )
		{
			$invitesToUpdate = array();

			foreach ( \IPS\Db::i()->select( '*', 'invite_invites', 'invite_status=0 AND invite_expiration_date<>""' ) as $invitation )
			{
				if ( $invitation['invite_expiration_date'] < time() )
				{
					$invitesToUpdate[] = $invitation['invite_id'];

					if( \IPS\Settings::i()->is_restoreinvitation )
					{
						$inviter = \IPS\Member::load( $invitation['invite_sender_id'] );

						if ( !$inviter->group['is_unlimited'] )
						{
							$invites = ( $inviter->invites_remaining + 1 );
							\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $invites ), array( 'member_id=?', $inviter->member_id ) );
						}
					}
				}
			}

			/* Expire the invitations */
			$where[] = \IPS\Db::i()->in( 'invite_id', $invitesToUpdate );
			\IPS\Db::i()->update( 'invite_invites', array( 'invite_status' => 2 ), $where );
		}

		/* Update remaining invites according to post offset */
		if ( \IPS\Settings::i()->is_contentcountperinvite > 0 )
		{
			foreach( \IPS\Db::i()->select( 'member_id, member_posts, member_group_id, invites_remaining, invite_offset', 'core_members', array( 'member_posts - invite_offset>=?', \IPS\Settings::i()->is_contentcountperinvite ) ) as $member )
			{
				$invites = intval( ( $member['member_posts'] - $member['invite_offset'] ) / \IPS\Settings::i()->is_contentcountperinvite );
				$update[ $member['member_id'] ]['offset'] = $member['posts'] - $tmp;
				$tmp = intval( $member['member_posts'] / \IPS\Settings::i()->is_contentcountperinvite );
				$update[ $member['member_id'] ]['invites'] = $invites+$member['invites_remaining'];
				$update[ $member['member_id'] ]['offset'] = $tmp*\IPS\Settings::i()->is_contentcountperinvite;
				$update[ $member['member_id'] ]['member_id'] = $member['member_id'];

				foreach( $update as $k )
				{
					$group = \IPS\Member\Group::load( $member['member_group_id'] );

					if ( !$group->is_unlimited )
					{
						\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $k['invites'], 'invite_offset'=> $k['offset'] ), array( 'member_id=?', $member['member_id'] ) );
					}
				}
			}

			\IPS\Member::clearCreateMenu();
		}

		return NULL;
	}

	/**
	 * Cleanup
	 *
	 * If your task takes longer than 15 minutes to run, this method
	 * will be called before execute(). Use it to clean up anything which
	 * may not have been done
	 *
	 * @return	void
	 */
	public function cleanup()
	{
		
	}
}