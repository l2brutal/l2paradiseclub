<?php

/**
 * @brief       List Active Record Class
 * @author      <a href='http://www.bbcode.it'>InvisionHQ - G. Venturini</a>
 * @copyright   (c) 2016 InvisionHQ - G. Venturini
 * @package     IPS Social Suite
 * @subpackage  Fmakeup
 * @since       1.0.0
 * @version     1.0.0
 */

namespace IPS\fmakeup;

if( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER[ 'SERVER_PROTOCOL' ] ) ? $_SERVER[ 'SERVER_PROTOCOL' ] :
            'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _Lista extends \IPS\Patterns\ActiveRecord
{
    
    /**
     * @brief    [ActiveRecord] Multiton Store
     */
    protected static $multitons;
    
    /**
     * @brief    [ActiveRecord] Database table
     */
    public static $databaseTable = 'forums_forums';
    
    /**
     * @brief    [ActiveRecord] Database Prefix
     */
    public static $databasePrefix = "";


    /**
     * Save Changed Columns
     *
     * @return	void
     */
    public function save()
    {
        parent::save();
        unset( \IPS\Data\Cache::i()->fmakeup );
    }

    /**
     * [ActiveRecord] Delete Record
     *
     * @return	void
     */
    public function delete()
    {
        /* Delete */
        parent::delete();

        /* Empty the cache */
        unset( \IPS\Data\Cache::i()->fmakeup );
        if ( !\IPS\Db::i()->select( 'COUNT(*)', 'badges_hq', 'status=1' )->first() )
        {
            \IPS\Db::i()->update( 'core_sys_conf_settings', array( 'conf_value' => 0 ), array( 'conf_key=?', 'ads_exist' ) );
            unset( \IPS\Data\Store::i()->settings );
        }
    }
	
}