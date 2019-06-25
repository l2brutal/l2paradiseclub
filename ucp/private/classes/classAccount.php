<?php

class Account {

	public static function encrypt($plain) {
		$array_mul = array ( 0 => 213119, 1 => 213247, 2 => 213203, 3 => 213821 ); $array_add = array ( 0 => 2529077, 1 => 2529089, 2 => 2529589, 3 => 2529997 ); $dst = $key = array ( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0 ); for ( $i = 0; $i < strlen ( $plain ); $i++ ) { $dst [ $i ] = $key [ $i ] = ord ( substr ( $plain, $i, 1 ) ); } for ( $i = 0; $i <= 3; $i++ ) { $val [ $i ] = fmod ( ( $key [ $i * 4 + 0 ] + $key [ $i * 4 + 1 ] * 0x100 + $key [ $i * 4 + 2 ] * 0x10000 + $key [ $i * 4 + 3 ] * 0x1000000 ) * $array_mul [ $i ] + $array_add [ $i ], 4294967296 ); } for ( $i = 0; $i <= 3; $i++ ) { $key [ $i * 4 + 0 ] = $val [ $i ] & 0xff; $key [ $i * 4 + 1 ] = $val [ $i ] / 0x100 & 0xff; $key [ $i * 4 + 2 ] = $val [ $i ] / 0x10000 & 0xff; $key [ $i * 4 + 3 ] = $val [ $i ] / 0x1000000 & 0xff; } $dst [ 0 ] = $dst [ 0 ] ^ $key [ 0 ]; for ( $i = 1; $i <= 15; $i++ ) { $dst [ $i ] = $dst [ $i ] ^ $dst [ $i - 1 ] ^ $key [ $i ]; } for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] == 0 ) { $dst [ $i ] = 0x66; } } $encrypted = "0x"; for ( $i = 0; $i <= 15; $i++ ) { if ( $dst [ $i ] < 16 ) { $encrypted .= "0"; } $encrypted .= dechex ( $dst [ $i ] ); }
		return $encrypted;
	}
	
	public static function checkLoginExists($login) {
		
		$sql = DB::Executa("SELECT TOP 1 *, name AS login FROM ssn WHERE name = '".$login."'", "DB");
		return $sql;
		
	}
	
	public static function checkEmailExists($email) {
		
		$sql = DB::Executa("SELECT name AS login, email FROM ssn WHERE email = '".$email."'", "DB");
		return $sql;
		
	}
	
	public static function insertRegCode($login, $confirm_code) {
		
		$sql = DB::Executa("INSERT INTO site_reg_code (account, code, date) VALUES ('".$login."', '".$confirm_code."', '".time()."')", "SITE");
		return $sql;
		
	}
	
	public static function Register($login, $pass, $accLvl, $email) {
		
		if($accLvl == '0') { $accLvl = '1'; } else { $accLvl = '0'; }
		
		$ssn = rand(1000000,9999999).rand(100000,999999);
		if(count(DB::Executa("SELECT TOP 1 ssn FROM ssn WHERE ssn = '".$ssn."'", "DB")) > 0) {
			$ssn = rand(1000000,9999999).rand(100000,999999);
			if(count(DB::Executa("SELECT TOP 1 ssn FROM ssn WHERE ssn = '".$ssn."'", "DB")) > 0) {
				$ssn = rand(1000000,9999999).rand(100000,999999);
				if(count(DB::Executa("SELECT TOP 1 ssn FROM ssn WHERE ssn = '".$ssn."'", "DB")) > 0) {
					$ssn = rand(1000000,9999999).rand(100000,999999);
					if(count(DB::Executa("SELECT TOP 1 ssn FROM ssn WHERE ssn = '".$ssn."'", "DB")) > 0) {
						$ssn = rand(1000000,9999999).rand(100000,999999);
						if(count(DB::Executa("SELECT TOP 1 ssn FROM ssn WHERE ssn = '".$ssn."'", "DB")) > 0) {
							return false;
						}
					}
				}
			}
		}
		
		if(!DB::Executa("INSERT INTO ssn (ssn, name, email, job, phone, zip, addr_main, addr_etc, account_num) VALUES ('".$ssn."', '".$login."', '".$email."', '0', '', '', '', '', '1')", "DB")) {
			return false;
		}
		
		if(!DB::Executa("INSERT INTO user_auth (account, password, quiz1, quiz2, answer1, answer2) VALUES ('".$login."', ".Account::encrypt($pass).", '', '', ".Account::encrypt($pass).", ".Account::encrypt($pass).")", "DB")) {
			DB::Executa("DELETE FROM ssn WHERE name = '".$login."'", "DB");
			return false;
		}
		
		if(!DB::Executa("INSERT INTO user_account (account, pay_stat) VALUES ('".$login."', '".$accLvl."')", "DB")) {
			DB::Executa("DELETE FROM ssn WHERE name = '".$login."'", "DB");
			DB::Executa("DELETE FROM user_auth WHERE account = '".$login."'", "DB");
			return false;
		}
		
		if(!DB::Executa("INSERT INTO user_info (account, ssn, kind) VALUES ('".$login."', '".$ssn."', '99')", "DB")) {
			DB::Executa("DELETE FROM ssn WHERE name = '".$login."'", "DB");
			DB::Executa("DELETE FROM user_auth WHERE account = '".$login."'", "DB");
			DB::Executa("DELETE FROM user_account WHERE account = '".$login."'", "DB");
			return false;
		}
		
		return true;
		
	}
	
	public static function insertForgotCode($val, $code) {
		
		$sql = DB::Executa("INSERT INTO site_forgotpass VALUES ('".$val."', '".$code."', '".time()."')", "SITE");
		return $sql;
		
	}
	
	public static function deleteForgotExpiredCodes() {
		
		$sql = DB::Executa("DELETE FROM site_forgotpass WHERE date < '".(time()-86400)."'", "SITE");
		return $sql;
		
	}
	
	public static function checkForgotCode($acc, $code) {
		
		$sql = DB::Executa("SELECT TOP 1 account, code FROM site_forgotpass WHERE account = '".$acc."' AND code = '".$code."'", "SITE");
		return $sql;
		
	}
	
	public static function deleteForgotCode($val) {
		
		$sql = DB::Executa("DELETE FROM site_forgotpass WHERE account = '".$val."'", "SITE");
		return $sql;
		
	}
	
	public static function updatePass($pass, $login) {
		
		$sql = DB::Executa("UPDATE user_auth SET password = ".Account::encrypt($pass)." WHERE account = '".$login."'", "DB");
		return $sql;
		
	}
	
	public static function updatePassGroup($pass, $logins) {
		
		$sql = DB::Executa("UPDATE user_auth SET password = ".Account::encrypt($pass)." WHERE account IN (".$logins.")", "DB");
		return $sql;
		
	}
	
	public static function deleteRegExpiredCodes() {
		
		$expireds = DB::Executa("SELECT account FROM site_reg_code WHERE date < '".(time()-86400)."'", "SITE");
		if(count($expireds) > 0) {
			
			$sql = DB::Executa("DELETE FROM site_reg_code WHERE date < '".(time()-86400)."'", "SITE");
			if(!$sql) { return false; }
			
			$accs="";
			for($i=0; $i < count($expireds); $i++) {
				$accs .= "'".$expireds[$i]['account']."', ";
			}
			$accs = substr($accs, 0, -2);
			
			$sql = DB::Executa("SELECT account FROM user_account WHERE account IN (".$accs.") AND pay_stat = '0'", "DB");
			if(count($sql) > 0) {
				for($i=0; $i < count($sql); $i++) {
					DB::Executa("DELETE FROM ssn WHERE name = '".$sql[$i]['account']."'", "DB");
					DB::Executa("DELETE FROM user_auth WHERE account = '".$sql[$i]['account']."'", "DB");
					DB::Executa("DELETE FROM user_account WHERE account = '".$sql[$i]['account']."'", "DB");
					DB::Executa("DELETE FROM user_info WHERE account = '".$sql[$i]['account']."'", "DB");
				}
			}
			
		}
		
		return true;
		
	}

	public static function checkRegCode($acc, $code) {
		
		$sql = DB::Executa("SELECT TOP 1 account, code FROM site_reg_code WHERE account = '".$acc."' AND code = '".$code."'", "SITE");
		return $sql;
		
	}
	
	public static function deleteRegCode($val) {
		
		$sql = DB::Executa("DELETE FROM site_reg_code WHERE account = '".$val."'", "SITE");
		return $sql;
		
	}
	
	public static function updateAccessLevel($access, $login) {
		
		if($access == '0') { $access = '1'; } else { $access = '0'; }
		$sql = DB::Executa("UPDATE user_auth SET access_level = '".$access."' WHERE account = '".$login."'", "DB");
		return $sql;
		
	}
	
}
