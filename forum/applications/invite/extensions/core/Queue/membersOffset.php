<?php
/**
 * @brief		Background Task
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	Invite System
 * @since		27 May 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\extensions\core\Queue;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Background Task
 */
class _membersOffset
{
	/**
	 * @brief Number of members to run per cycle
	 */
	public $rebuild = 50;

	/**
	 * Parse data before queuing
	 *
	 * @param	array	$data
	 * @return	array
	 */
	public function preQueueData( $data )
	{
		return $data;
	}

	/**
	 * Run Background Task
	 *
	 * @param	mixed						$data	Data as it was passed to \IPS\Task::queue()
	 * @param	int							$offset	Offset
	 * @return	int							New offset
	 * @throws	\IPS\Task\Queue\OutOfRangeException	Indicates offset doesn't exist and thus task is complete
	 */
	public function run( $data, $offset )
	{
		$select = \IPS\Db::i()->select( '*', 'core_members', NULL, 'member_id ASC', array( $offset, $this->rebuild ) );

		if ( !$select->count() )
		{
			throw new \IPS\Task\Queue\OutOfRangeException;
		}

		foreach ( $select as $member )
		{
			\IPS\Db::i()->update( 'core_members', array( 'invite_offset' => $member['member_posts'] ), array( 'member_id=?', $member['member_id'] ) );
		}

		return $offset + $this->rebuild;
	}
	
	/**
	 * Get Progress
	 *
	 * @param	mixed					$data	Data as it was passed to \IPS\Task::queue()
	 * @param	int						$offset	Offset
	 * @return	array( 'text' => 'Doing something...', 'complete' => 50 )	Text explaining task and percentage complete
	 * @throws	\OutOfRangeException	Indicates offset doesn't exist and thus task is complete
	 */
	public function getProgress( $data, $offset )
	{
		return array(
			'text'      => \IPS\Member::loggedIn()->language()->addToStack( 'is_members_offset', FALSE ),
			'complete'  => ( isset( $data['count'] ) AND $data['count'] ) 
				? ( round( 100 / $data['count'] * $offset, 2 ) ) 
				: 100
		);
	}
}