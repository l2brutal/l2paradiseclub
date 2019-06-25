<?php


namespace IPS\invite\modules\admin\invite;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * bonus
 */
class _bonus extends \IPS\Dispatcher\Controller
{
	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'bonus_manage' );

		$form = new \IPS\Helpers\Form( 'update', 'add_bonus' );

		$form->add( new \IPS\Helpers\Form\Radio( 'is_bonus_type', 'all', TRUE, array(
			'options' => array(
				'member' 	=> 'bonus_to_member',
				'group' 	=> 'bonus_to_group'
			),
			'toggles'	=> array(
				'member'	=> array( 'is_bonus_type_member' ),
				'group'		=> array( 'is_bonus_type_group', 'is_bonus_type_group_others' )
			)
		) ) );

		$form->add( new \IPS\Helpers\Form\Member( 'is_bonus_type_member', NULL, FALSE, array(), function( $member ) use ( $form )
		{
			if ( \IPS\Request::i()->is_bonus_type === 'member' )
			{
				if( !is_object( $member ) )
				{
					throw new \InvalidArgumentException( 'is_no_member_selected' );
				}
			}
		},
		NULL, NULL, 'is_bonus_type_member' ) );

		$form->add( new \IPS\Helpers\Form\Select( 'is_bonus_type_group', NULL, FALSE, array( 'options'	=> \IPS\Member\Group::groups( TRUE, FALSE ), 'parse' => 'normal' ), NULL, NULL, NULL, 'is_bonus_type_group' ) );
		
		$form->add( new \IPS\Helpers\Form\YesNo( 'is_bonus_type_group_others', NULL, FALSE, array(), NULL, NULL, NULL, 'is_bonus_type_group_others' ) );

		$form->add( new \IPS\Helpers\Form\Number( 'is_bonus_nr', 1, FALSE, array( 'min' => 1, 'max' => 999999 ) ) );

		if ( $values = $form->values() )
		{
			$where = '';

			if ( isset( $values['is_bonus_type'] ) and $values['is_bonus_type'] == 'member' )
			{
				/* Bonus to a member */
				\IPS\Db::i()->update( 'core_members', array( 'invites_remaining' => $values['is_bonus_type_member']->invites_remaining + $values['is_bonus_nr'] ), array( 'member_id=?', $values['is_bonus_type_member']->member_id ) );
				\IPS\Member::clearCreateMenu();
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal('app=invite&module=invite&controller=bonus'), 'bonus_given' );
			}
			else
			{
				/* Bonus to a member group */
				if ( $values['is_bonus_type_group_others'] )
				{
					$where = array( "member_group_id = " . $values['is_bonus_type_group'] . " OR FIND_IN_SET(" . $values['is_bonus_type_group'] . ",mgroup_others)" );
				}
				else
				{
					$where = array( 'member_group_id =?', $values['is_bonus_type_group'] );
				}

				$_SESSION['is_bonusgroup'] = $where;
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=invite&module=invite&controller=bonus&do=giveBonusGroup&bonus=' . $values['is_bonus_nr'] ) );
			}
		}

		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__invite_invite_bonus');
		\IPS\Output::i()->output = $form;
	}

	public function giveBonusGroup()
	{
		\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'is_giving_bonus_group' );
		$cycle = 50;
		$bonus = \IPS\Request::i()->bonus;

		\IPS\Output::i()->output = new \IPS\Helpers\MultipleRedirect( \IPS\Http\Url::internal( 'app=invite&module=invite&controller=bonus&do=giveBonusGroup&bonus='.$bonus.'&cycle='.$cycle ),
		function( $data ) use ( $cycle )
		{
			$select	= \IPS\Db::i()->select( '*', 'core_members', $_SESSION['is_bonusgroup'], 'member_id ASC', array( is_array( $data ) ? $data['done'] : 0, $cycle ), NULL, NULL, \IPS\Db::SELECT_SQL_CALC_FOUND_ROWS );

			$total	= $select->count( TRUE );

			if ( !$select->count() )
			{
				return NULL;
			}

			if( !is_array( $data ) )
			{
				$data = array( 'total' => $total, 'done' => 0 );
			}

			foreach( $select AS $row )
			{
				try
				{
					$member = \IPS\Member::constructFromData( $row );							
					$member->invites_remaining = $member->invites_remaining + \IPS\Request::i()->bonus;
					$member->save();
				}
				catch( \Exception $e ) {}
			}

			$data['done'] += $cycle;

			return array( $data, \IPS\Member::loggedIn()->language()->addToStack( 'is_giving_bonus_group' ),( $data['done'] / $data['total'] ) * 100 );
		}, function()
		{
			/* Finished */
			$_SESSION['is_bonusgroup'] = NULL;
			\IPS\Member::clearCreateMenu();
			$url	= \IPS\Http\Url::internal( "app=invite&module=invite&controller=bonus" );
			\IPS\Output::i()->redirect( $url, 'bonus_given' );
		} );
	}
}