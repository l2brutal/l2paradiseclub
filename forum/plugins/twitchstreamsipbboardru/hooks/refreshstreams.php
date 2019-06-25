//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook45 extends _HOOK_CLASS_ 
{

	public function refreshstreams( $configuration=array() )
	{
		try
		{
			/* INIT */
			$streams = array();
			$offlineJson = array();
			$onlineJson = array();
			$featuredJson = array();
			$updateJson		= array();
			$removeJson		= array();
			$restricted 	= false;
	
			/* Grabs configuration if it is an AJAX request */
			if( !count($configuration) && \IPS\Request::i()->id )
			{
				$id = \IPS\Request::i()->id;
				$configuration = \IPS\Widget::getConfiguration($id);
			}
	
			/* Gather Updated Streamers List */
			$streamers = $this->gatherStreamers( $configuration );
			
			/* Build array for 1st load */
			foreach( $streamers as $streamer )
			{
				/* If streamer is live, let's sort them into appropriate list */
				if( $streamer['tw_isLive'] )
				{
					switch( $streamer['tw_featStream'])
					{
						case 0:
						$streams['online'][$streamer['tw_name']] = $streamer;
						break;
						case 1:
						$streams['featured'][$streamer['tw_name']] = $streamer;
						break;
						default:
					}
				} else
				{
					$streams['offline'][$streamer['tw_name']] = $streamer;
				}
			}
			
			/* For the AJAX Request */
			if( \IPS\Request::i()->isAjax() && \IPS\Request::i()->id )
			{
				/* Array of current online streams */
				$online = json_decode( \IPS\Request::i()->online, true);
				
				/* Loop through the streamers and process them accordingly */
				if( count( $streamers ) )
				{
					/* Check if we need to do anything to current online streams */
					if( count($online['streams'] ) )
					{
						foreach( $online['streams'] as $s)
						{	
							if( $streamers[$s]['tw_isLive'] )
							{
								$updateJson[] = array(
									'name'		=> $streamers[$s]['tw_name'],
									'views'		=> $streamers[$s]['tw_viewers']
								);
							} else {
								$removeJson[] = array(
									'name'		=> $streamers[$s]['tw_name'],
									'isLive'	=> 0,
									'html'		=> \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' )->streamsblockOffline( $streamers[$s] )	
								);
							}
						}
					}
					
					foreach( $streamers as $name => $newStream )
					{
						if( !in_array($name, $online['streams']) && $newStream['tw_isLive'] == 1 )
						{
							$onlineJson[] = array(
								'name'		=> $name,
								'html'		=> \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' )->streamsblockFeatured( $newStream ),
								'featured'	=> ($newStream['tw_featStream']) ? 1 : 0	
							);
						}
					}
				}
				$emptyJson[] = array(
					'html'		=> \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' )->streamsblockNone( $configuration )
				);
	
				\IPS\Output::i()->json( array( 'online' => $onlineJson, 'update' => $updateJson, 'remove' => $removeJson, 'empty' => $emptyJson ) ); 
				
			}
			return $streams;
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
	 * Gathers Streamer List
	 * 
	 * @param  Array $configuration Array of widget configuration variables
	 * @return Array                Returns a list of streamers
	 */
	public function gatherStreamers( $configuration )
	{
		try
		{
			/* INIT */
			$streamerList = array();
			/* Configuration Variables */
			$gameFilterList = $configuration['tw_games_limit'];
	
			/* Update Streams */
			$this->updateStreams( $configuration );
	
			/* Let's query the database */
			$streamers = \IPS\Db::i()->select( '*', 'tw_streams' );
	
			foreach( $streamers as $streamer )
			{
				/* Check if streamer is game restricted */
				if( $gameFilterList )
				{
					$restricted = $this->restrictedFilter( $gameFilterList, $streamer );
				}
	
				if( !$restricted )
				{
					$streamerList[$streamer['tw_name']] = $streamer;
				}
			}
	
			return $streamerList;
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
	 * Used to filter streamers by game
	 * @param  array 	$gameLimit Array of restricted games
	 * @param  string 	$streamer  Streamer name to be filtered
	 * @return bool     Returns true if streamer is to be filtered
	 */
	public function restrictedFilter( $gameLimit, $streamer )
	{
		try
		{
			$flag = true;
			foreach( $gameLimit as $game )
			{
				if( mb_strstr(mb_strtolower($streamer['tw_game']), $game ) !== false )
				{
					$flag = false;
					break;
				}
			}
			if( $flag == true )
			{
				return true;
			}
			else
			{
				return false;
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
	/**
	 * Inserts updated stream information into Database
	 * 
	 * @param  array  $configuration Widget Configuration
	 * @return none                  None
	 */
	public function updateStreams( $configuration=array() )
	{
		try
		{
			try
			{
				/* Init */
				$online 	= array();
				$streamers	= array();
				$krakenURL	= "";
		
				/* Gathers all Users in Database */
	
				$users = \IPS\Db::i()->select( '*', 'tw_streams', 'tw_name IS NOT NULL' );
	
				if( count( $users ) )
				{
					foreach( $users as $streamer )
					{
						$streamers[] = $streamer['tw_name'];
						$offline[$streamer['tw_name']] = array(
							'id'		=> $streamer['tw_id'],
							'name'		=> $streamer['tw_name'],
							'dname'		=> $streamer['tw_dname'],
							'isLive'	=> $streamer['tw_isLive'],
							'url'		=> $streamer['tw_url'],
							'game'		=> $streamer['tw_game'],
							'viewers'	=> $streamer['tw_viewer']
							);
					}
				}
		
				/* Builds url for Twitch.tv API */
				$krakenURL = (string) \IPS\Http\Url::external( "https://api.twitch.tv/kraken/streams/?" )->setQueryString( array(
					'channel'		=> implode(',', $streamers),
					'client_id'		=> $configuration['tw_client_id'],
					'limit'			=> '100'
					
					) ); 
	
				/* Decode the json */
				try
				{
					$response = \IPS\Http\Url::external( $krakenURL )->request()->get()->decodeJson();
				}
				catch( \IPS\RuntimeException $e) {
					throw $e;
				}
	
				if( is_array( $response ) )
				{
					if( count( $response['streams'] ) )
					{
						/* Loop through streamer data and organize it into an array */			
						foreach( $response['streams'] as $stream )
						{
							$streamer_id = $stream['channel']['_id'];
	
							if( !isset($stream['game'] ) )
							{
								$stream['game'] = 'No Game Set';
							}
	
							if( isset($streamer_id) )
							{
								$insert[] = array(
									'tw_id'				=> $streamer_id,
									'tw_name' 			=> $stream['channel']['name'],
									'tw_dname' 			=> $stream['channel']['display_name'],
									'tw_isLive'			=> 1,
									'tw_url' 			=> $stream['channel']['url'],
									'tw_game' 			=> $stream['game'],
									'tw_viewers'		=> $stream['viewers']
									);
								$online[] = $stream['channel']['name'];
							}
						}
					}
					else
					{
						$online = array();
					}
				}
		
				if( is_array( $offline ) )
				{
					if( is_array( $online ) )
					{
						/* Updates Offline Streamers */
						foreach( $offline as $streamer )
						{
							if( !in_array($streamer['name'], $online) )
							{
								$insert[] = array(
									'tw_id'		=> $streamer['id'],
									'tw_name'	=> $streamer['name'],
									'tw_dname'	=> $streamer['dname'],
									'tw_isLive'	=> 0,
									'tw_url'	=> $streamer['url'],
									'tw_game'	=> $streamer['game'],
									'tw_viewers'=> $streamer['viewers']
									);
							}
						}
					}
				}
			}
			catch( \IPS\RuntimeException $e )
			{
				throw $e;
			}
	
			/* Simplified Queries v1.0.6 */
			if( count( $insert ) )
			{
				try
				{
					\IPS\Db::i()->insert( 'tw_streams', $insert, 1 );
				} 
				catch( \IPS\RuntimeException $e )
				{
					throw $e;
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
