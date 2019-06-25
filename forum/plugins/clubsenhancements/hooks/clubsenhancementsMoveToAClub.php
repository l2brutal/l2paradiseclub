//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

abstract class hook37 extends _HOOK_CLASS_
{
	/**
	 * Move
	 *
	 * @param	\IPS\Node\Model	$container	Container to move to
	 * @param	bool			$keepLink	If TRUE, will keep a link in the source
	 * @return	void
	 */
	public function move( \IPS\Node\Model $container, $keepLink=FALSE )
	{
		try
		{
			parent::move( $container, $keepLink );
	
			if( \IPS\Settings::i()->clubsenhancementsAddMembersItems AND $container->club_id )
			{
				$class 	= get_class( $this );
				$club 	= \IPS\Member\Club::load( $container->club_id );
				$member = \IPS\Member::load( $this->mapped('author') );
	
				try
				{
					$club->addMember( $member, \IPS\Member\Club::STATUS_MEMBER, FALSE );
					$club->recountMembers();					
				}
				catch( \OverflowException $e ){}
	
				/* Replies/Comments */
				if ( isset( $class::$commentClass ) )
				{
					$commentClass	= $class::$commentClass;
					$idColumn 		= $this::$databaseColumnId;
		
					foreach( \IPS\Db::i()->select( '*', $commentClass::$databaseTable, array( $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['item'].'=?', $this->$idColumn ) ) as $comment )
					{
						$commAuthor = \IPS\Member::load( $comment[ $commentClass::$databasePrefix . $commentClass::$databaseColumnMap['author'] ] );
		
						try
						{
							$club->addMember( $commAuthor, \IPS\Member\Club::STATUS_MEMBER, FALSE );
							$club->recountMembers();					
						}
						catch( \OverflowException $e ){}
					}
				}
	
				/* Reviews */
				if ( isset( $class::$reviewClass ) )
				{
					$reviewClass	= $class::$reviewClass;
					$idColumn 		= $this::$databaseColumnId;
					
					foreach( \IPS\Db::i()->select( '*', $reviewClass::$databaseTable, array( $reviewClass::$databasePrefix . $reviewClass::$databaseColumnMap['item'].'=?',  $this->$idColumn ) ) as $review )
					{
						$reviewAuthor = \IPS\Member::load( $review[ $reviewClass::$databasePrefix . $reviewClass::$databaseColumnMap['author'] ] );
		
						try
						{
							$club->addMember( $reviewAuthor, \IPS\Member\Club::STATUS_MEMBER, FALSE );
							$club->recountMembers();					
						}
						catch( \OverflowException $e ){}
					}
				}
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
}