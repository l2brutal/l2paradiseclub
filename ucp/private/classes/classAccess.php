<?php

class Access {

	public static function encrypt($plain) {
		$array_mul = array ( 0 => 213119, 1 => 213247, 2 => 213203, 3 => 213821 ); $array_add = array ( 0 => 2529077, 1 => 2529089, 2 => 2529589, 3 => 2529997 ); $dst = $key = array ( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0 ); for ( $i = 0; $i < strlen ( $plain ); $i++ ) { $dst [ $i ] = $key [ $i ] = ord ( substr ( $plain, $i, 1 ) ); } for ( $i = 0; $i <= 3; $i++ ) { $val [ $i ] = fmod ( ( $key [ $i * 4 + 0 ] + $key [ $i * 4 + 1 ] * 0x100 + $key [ $i * 4 + 2 ] * 0x10000 + $key [ $i * 4 + 3 ] * 0x1000000 ) * $array_mul [ $i ] + $array_add [ $i ], 4294967296 ); } for ( $i = 0; $i <= 3; $i++ ) { $key [ $i * 4 + 0 ] = $val [ $i ] & 0xff; $key [ $i * 4 + 1 ] = $val [ $i ] / 0x100 & 0xff; $key [ $i * 4 + 2 ] = $val [ $i ] / 0x10000 & 0xff; $key [ $i * 4 + 3 ] = $val [ $i ] / 0x1000000 & 0xff; } $dst [ 0 ] = $dst [ 0 ] ^ $key [ 0 ]; for ( $i = 1; $i <= 15; $i++ ) { $dst [ $i ] = $dst [ $i ] ^ $dst [ $i - 1 ] ^ $key [ $i ]; } for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] == 0 ) { $dst [ $i ] = 0x66; } } $encrypted = "0x"; for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] < 16 ) { $encrypted .= "0"; } $encrypted .= dechex ( $dst [ $i ] ); }
		return $encrypted;
	}
	
	public static function login($login, $password) {
		
		return DB::Executa("SELECT TOP 1 account FROM user_auth WHERE account = '".$login."' AND password = ".Access::encrypt($password)."", "DB");
		
	}
	
	public static function logout() {
		
		$_SESSION['acc'] = '';
		$_SESSION['ses'] = '';
		unset($_SESSION['acc']);
		unset($_SESSION['ses']);
		header('Location: ./');
		exit;
		
	}
	
	public static function registerAccess($login) {
		
		$sql = DB::Executa("INSERT INTO site_ucp_lastlogins (login, ip, logdate) VALUES ('".$login."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')", "SITE");
		if(!$sql) { return false; }
		
		$sql = DB::Executa("SELECT TOP 5 * FROM site_ucp_lastlogins WHERE login = '".$login."' ORDER BY logdate DESC", "SITE");
		if(count($sql) > 0) {
			$DATEs = '';
			for($i=0, $c=count($sql); $i < $c; $i++) {
				$DATEs .= $sql[$i]['logdate'].',';
			}
			$DATEs = substr($DATEs, 0, -1);
		} else {
			$DATEs = '';
		}
		
		$sql = DB::Executa("DELETE FROM site_ucp_lastlogins WHERE login = '".$login."' AND logdate NOT IN (".$DATEs.")", "SITE");
		if(!$sql) { return false; }
		
		return true;
		
	}
	
}
