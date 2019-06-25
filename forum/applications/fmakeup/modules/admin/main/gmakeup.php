<?php

/**
 * @brief       Gmakeup Class
 * @author      <a href='http://www.bbcode.it'>InvisionHQ - G. Venturini</a>
 * @copyright   (c) 2016 InvisionHQ - G. Venturini
 * @package     IPS Social Suite
 * @subpackage  iMakeup
 * @since       1.0.0
 * @version     1.0.0
 */

namespace IPS\fmakeup\modules\admin\main;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * gmakeup
 */
class _gmakeup extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'gmakeup_manage' );
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
        /* Init */
        $content = array();

        if (!\IPS\Settings::i()->fmu_g_forums)
        {
            $forums_title = \IPS\Member::loggedIn()->language()->addToStack('fmakeup_enable');

        } else {

	        $forums_title = \IPS\Member::loggedIn()->language()->addToStack('fmakeup_disable');
        }

		if (!\IPS\Settings::i()->fmu_g_rules)
		{
			$rules_title = \IPS\Member::loggedIn()->language()->addToStack('fmakeup_enable');

		} else {

			$rules_title = \IPS\Member::loggedIn()->language()->addToStack('fmakeup_disable');
		}

        $content[] = array(
            'title'			=> \IPS\Member::loggedIn()->language()->addToStack('fmakeup_remove_forums'),
            'description'	=> \IPS\Member::loggedIn()->language()->addToStack('fmakeup_remove_forums_desc'),
            'status'		=> \IPS\Settings::i()->fmu_g_forums,
            'button'		=> array( 'title' => $forums_title, 'action' => "app=fmakeup&module=main&controller=gmakeup&do=removeForums" ),
        );

		$content[] = array(
			'title'			=> \IPS\Member::loggedIn()->language()->addToStack('fmakeup_show_rules'),
			'description'	=> \IPS\Member::loggedIn()->language()->addToStack('fmakeup_show_rules_desc'),
			'status'		=> \IPS\Settings::i()->fmu_g_rules,
			'button'		=> array( 'title' => $rules_title, 'action' => "app=fmakeup&module=main&controller=gmakeup&do=showRules" ),
		);


        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('fmakeup_gmakeup_page_title');

        \IPS\Output::i()->output .= \IPS\Theme::i()->getTemplate( 'main' )->gmakeup( $content );
	}

    /**
     * Toggle a removeForums Makeup state to active or inactive
     *
     * @note	Nothing
     * @return	void
     */
    protected function removeForums()
    {
        /* Toggle Forums setting */
        if ( \IPS\Settings::i()->fmu_g_forums )
        {
            \IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => 0 ), array( 'conf_key=?', 'fmu_g_forums' ) );
            unset( \IPS\Data\Store::i()->settings );
        } else {
            \IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => 1 ), array( 'conf_key=?', 'fmu_g_forums' ) );
            unset( \IPS\Data\Store::i()->settings );
        }

        \IPS\Output::i()->redirect(
            \IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=gmakeup' ),
            \IPS\Settings::i()->fmu_g_forums ? 'fmakeup_toggled_notvisible' : 'fmakeup_toggled_visible'
        );
    }

	/**
	 * Toggle Rules icons state to active or inactive
	 *
	 * @note	Nothing
	 * @return	void
	 */
	protected function showRules()
	{
		/* Toggle rules icon setting */
		if ( \IPS\Settings::i()->fmu_g_rules )
		{
			\IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => 0 ), array( 'conf_key=?', 'fmu_g_rules' ) );
			unset( \IPS\Data\Store::i()->settings );
		} else {
			\IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => 1 ), array( 'conf_key=?', 'fmu_g_rules' ) );
			unset( \IPS\Data\Store::i()->settings );
		}

		\IPS\Output::i()->redirect(
			\IPS\Http\Url::internal( 'app=fmakeup&module=main&controller=gmakeup' ),
			\IPS\Settings::i()->fmu_g_rules ? 'fmakeup_toggled_notvisible' : 'fmakeup_toggled_visible'
		);
	}

    /**
     * Change conf global permissions
     *
     * @return	void
     */
    protected function conf()
    {
        /* INIT */
        $done = FALSE;

        /* Try... */
        if ( \IPS\NO_WRITES or !@chmod( \IPS\ROOT_PATH . '/conf_global.php', 0444 ) )
        {
            \IPS\Output::i()->error( 'conf_not_altered', '2C258/2', 500, '' );
        }

        /* All Done */
        \IPS\Session::i()->log( 'acplogs__security_conf' );
        \IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=core&module=overview&controller=security" ), 'conf_altered' );
    }
}