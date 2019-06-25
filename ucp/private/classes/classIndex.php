<?php

class Index {
	
	public static function accData($login) {
		
		$sql = DB::Executa("
		SELECT TOP 1
			ssn.name AS login, 
			ssn.email, 
			(DATEDIFF(SECOND,{d '1970-01-01'}, user_info.create_date) + 10800) AS created_time, 
			(CONVERT(bigint, (DATEDIFF(SECOND,{d '1970-01-01'}, user_account.last_login) + 10800)) * 1000) AS lastactive, 
			user_account.last_ip AS lastIP
		FROM
			ssn
		INNER JOIN
			user_info ON user_info.account = ssn.name
		INNER JOIN
			user_account ON user_info.account = user_account.account
		WHERE
			ssn.name = '".$login."'
		", "DB");
		if(!$sql) { return false; }
		
		$countChars = DB::Executa("SELECT COUNT(*) AS chars FROM user_data WHERE account_name = '".$login."' AND char_name NOT LIKE '%\_%' ESCAPE '\'", "WORLD");
		
		$return[0] = array_merge($sql[0], $countChars[0]);
		return $return;
		
	}
	
	public static function lastLogins($login) {
		
		$sql = DB::Executa("SELECT TOP 5 * FROM site_ucp_lastlogins WHERE login = '".$login."' ORDER BY logdate DESC", "SITE");
		return $sql;
		
	}
	
	public static function findChars($login) {
		
		$sql = DB::Executa("
		SELECT
			TOP 7
			C.char_id AS obj_Id, 
			C.use_time AS onlinetime, 
			C.char_name, 
			C.nickname AS title, 
			(CONVERT(bigint, (DATEDIFF(SECOND,{d '1970-01-01'}, C.login) + 10800)) * 1000) AS lastAccess, 
			CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, 
			C.subjob0_class AS base_class, 
			C.subjob1_class AS subclass1, 
			C.subjob2_class AS subclass2, 
			C.subjob3_class AS subclass3, 
			C.Lev AS level, 
			C.gender AS sex, 
			C.Duel AS pvpkills, 
			C.PK AS pkkills, 
			C.Align AS karma, 
			CLAN.name AS clan_name, 
			ALLY.name AS ally_name,
			CASE WHEN UN1.nobless_type IS NOT NULL THEN 1 ELSE 0 END AS nobless,  
			CASE WHEN UN2.hero_type IS NOT NULL THEN 1 ELSE 0 END AS hero_end
		FROM
			user_data AS C
		LEFT JOIN
			Pledge AS CLAN ON CLAN.pledge_id = C.pledge_id
		LEFT JOIN
			Alliance AS ALLY ON ALLY.id = CLAN.alliance_id
		LEFT JOIN
			user_nobless AS UN1 ON UN1.char_id = C.char_id AND UN1.nobless_type > 0
		LEFT JOIN
			user_nobless AS UN2 ON UN2.char_id = C.char_id AND UN2.hero_type > 0
		WHERE
			C.account_name = '".$login."'
			AND C.char_name NOT LIKE '%\_%' ESCAPE '\'
		", "WORLD");
		return $sql;
		
	}
	
}
