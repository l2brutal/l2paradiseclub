<?php
/**
 * @brief		Admin CP Member Form
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	(BD4) Moods
 * @since		22 Dec 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\bdmoods\extensions\core\MemberForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Admin CP Member Form
 */
class _MemberForm
{
	/**
	 * Action Buttons
	 *
	 * @param	\IPS\Member	$member	The Member
	 * @return	array
	 */
	public function actionButtons( $member )
	{
		return array();
	}

	/**
	 * Process Form
	 *
	 * @param	\IPS\Helpers\Form		$form	The form
	 * @param	\IPS\Member				$member	Existing Member
	 * @return	void
	 */
	public function process( &$form, $member )
	{
		$moods = array( 0 => 'bd_moods_moodNone' );
		foreach ( \IPS\bdmoods\Mood::moods() as $mood )
		{
			$moods[ $mood['mood_id'] ] = $mood['mood_title'];
		}

		$form->add( new \IPS\Helpers\Form\Select( 'bd_moods_mood',$member->bdm_mood, TRUE, array( 'options' => $moods), NULL, NULL, NULL, 'bd_moods_mood' ) );
		$form->add( new \IPS\Helpers\Form\Text( 'bd_moods_customFeeling',$member->bdm_moodtext, FALSE,array('maxLength'=>(\IPS\Settings::i()->bd_moods_maxcustomtextlength > 64 ? 64 : \IPS\Settings::i()->bd_moods_maxcustomtextlength) ) ) );

	}
	
	/**
	 * Save
	 *
	 * @param	array				$values	Values from form
	 * @param	\IPS\Member			$member	The member
	 * @return	void
	 */
	public function save( $values, &$member )
	{
		if ($values['bd_moods_mood']>0) {
			$member->bdm_mood = $values['bd_moods_mood'];
			$member->bdm_moodtext = $values['bd_moods_customFeeling'];
		}
		else {
			$member->bdm_mood=null;
			$member->bdm_moodtext = null;
			\IPS\Db::i()->delete('bdmoods_updates',array('update_memberid=?',$member->member_id));
		}
	}
}