<?php
/**
 * @brief		File Storage Extension: FileStorage
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	(BD4) Moods
 * @since		22 Dec 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\bdmoods\extensions\core\FileStorage;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * File Storage Extension: FileStorage
 */
class _FileStorage
{
	/**
	 * Count stored files
	 *
	 * @return	int
	 */
	public function count()
	{
		return \IPS\Db::i()->select( 'COUNT(*)', 'bdmoods_moods', 'mood_image IS NOT NULL' )->first();
	}
	
	/**
	 * Move stored files
	 *
	 * @param	int			$offset					This will be sent starting with 0, increasing to get all files stored by this extension
	 * @param	int			$storageConfiguration	New storage configuration ID
	 * @param	int|NULL	$oldConfiguration		Old storage configuration ID
	 * @throws	\UnderflowException					When file record doesn't exist. Indicating there are no more files to move
	 * @return	void|int							An offset integer to use on the next cycle, or nothing
	 */
	public function move( $offset, $storageConfiguration, $oldConfiguration=NULL )
	{
		$record	= \IPS\Db::i()->select( '*', 'bdmoods_moods', 'mood_image IS NOT NULL', 'mood_id', array( $offset, 1 ) )->first();

        try
		{
			$file = \IPS\File::get( $oldConfiguration ?: 'bdmoods_FileStorage', $record['mood_image'] )->move( $storageConfiguration );
			\IPS\Db::i()->update( 'bdmoods_moods', array( 'mood_image' => (string) $file ), array( 'mood_id=?', $record['mood_id'] ) );
		}
		catch( \Exception $e )
		{
			/* Any issues are logged and the \IPS\Db::i()->update not run as the exception is thrown */
		}
	}

	/**
	 * Check if a file is valid
	 *
	 * @param	\IPS\Http\Url	$file		The file to check
	 * @return	bool
	 */
	public function isValidFile( $file )
	{
		try
		{
			$record	= \IPS\Db::i()->select( '*', 'bdmoods_moods', array( 'mood_image=?', (string) $file ) )->first();

			return TRUE;
		}
		catch ( \UnderflowException $e )
		{
			return FALSE;
		}
	}

	/**
	 * Delete all stored files
	 *
	 * @return	void
	 */
	public function delete()
	{
		foreach( \IPS\Db::i()->select( '*', 'bdmoods_moods', 'mood_image IS NOT NULL' ) as $file )
		{
			try
			{
				\IPS\File::get( 'bdmoods_FileStorage', $file['mood_image'] )->delete();
			}
			catch( \Exception $e ){}
		}
	}
}