//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook39 extends _HOOK_CLASS_
{
	/**
	 * Change Owner
	 *
	 */
	protected function changeOwner()
	{
		try
		{
			$owner 	= $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/1', 403, '' );
			}
	
			$form = new \IPS\Helpers\Form( 'owner', 'clubsenhancementsChangeOwner' );
			$form->class = 'ipsForm_vertical';
			$form->add( new \IPS\Helpers\Form\Member( 'club_owner', NULL, TRUE ) );
	
			if ( $values = $form->values() )
			{
				$this->club->owner = $values['club_owner'];
				$this->club->addMember( $values['club_owner'], \IPS\Member\Club::STATUS_LEADER, TRUE );
				$this->club->recountMembers();
	
				\IPS\Output::i()->redirect( $this->club->url(), 'clubsenhancementsOwnerChanged' );
			}
	
			\IPS\Output::i()->title = $this->club->name;
			\IPS\Output::i()->output = $form->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'forms', 'core' ) ), 'popupTemplate' ) );
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
	 * Change Type
	 *
	 */
	protected function changeType()
	{
		try
		{
			$owner = $this->club->get_owner();
		
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/2', 403, '' );
			}
	
			$form = new \IPS\Helpers\Form( 'form', 'clubsenhancementsChangeType' );
			$form->class = 'ipsForm_vertical';
	
			$availableTypes = array();
			foreach ( explode( ',', \IPS\Member::loggedIn()->group['g_create_clubs'] ) as $type )
			{
				if ( $type !== '' )
				{
					$availableTypes[ $type ] = 'club_type_' . $type;
				}
			}
			$form->add( new \IPS\Helpers\Form\Radio( 'club_type', $this->club->type, TRUE, array( 'options' => $availableTypes ) ) );
	
			if ( $values = $form->values() )
			{
				$this->club->type = $values['club_type'];
				$this->club->save();
	
				foreach ( $this->club->nodes() as $node )
				{
					try
					{
						$nodeClass = $node['node_class'];
						$node = $nodeClass::load( $node['node_id'] );
						$node->setPermissionsToClub( $this->club );
					}
					catch ( \Exception $e ) { }
				}
		
				\IPS\Output::i()->redirect( $this->club->url(), 'clubsenhancementsTypeChanged' );
			}
	
			\IPS\Output::i()->title = $this->club->name;
			\IPS\Output::i()->output = $form->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'forms', 'core' ) ), 'popupTemplate' ) );
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
	 * Add manually mebmers or ALL members from a group
	 *
	 */
	protected function addMembersGroup()
	{
		try
		{
			$owner = $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/3', 403, '' );
			}
	
			if( !\IPS\Settings::i()->clubsenhancementsOwnerCanAddMembers AND $owner->member_id == \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/4', 403, '' );
			}
	
			$form = new \IPS\Helpers\Form( 'form', 'add_button' );
			$form->add( new \IPS\Helpers\Form\Radio( 'club_member_type', NULL, TRUE, array(
	            'options' => array(
		            'member'	=> 'club_member_type_member',
		            'group'		=> 'club_member_type_group'
	            ),
	            'toggles' => array(
		            'member'  	=> array( 'club_member_type_member' ),
		            'group'  	=> array( 'club_member_type_group', 'club_member_type_group_secg' )
	            )
	        ) ) );
	
			$form->add( new \IPS\Helpers\Form\Member( 'club_member_type_member', NULL, TRUE, array(), function( $member ) use ( $form )
			{
				if( !is_object( $member ) or !$member->member_id )
				{
					throw new \InvalidArgumentException( 'form_member_bad' );
				}
				if( $this->club->isLeader( $member ) OR $this->club->owner == $member )
				{
					throw new \InvalidArgumentException( 'clubsenhancementsCannotAdd' );
				}
			}, NULL, NULL, 'club_member_type_member' ) );
			$form->add( new \IPS\Helpers\Form\Select( 'club_member_type_group', NULL, FALSE, array( 'options' => \IPS\Member\Group::groups( TRUE, FALSE ), 'parse' => 'normal' ), NULL, NULL, NULL, 'club_member_type_group' ) );
			$form->add( new \IPS\Helpers\Form\YesNo( 'club_member_type_group_secg', NULL, FALSE, array(), NULL, NULL, NULL, 'club_member_type_group_secg' ) );
	
			if ( $values = $form->values() )
			{
				if( $values['club_member_type'] == 'member' )
				{
					if( \IPS\Settings::i()->clubsenhancementsShowAddedBy )
					{
						$this->club->addMember( $values['club_member_type_member'], \IPS\Member\Club::STATUS_MEMBER, TRUE, \IPS\Member::loggedIn(), NULL, TRUE );
					}
					else
					{
						$this->club->addMember( $values['club_member_type_member'], \IPS\Member\Club::STATUS_MEMBER, TRUE, NULL, NULL, TRUE );
					}
	
					$this->club->recountMembers();
					\IPS\Output::i()->redirect( $this->club->url(), 'clubsenhancementsMembersAdded' );
				}
				else
				{
					$groupsF	= array();
					$set		= array();
					$where 		= array();
	
					$set[]		= "FIND_IN_SET(" . $values['club_member_type_group'] . ",mgroup_others)";
					$groupsF[] 	= $values['club_member_type_group'];
					
					if( $values['club_member_type_group_secg'] )
					{
						$where[] = array( "( member_group_id IN(" . implode( ',', $groupsF ) . ") OR " . implode( ' OR ', $set ) . ' )' );
					}
					else
					{
						$where[] = array( 'member_group_id=?', $values['club_member_type_group'] );
					}
	
					$_SESSION['add_group_club'] = $where;
					\IPS\Output::i()->redirect( $this->club->url()->setQueryString( array( 'do' => 'addMembersGroupFunction', 'group' => $values['club_member_type_group'], 'cycle' => 25 ) ) );
				}
			}
		
			\IPS\Output::i()->title = $this->club->name;
			\IPS\Output::i()->output = $form->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'forms', 'core' ) ), 'popupTemplate' ) );
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

	public function addMembersGroupFunction()
	{
		try
		{
			$group 	= \IPS\Request::i()->group;
			$cycle	= \IPS\Request::i()->cycle;
	
			\IPS\Output::i()->title  = \IPS\Member::loggedIn()->language()->addToStack( 'clubsenhancementsMembersAddingMembers' );
		
			\IPS\Output::i()->output = new \IPS\Helpers\MultipleRedirect( $this->club->url()->setQueryString( array( 'do' => 'addMembersGroupFunction', 'group' => $group, 'cycle' => $cycle ) ),
			function( $data ) use ( $cycle )
			{
				$select	= \IPS\Db::i()->select( '*', 'core_members', $_SESSION['add_group_club'], 'member_id ASC', array( is_array( $data ) ? $data['done'] : 0, $cycle ), NULL, NULL, \IPS\Db::SELECT_SQL_CALC_FOUND_ROWS );
	
				$group 	= \IPS\Request::i()->group;
				$cycle	= \IPS\Request::i()->cycle;
				$total		= $select->count( TRUE );
	
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
					$member	= \IPS\Member::constructFromData( $row );
	
					if( $this->club->isLeader( $member ) OR $this->club->owner == $member )
					{
						continue;
					}
					else
					{
						if( \IPS\Settings::i()->clubsenhancementsShowAddedBy )
						{
							$this->club->addMember( $member, \IPS\Member\Club::STATUS_MEMBER, TRUE, \IPS\Member::loggedIn(), NULL, TRUE );
						}
						else
						{
							$this->club->addMember( $member, \IPS\Member\Club::STATUS_MEMBER, TRUE, NULL, NULL, TRUE );
						}
					}
				}
	
				$data['done'] += $cycle;
	
				return array( $data, \IPS\Member::loggedIn()->language()->addToStack( 'clubsenhancementsMembersAddingMembers' ),( $data['done'] / $data['total'] ) * 100 );
			}, function()
			{
				/* Finished */
				$_SESSION['add_group_club'] = NULL;
				$this->club->recountMembers();
				\IPS\Output::i()->redirect( $this->club->url(), 'clubsenhancementsMembersAddedGroup' );
			} );
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

	public function manageFeatures()
	{
		try
		{
			$owner = $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/5', 403, '' );
			}
			
			$data = iterator_to_array( \IPS\Db::i()->select( '*', 'core_clubs_node_map', array( 'club_id=?', $this->club->id ), 'node_position ASC, id ASC' ) );
	
			\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' )->features( $data, $this->club );
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

	public function doUpdateFeatureOrder()
	{
		try
		{
			$position = 1;
			$order = array();
	
			foreach( \IPS\Request::i()->order as $id => $feature )
			{
				$featureId = str_replace( 'sortable-', '', $feature );
				$order[ (int) $featureId ] = $position++;
			}
	
			/* Okay, now order */
			foreach( $order as $id => $position )
			{
				\IPS\Db::i()->update( 'core_clubs_node_map', array( 'node_position' => $position ), array( 'id=?', $id ) );
			}
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

	public function doDeleteFeature()
	{
		try
		{
			$owner = $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/6', 403, '' );
			}
	
			$club = $this->club;
			
			/* Load Node */
			$nodeClass = \IPS\Request::i()->nodeClass;
			try
			{
				$node = $nodeClass::load( \IPS\Request::i()->nodeId );
				$nodeClub = $node->club();
				if ( !$nodeClub or $nodeClub->id !== $club->id )
				{
					throw new \Exception;
				}
			}
			catch ( \Exception $e )
			{
				\IPS\Output::i()->error( 'node_error', 'Clubs Enhancements/7', 404, '' );
			}
			$targetUrl = $club->url();
			
			/* Do we have any children or content? */
			if ( $node->hasChildren( NULL, NULL, TRUE ) or $node->showDeleteOrMoveForm() )
			{
				$form = $node->deleteOrMoveForm( FALSE, TRUE );
				if ( $values = $form->values() )
				{
					\IPS\Db::i()->delete( 'core_clubs_node_map', array( 'club_id=? AND node_class=? AND node_id=?', $club->id, $nodeClass, $node->_id ) );
					$node->deleteOrMoveFormSubmit( $values );				
					\IPS\Output::i()->redirect( $targetUrl );
				}
				else
				{
					/* Show form */
					\IPS\Output::i()->output = $form;
					return;
				}
			}
			else
			{
				/* Make sure the user confirmed the deletion */
				\IPS\Request::i()->confirmedDelete();
			}
			
			/* Delete it */
			\IPS\Db::i()->delete( 'core_clubs_node_map', array( 'club_id=? AND node_class=? AND node_id=?', $club->id, $nodeClass, $node->_id ) );
	
			$node->delete();
	
			/* Boink */
			if( \IPS\Request::i()->isAjax() )
			{
				\IPS\Output::i()->json( "OK" );
			}
			else
			{
				\IPS\Output::i()->redirect( $targetUrl );
			}
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

	public function disableFeature()
	{
		try
		{
			$owner = $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/14', 403, '' );
			}
	
			\IPS\Db::i()->update( 'core_clubs_node_map', array( 'node_enabled' => \IPS\Request::i()->status ), array( 'id=?', \IPS\Request::i()->featureId ) );
			\IPS\Output::i()->redirect( $this->club->url() );
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
	 * Create a node
	 *
	 * @return	void
	 */
	protected function nodeForm()
	{
		try
		{
			/* Permission check */
			$class = \IPS\Request::i()->type;
			if ( !$this->club->isLeader() or !in_array( $class, \IPS\Member\Club::availableNodeTypes( \IPS\Member::loggedIn() ) ) or ( \IPS\Settings::i()->clubs_require_approval and !$this->club->approved ) )
			{
				\IPS\Output::i()->error( 'no_module_permission', '2C350/T', 403, '' );
			}
	
			if( !isset( \IPS\Request::i()->node ) AND \IPS\Settings::i()->clubsenhancementsRestrictFeaturesNr > 0 )
			{
				$totalNodes = \IPS\Db::i()->select( 'count(*)', 'core_clubs_node_map', array( "club_id=? AND node_class=?", $this->club->id, $class ) )->first();
	
				if( $totalNodes >= \IPS\Settings::i()->clubsenhancementsRestrictFeaturesNr )
				{
					\IPS\Output::i()->error( 'clubsenhancementsRestrictFeaturesError', 'Clubs Enhancements/8', 403, '' );
				}
			}
	
			return parent::nodeForm();
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
	 * Join
	 *
	 * @return	void
	 */
	protected function join()
	{
		try
		{
			if( \IPS\Settings::i()->clubsenhancementsMemberCanJoin > 0 )
			{
				if( $this->club->numberOfClubsMemberIsMember( \IPS\Member::loggedIn() ) >= \IPS\Settings::i()->clubsenhancementsMemberCanJoin )
				{
					\IPS\Output::i()->error( 'clubsenhancementsRestrictCannotJoin', 'Clubs Enhancements/13', 403, '' );
				}
			}
	
			return parent::join();
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
	 * View
	 *
	 * @return	void
	 */
	protected function manage()
	{
		try
		{
			if( \IPS\Settings::i()->clubsenhancementsNewPage AND $this->club->new_home_page )
			{
				/* Display */
				\IPS\Output::i()->title = $this->club->name;
				\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' )->newIndex( $this->club );			
			}
			else
			{
				parent::manage();
			}
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

	public function activity()
	{
		try
		{
			/* If this is a closed club, and we don't have permission to read it, show the member page instead */
			if ( $this->club->type == \IPS\Member\Club::TYPE_CLOSED && !$this->club->canRead() )
			{
				$this->members();
				return;
			}
	
			/* Get the activity stream */
			$activity = \IPS\Content\Search\Query::init()->filterByClub( $this->club )->setOrder( \IPS\Content\Search\Query::ORDER_NEWEST_UPDATED )->search();
	
			/* Get who joined the club in between those results */
			if ( $this->club->type != \IPS\Member\Club::TYPE_PUBLIC )
			{
				$lastTime = NULL;
				foreach ( $activity as $key => $result )
				{
					$lastTime = $result->createdDate->getTimestamp();
				}
				$joins = array();
				$joinWhere = array( array( 'club_id=?', $this->club->id ), array( \IPS\Db::i()->in( 'status', array( \IPS\Member\Club::STATUS_MEMBER, \IPS\Member\Club::STATUS_MODERATOR, \IPS\Member\Club::STATUS_LEADER ) ) ) );
				if ( $lastTime )
				{
					$joinWhere[] = array( 'core_clubs_memberships.joined>?', $lastTime );
				}
				$select = 'core_clubs_memberships.joined' . ',' . implode( ',', array_map( function( $column ) {
					return 'core_members.' . $column;
				}, \IPS\Member::columnsForPhoto() ) );
				foreach ( \IPS\Db::i()->select( $select, 'core_clubs_memberships', $joinWhere, 'joined DESC', NULL, NULL, NULL, \IPS\Db::SELECT_MULTIDIMENSIONAL_JOINS )->join( 'core_members', 'core_members.member_id=core_clubs_memberships.member_id' ) as $join )
				{
					$joins[] = new \IPS\Content\Search\Result\Custom(
						\IPS\DateTime::ts( $join['core_clubs_memberships']['joined'] ),
						\IPS\Member::loggedIn()->language()->addToStack( 'clubs_activity_joined', FALSE, array( 'htmlsprintf' => \IPS\Theme::i()->getTemplate( 'global', 'core', 'front' )->userLinkFromData( $join['core_members']['member_id'], $join['core_members']['name'], $join['core_members']['members_seo_name'] ) ) ),
						\IPS\Theme::i()->getTemplate( 'global', 'core', 'front' )->userPhotoFromData( $join['core_members']['member_id'], $join['core_members']['name'], $join['core_members']['members_seo_name'], \IPS\Member::photoUrl( $join['core_members'] ), 'tiny' )
					);
				}
				
				/* Merge them in */
				if ( !empty( $joins ) )
				{
					$activity = array_merge( iterator_to_array( $activity ), $joins );
					uasort( $activity, function( $a, $b )
					{
						if ( $a->createdDate->getTimestamp() == $b->createdDate->getTimestamp() )
						{
							return 0;
						}
						elseif( $a->createdDate->getTimestamp() < $b->createdDate->getTimestamp() )
						{
							return 1;
						}
						else
						{
							return -1;
						}
					} );
				}
			}
					
			/* Display */				
			\IPS\Output::i()->title = $this->club->name;
			\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate('clubs')->view( $this->club, $activity, $this->club->fieldValues() );
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

	public function newHomePage()
	{
		try
		{
			/* Permission check */
			$owner = $this->club->get_owner();
			if( !\IPS\Member::loggedIn()->isAdmin() AND $owner->member_id != \IPS\Member::loggedIn()->member_id )
			{
				\IPS\Output::i()->error( 'no_module_permission', 'Clubs Enhancements/15', 403, '' );
			}
	
			$form = new \IPS\Helpers\Form( 'homepage', 'save' );
			$form->class = 'ipsPad ipsForm_vertical';
			$form->add( new \IPS\Helpers\Form\YesNo( 'new_home_page', $this->club->new_home_page, FALSE, array( 'togglesOn' => array( 'new_home_page_content' ) ) ) );
			$form->add( new \IPS\Helpers\Form\Editor( 'new_home_page_content', $this->club->new_home_page_content, FALSE, array( 'app' => 'core', 'key' => 'Signatures', 'autoSaveKey' => "club-home-page-{$this->club->id}", 'attachIds' => ( $this->club->id ? array( $this->club->id, NULL, 'club-home-page' ) : NULL ) ), NULL, NULL, NULL, 'new_home_page_content' ) );
	
			if ( $values = $form->values() )
			{
				$this->club->new_home_page 			= $values['new_home_page'];
				$this->club->new_home_page_content 	= $values['new_home_page_content'];
				$this->club->save();
	
				\IPS\Output::i()->redirect( $this->club->url(), 'saved' );
			}
	
			\IPS\Output::i()->title = $this->club->name;
			\IPS\Output::i()->output = $form->customTemplate( array( call_user_func_array( array( \IPS\Theme::i(), 'getTemplate' ), array( 'forms', 'core' ) ), 'popupTemplate' ) );
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