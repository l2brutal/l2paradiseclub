<?php
namespace IPS\bdmoods;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

class _Mood extends \IPS\Patterns\ActiveRecord
{
	/**
	 * @brief	[ActiveRecord] Multiton Store
	 */
	protected static $multitons;

	/**
	 * @brief	[ActiveRecord] Database Table
	 */
	public static $databaseTable = 'bdmoods_moods';
		
	/**
	 * @brief	[ActiveRecord] ID Database Column
	 */
	public static $databaseColumnId = 'mood_id';
	

	/**
	 * Load Record
	 *
	 * @see		\IPS\Db::build
	 * @param	int|string	$id					ID
	 * @param	string		$idField			The database column that the $id parameter pertains to (NULL will use static::$databaseColumnId)
	 * @param	mixed		$extraWhereClause	Additional where clause(s) (see \IPS\Db::build for details)
	 * @return	static
	 * @throws	\InvalidArgumentException
	 * @throws	\OutOfRangeException
	 */
	public static function load( $id, $idField=NULL, $extraWhereClause=NULL )
	{	
        
        try	{
            if( $id === NULL OR $id === 0 OR $id === '' ) {
				$classname = get_called_class();
				return new $classname;
			}
			else {
                return parent::load( $id, $idField, $extraWhereClause );		
            }
        }
        catch ( \OutOfRangeException $e ) {
			$classname = get_called_class();
			return new $classname;
        }
	}
    

	/**
	 * Get Moods
	 *
	 * @return	array
	 */
	public static function moods()
	{

		return iterator_to_array( \IPS\Db::i()->select( '*', 'bdmoods_moods', NULL,'mood_title')->setKeyField( 'mood_id' ));
    }
    
    
    public function get_image($title=null) {
        
        return "<img src='".\IPS\File::get('bdmoods_FileStorage',$this->mood_image)->url."' alt='".$this->mood_title."' data-ipsTooltip _title='".\IPS\Member::loggedIn()->language()->addToStack( 'bd_moods_tooltip',FALSE, array('sprintf'=>array($title)))."' style='max-width:".\IPS\Settings::i()->bd_moods_imgmaxwidth."px;max-height:".\IPS\Settings::i()->bd_moods_imgmaxheight."px' />";
    }
    
    public function get_text($member) {
        if (!$member instanceof \IPS\Member) {
            $member = \IPS\Member::load($member);
        }

        if ($member->bdm_moodtext=="" ) {
            return $this->mood_title;
        }
        else {
            return (!$member->group['g_bdm_canUseCustom'] ? $this->mood_title: $member->bdm_moodtext);
        }
    }
}
