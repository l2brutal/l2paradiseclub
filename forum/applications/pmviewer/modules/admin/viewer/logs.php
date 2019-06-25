<?php


namespace IPS\pmviewer\modules\admin\viewer;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * logs
 */
class _logs extends \IPS\Dispatcher\Controller
{	
	/**
	 * Manage
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'logs_manage' );

		$where = array(
			array( 'appcomponent=? AND lang_key<>?', 'pmviewer', 'acplogs__pmviewer_settings' )
		);

		/* Create the table */
		$table = new \IPS\Helpers\Table\Db( 'core_admin_logs', \IPS\Http\Url::internal( 'app=pmviewer&module=viewer&controller=logs' ), $where );

		/* Column stuff */
		$table->include = array( 'member_id', 'ctime', 'ip_address', 'lang_key' );

		$table->mainColumn = 'ctime';
		$table->widths = array( 'member_id' => '18', 'ctime' => '15', 'ip_address' => '20', 'lang_key' => '47' );

		$table->parsers = array(
			'member_id' => function( $val, $row )
			{
				$member = \IPS\Member::load( $val );
				return \IPS\Theme::i()->getTemplate( 'global', 'core' )->userPhoto( $member, 'tiny' ) . '  ' . "<a href='" . \IPS\Http\Url::internal( 'app=core&module=members&controller=members&do=edit&id=' ) . $val . "'>" . $member->name . "</a>";
			},
			'ctime'	=> function( $val, $row )
			{
				$date	= \IPS\DateTime::ts( $val );

				return $date->localeDate() . ' ' . $date->localeTime( FALSE );
			},
			'ip_address'=> function( $val )
			{
				if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'core', 'members', 'membertools_ip' ) )
				{
					return "<a href='" . \IPS\Http\Url::internal( "app=core&module=members&controller=ip&ip={$val}" ) . "'>{$val}</a>";
				}
				return $val;
			},
			'lang_key'	=> function( $val, $row )
			{
				if ( $row['lang_key'] )
				{
					$langKey = $row['lang_key'];
					$params = array();
					foreach ( json_decode( $row['note'], TRUE ) as $k => $v )
					{
						$params[] = ( $v ? \IPS\Member::loggedIn()->language()->addToStack( $k ) : $k );
					}
					
					return \IPS\Member::loggedIn()->language()->addToStack( $langKey, FALSE, array( 'sprintf' => $params ) );
				}
				else
				{
					return $row['note'];
				}
			},
		);

		$table->limit = \IPS\Settings::i()->pmviewer_logentriesperpage;

		/* Sort stuff */
		$table->sortBy = $table->sortBy ?: 'ctime';
		$table->sortDirection = $table->sortDirection ?: 'desc';

		/* Search */
		$table->advancedSearch	= array(
			'member_id'			=> \IPS\Helpers\Table\SEARCH_MEMBER,
			'ip_address'		=> \IPS\Helpers\Table\SEARCH_CONTAINS_TEXT,
			'ctime'				=> \IPS\Helpers\Table\SEARCH_DATE_RANGE,
			'note'				=> \IPS\Helpers\Table\SEARCH_CONTAINS_TEXT,
		);

		$table->quickSearch = 'note';

		/* Add a button for settings */
		if ( \IPS\Member::loggedIn()->hasAcpRestriction( 'core', 'staff', 'restrictions_adminlogs_prune' ) )
		{
			\IPS\Output::i()->sidebar['actions'] = array(
				'settings'	=> array(
					'title'		=> 'prunesettings',
					'icon'		=> 'cog',
					'link'		=> \IPS\Http\Url::internal( 'app=core&module=staff&controller=admin&do=actionLogSettings' ),
					'data'		=> array( 'ipsDialog' => '', 'ipsDialog-title' => \IPS\Member::loggedIn()->language()->addToStack('prunesettings') )
				),
			);
		}

		/* Display */
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__pmviewer_viewer_logs');
		\IPS\Output::i()->output	= \IPS\Theme::i()->getTemplate( 'global', 'core' )->block( 'title', (string) $table );
	}
}
