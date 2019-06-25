<?php 

  // Modifica a zona de tempo a ser utilizada. Disnovível desde o PHP 5.1
  date_default_timezone_set('America/Bahia');

$script_tz = date_default_timezone_get();

if (strcmp($script_tz, ini_get('date.timezone'))){
    echo '';
} else {
    echo '';
}

  // server_status = online/offline
  $server_status = 'offline';
  // player_online = number or GM
  $players_online = mt_rand(0,0);
  // Multiple player online
  $countMultiple = 1.0;
  // server operational = OBT/BETA/LIVE
  $server_operational = 'LIVE';
  // server time
  $server_time = date("F j, Y, g:i a");
  // Countdown
  $countdown = '';
  // Video clear/none
  $video = ''; 
  // Twitch
  $twitch = 'none';


  // RANKS
  $ranks_warning = '';
  $enable_ranking = 'none';


  // LINKS DO SITE
  $register_now = '/ucp/?page=register';
  $forum = "/forum";
  $server_info = "/forum/topic/5458-l2paradise-obt-informations/";
  $donate = '/ucp/';
  
  // DOWNLOAD LINKS
  $download_game = 'downloads.php'; 

  $patch_mega = 'https://mega.nz';
  $patch_mediafire = 'https://mediafire.com';
  $patch_4shared = 'https://4shared.com';

  $client_option1 = 'https://drive.google.com/open?id=1DXjWp0cu3b4Xtl5dVAc5y3FRBtc8mSge';
  $client_option2 = 'https://drive.google.com/open?id=1DXjWp0cu3b4Xtl5dVAc5y3FRBtc8mSge';
  $client_option3 = 'https://drive.google.com/open?id=1DXjWp0cu3b4Xtl5dVAc5y3FRBtc8mSge';

  $footer = 'Lineage 2 Paradise - All right reserved to NCSOFT';

  // Hide/Show - Lastest Forums
  // Empty to show content or none
  $hide_forum = 'none';
  $hide_news = '';
  $hide_news_box = 'none';

 ?>
