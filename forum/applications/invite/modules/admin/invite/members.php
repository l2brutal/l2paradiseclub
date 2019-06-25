<?php


namespace IPS\invite\modules\admin\invite;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * members
 */
class _members extends \IPS\Dispatcher\Controller
{	
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'members_manage' );
		parent::execute();
	}
	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{		
		/* Create the table */
		/* Create the table */
		$table = new \IPS\Helpers\Table\Db( 'core_members', \IPS\Http\Url::internal( 'app=invite&module=invite&controller=members' ), array( array( 'invites_remaining>?', 0 ) ) );
		$table->joins = array(
			array( 'select' => 'g.is_unlimited', 'from' => array( 'core_groups', 'g' ), 'where' => "g.g_id=core_members.member_group_id" )
		);

		/* Filters */
		$table->filters = array(
			'invitations_unlimited'	=> 'is_unlimited = 1',
			'invitations_limited'	=> 'is_unlimited = 0',
		);

		$table->include 	= array( 'photo', 'name', 'member_group_id', 'invites_remaining' );
		$table->widths 		= array( 'photo' => 7, 'name' => 40, 'member_group_id' => 30, 'invites_remaining' => 23 );
		$table->mainColumn 	= 'name';
		$table->noSort		= array( 'photo' );
		$table->classes 	= array( 'ipsDataList', 'ipsDataList_zebra' );
		$table->langPrefix 	= 'is_';

        /* Custom parsers */
        $table->parsers = array(
            'member_group_id'	=> function( $val, $row )
            {
                return \IPS\Member\Group::load( $val )->formattedName;
            },
            'photo' => function( $val, $row )
            {
                return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( \IPS\Member::constructFromData( $row ), 'mini' );
            },
            'invites_remaining'	=> function( $val, $row )
            {
				if( $row['is_unlimited'] )
				{
					return '<strong>'.\IPS\Member::loggedIn()->language()->addToStack('invitations_unlimited').'</strong>';
				}
				else
				{
					return $val;
				}
			}
        );

        /* Default sort options */
		//$table->sortBy = $table->sortBy ?: 'invites_remaining';
		$table->sortBy  = $table->sortBy ?: 'invites_remaining desc desc,name';
        $table->sortDirection = $table->sortDirection ?: 'asc';

		/* Search */
		$table->quickSearch = 'name';

		$groups     = array( '' => 'any_group' );
		foreach ( \IPS\Member\Group::groups() as $k => $v )
		{
			if( $k != \IPS\Settings::i()->guest_group )
			{
				$groups[ $k ] = $v->name;
			}
		}

		$table->advancedSearch = array(
			'name'					=> \IPS\Helpers\Table\SEARCH_MEMBER,
			'member_group_id'		=> array( \IPS\Helpers\Table\SEARCH_SELECT, array( 'options' => $groups ), function( $val )
			{
				return array( 'member_group_id=? OR FIND_IN_SET( ?, mgroup_others )', $val, $val );
			} ),
			'invites_remaining'		=> \IPS\Helpers\Table\SEARCH_NUMERIC
		);

		/* Display */
		\IPS\Output::i()->output	= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
		\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack('is_invitation_per_member');
	}
}