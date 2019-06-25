<?php

class Configs {
	
	public static function encrypt($plain) {
		$array_mul = array ( 0 => 213119, 1 => 213247, 2 => 213203, 3 => 213821 ); $array_add = array ( 0 => 2529077, 1 => 2529089, 2 => 2529589, 3 => 2529997 ); $dst = $key = array ( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0 ); for ( $i = 0; $i < strlen ( $plain ); $i++ ) { $dst [ $i ] = $key [ $i ] = ord ( substr ( $plain, $i, 1 ) ); } for ( $i = 0; $i <= 3; $i++ ) { $val [ $i ] = fmod ( ( $key [ $i * 4 + 0 ] + $key [ $i * 4 + 1 ] * 0x100 + $key [ $i * 4 + 2 ] * 0x10000 + $key [ $i * 4 + 3 ] * 0x1000000 ) * $array_mul [ $i ] + $array_add [ $i ], 4294967296 ); } for ( $i = 0; $i <= 3; $i++ ) { $key [ $i * 4 + 0 ] = $val [ $i ] & 0xff; $key [ $i * 4 + 1 ] = $val [ $i ] / 0x100 & 0xff; $key [ $i * 4 + 2 ] = $val [ $i ] / 0x10000 & 0xff; $key [ $i * 4 + 3 ] = $val [ $i ] / 0x1000000 & 0xff; } $dst [ 0 ] = $dst [ 0 ] ^ $key [ 0 ]; for ( $i = 1; $i <= 15; $i++ ) { $dst [ $i ] = $dst [ $i ] ^ $dst [ $i - 1 ] ^ $key [ $i ]; } for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] == 0 ) { $dst [ $i ] = 0x66; } } $encrypted = "0x"; for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] < 16 ) { $encrypted .= "0"; } $encrypted .= dechex ( $dst [ $i ] ); }
		return $encrypted;
	}
	
	public static function accData($login) {
		
		$sql = DB::Executa("SELECT TOP 1 *, name AS login FROM ssn WHERE name = '".$login."'", "DB");
		return $sql;
		
	}
	
	public static function changeData($acc, $pass) {
		
		$sql = DB::Executa("UPDATE user_auth SET password = ".Configs::encrypt($pass)." WHERE account = '".$acc."'", "DB");
		return $sql;
		
	}
	
	public static function checkLoginExists($login) {
		
		$sql = DB::Executa("SELECT TOP 1 name AS login, email FROM ssn WHERE name = '".$login."'", "DB");
		return $sql;
		
	}
	
	public static function checkEmailExists($email) {
		
		$sql = DB::Executa("SELECT name AS login, email FROM ssn WHERE email = '".$email."'", "DB");
		return $sql;
		
	}
	
	public static function insertEmailCode($email, $code, $acc) {
		
		$sql = DB::Executa("INSERT INTO site_emailchange VALUES ('".$acc."', '".$email."', '".$code."', '".time()."')", "SITE");
		return $sql;
		
	}
	
	public static function checkEmailCode($acc, $code) {
		
		$sql = DB::Executa("SELECT TOP 1 * FROM site_emailchange WHERE account = '".$acc."' AND code = '".$code."'", "SITE");
		return $sql;
		
	}
	
	public static function deleteEmailExpiredCodes() {
		
		$expireds = DB::Executa("SELECT * FROM site_emailchange WHERE date < '".(time()-86400)."'", "SITE");
		if(count($expireds) > 0) {
			
			$sql = DB::Executa("DELETE FROM site_emailchange WHERE date < '".(time()-86400)."'", "SITE");
			return $sql;
			
		}
		
		return true;
		
	}

	public static function deleteEmailCode($val) {
		
		$sql = DB::Executa("DELETE FROM site_emailchange WHERE account = '".$val."'", "SITE");
		return $sql;
		
	}

	public static function updateEmail($email, $login) {
		
		$sql = DB::Executa("UPDATE ssn SET email = '".$email."' WHERE name = '".$login."'", "DB");
		return $sql;
		
	}
	
}
