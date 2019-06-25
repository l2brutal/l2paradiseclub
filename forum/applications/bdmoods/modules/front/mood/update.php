<?php


namespace IPS\bdmoods\modules\front\mood;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * update
 */
class _update extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Output::i()->jsFiles = array_merge( \IPS\Output::i()->jsFiles, \IPS\Output::i()->js('front_chooser.js', 'bdmoods' ) );
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		$member = \IPS\Member::loggedin();

		/* Check we're not a guest */
		if ( !$member->member_id ) {
			\IPS\Output::i()->error( 'no_module_permission', 'BDMFMU/1', 403, '' );
		}

		/* Check that we're allowed to change */
		if ( $member->group['g_bdm_canChange'] == FALSE ) {
			\IPS\Output::i()->error( 'no_module_permission', 'BDMFMU/2', 403, '' );
		}

		// Load all moods.
		$moods = \IPS\bdmoods\Mood::moods();
		//var_dump($moods);die();

		/* Build form */
		$form = new \IPS\Helpers\Form(null,'update');

		if ($member->group['g_bdm_canUseCustom']) {
			$form->add( new \IPS\Helpers\Form\Text( 'bd_moods_customFeeling', $member->bdm_moodtext, FALSE,array('maxLength'=>(\IPS\Settings::i()->bd_moods_maxcustomtextlength > 255 ?255 : \IPS\Settings::i()->bd_moods_maxcustomtextlength),'trim'=>TRUE)));
		}

		$form->addButton( 'bd_moods_removeMood', 'button',NULL, $class='ipsButton ipsButton_negative',array('data-removemood'=>true) );

		$form->hiddenValues['bdmoods_moodid'] = (is_null(\IPS\Request::i()->bdmoods_moodid) ? $member->bdm_mood : \IPS\Request::i()->bdmoods_moodid);
		$form->hiddenValues['bdmoods_remove'] = (is_null(\IPS\Request::i()->bdmoods_remove) ? false : \IPS\Request::i()->bdmoods_remove);
		$form->class = 'ipsForm_horizontal';

		$formTpl = $form->customTemplate( array( \IPS\Theme::i()->getTemplate( 'global', 'bdmoods' ), 'chooserForm' ) );

		if ( $values = $form->values() )
		{
			// Remove nasties
			$values['bdmoods_remove'] = filter_var($values['bdmoods_remove'],FILTER_SANITIZE_NUMBER_INT);
			$values['bdmoods_moodid'] = mb_substr(filter_var($values['bdmoods_moodid'],FILTER_SANITIZE_NUMBER_INT),0,10);
			$values['bdmoods_moodid'] = ($values['bdmoods_moodid'] >= 0 ? $values['bdmoods_moodid'] : 0);

			if ($values['bdmoods_remove']==false && !empty($values['bdmoods_moodid'])) {
				$member->bdm_mood = $values['bdmoods_moodid'];

				if ($member->group['g_bdm_canUseCustom']) {
					$member->bdm_moodtext = $values['bd_moods_customFeeling'];
				}
				else {
					$member->bdm_moodtext = null;
				}
				$member->save();

				\IPS\Db::i()->insert('bdmoods_updates',array('update_memberid'=>$member->member_id,'update_mood'=>$member->bdm_mood,'update_time'=>time()),TRUE);
			}
			else {
				$member->bdm_mood=null;
				$member->bdm_moodtext=null;
				$member->save();
				\IPS\Db::i()->delete('bdmoods_updates',array('update_memberid=?',$member->member_id));
			}

			\IPS\Output::i()->redirect(isset( $_SERVER['HTTP_REFERER'] ) ? \IPS\Http\Url::external( $_SERVER['HTTP_REFERER'] ) : \IPS\Http\Url::internal( '' ));
		}

		\IPS\Output::i()->title	= \IPS\Member::loggedIn()->language()->addToStack( 'bd_moods_moodChooser' );
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'global', 'bdmoods' )->chooser($moods,$formTpl);

	}
	
	// Create new methods with the same name as the 'do' parameter which should execute it
}