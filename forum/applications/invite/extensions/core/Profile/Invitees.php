<?php
/**
 * @brief		Profile extension: Invitees
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	Invite System
 * @since		05 Mar 2016
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\invite\extensions\core\Profile;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * @brief	Profile extension: Invitees
 */
class _Invitees
{
	/**
	 * Member
	 */
	protected $member;
	
	/**
	 * Constructor
	 *
	 * @param	\IPS\Member	$member	Member whose profile we are viewing
	 * @return	void
	 */
	public function __construct( \IPS\Member $member )
	{
		$this->member = $member;
	}
	
	/**
	 * Is there content to display?
	 *
	 * @return	bool
	 */
	public function showTab()
	{
		$count = (int) \IPS\Db::i()->select( 'count(*)', 'core_members', array( 'invited_by=?', \IPS\Request::i()->id ) )->first();

		if ( $this->member->group['is_canvite'] AND !$this->member->invite_revoke_access AND $count )
		{
			return TRUE;
		}

		return FALSE;
	}
	
	/**
	 * Display
	 *
	 * @return	string
	 */
	public function render()
	{
		/* Load Member */
		try
		{
			$member = \IPS\Member::load( \IPS\Request::i()->id );
		}
		catch ( \OutOfRangeException $e )
		{
			\IPS\Output::i()->error( 'node_error', '2C114/5', 404, '' );
		}

		/* Init Table */
		$table = new \IPS\Helpers\Table\Db( 'core_members', \IPS\Http\Url::internal( "app=core&module=members&controller=profile&id={$member->member_id}&tab=node_invite_Invitees", 'front', 'profile', $member->members_seo_name ), array( 'invited_by=?', \IPS\Request::i()->id ) );
		$table->limit = 10;

		$table->include = array( 'member_id', 'photo', 'name', 'joined', 'member_posts', 'member_group_id' );

		$table->tableTemplate  	= array( \IPS\Theme::i()->getTemplate( 'profile', 'invite', 'front' ), 'inviteesTable' );
		$table->rowsTemplate   	= array( \IPS\Theme::i()->getTemplate( 'profile', 'invite', 'front' ), 'inviteesRows' );

		$table->mainColumn 	= 'name';
		$table->noSort		= array( 'photo' );

        /* Custom parsers */
        $table->parsers = array(
            'name'	=> function( $val, $row )
            {
				$member = \IPS\Member::load( $row['member_id'] );
				return $member->link();
            },
			'member_group_id' => function( $val, $row )
			{
				return \IPS\Member\Group::load( $row['member_group_id'] )->formattedName;
			},
            'photo' => function( $val, $row )
            {
                return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( \IPS\Member::constructFromData( $row ), 'tiny' );
            },
            'joined' => function( $val, $row )
            {
                return \IPS\DateTime::ts( $val )->relative();
            }
        );

		$table->sortBy = $table->sortBy ?: 'name';
		$table->sortDirection = $table->sortDirection ?: 'asc';

		/* Display */
		return (string) $table;
	}
}