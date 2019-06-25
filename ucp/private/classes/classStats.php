<?php

class Stats {

	public static function TopPvP($limit) {
		
		$sql = DB::Executa("SELECT TOP ".$limit." C.char_name, C.duel AS pvpkills, C.PK AS pkkills, CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, C.use_time AS onlinetime, D.name AS clan_name FROM user_data AS C LEFT JOIN pledge AS D ON D.pledge_id = C.pledge_id WHERE C.builder = '0' AND C.char_name NOT LIKE '%\_%' ESCAPE '\' ORDER BY C.duel DESC, C.PK DESC, C.use_time DESC, C.char_name ASC", "WORLD");
		return $sql;
		
	}
	
	public static function TopPk($limit) {
		
		$sql = DB::Executa("SELECT TOP ".$limit." C.char_name, C.duel AS pvpkills, C.PK AS pkkills, CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, C.use_time AS onlinetime, D.name AS clan_name FROM user_data AS C LEFT JOIN pledge AS D ON D.pledge_id = C.pledge_id WHERE C.builder = '0' AND C.char_name NOT LIKE '%\_%' ESCAPE '\' ORDER BY C.PK DESC, C.duel DESC, C.use_time DESC, C.char_name ASC", "WORLD");
		return $sql;
		
	}
	
	public static function TopOnline($limit) {
		
		$sql = DB::Executa("SELECT TOP ".$limit." C.char_name, C.duel AS pvpkills, C.PK AS pkkills, CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, C.use_time AS onlinetime, D.name AS clan_name FROM user_data AS C LEFT JOIN pledge AS D ON D.pledge_id = C.pledge_id WHERE C.builder = '0' AND C.char_name NOT LIKE '%\_%' ESCAPE '\' ORDER BY C.use_time DESC, C.duel DESC, C.PK DESC, C.char_name ASC", "WORLD");
		return $sql;
		
	}
	
	public static function TopLevel($limit) {
		
		$sql = DB::Executa("SELECT TOP ".$limit." C.char_name, C.duel AS pvpkills, C.PK AS pkkills, CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, C.use_time AS onlinetime,  C.Lev AS level, D.name AS clan_name FROM user_data AS C LEFT JOIN pledge AS D ON D.pledge_id = C.pledge_id WHERE C.builder = '0' AND C.char_name NOT LIKE '%\_%' ESCAPE '\' ORDER BY C.Lev DESC, C.Exp DESC, C.use_time DESC, C.char_name ASC", "WORLD");
		return $sql;
		
	}
	
	public static function TopAdena($limit, $adnBillionItem) {

		$sql = "
		SELECT TOP ".$limit." 
			C.char_name, 
			CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, 
			C.use_time AS onlinetime, 
			C.Lev AS level, 
			D.name AS clan_name, 
			(";
		
		if($adnBillionItem != 0) {
			$sql .= "
					ISNULL( (SELECT TOP 1 (CONVERT(bigint, SUM(I2.amount)) * 1000000000) FROM user_item AS I2 WHERE I2.char_id = C.char_id AND I2.item_type = '".$adnBillionItem."') , 0)
					+
				";
		}
				
		$sql .= "
				ISNULL( (SELECT TOP 1 CONVERT(bigint, SUM(I1.amount)) FROM user_item AS I1 WHERE I1.char_id = C.char_id AND I1.item_type = '57') , 0)
			) AS adenas
		FROM 
			user_data AS C 
		LEFT JOIN 
			pledge AS D ON D.pledge_id = C.pledge_id 
		WHERE
			C.builder = '0' 
			AND C.char_name NOT LIKE '%\_%' ESCAPE '\'
		ORDER BY
			adenas DESC, C.use_time DESC, char_name ASC
		";
		return DB::Executa($sql);
		
	}
	
	public static function TopClan($limit) {
		
		$sql = DB::Executa("
			SELECT
			TOP ".$limit."
				C.name AS clan_name,
				C.skill_level AS clan_level,
				C.name_value AS reputation_score,
				A.name AS ally_name,
				P.char_name,
				(SELECT COUNT(*) FROM user_data WHERE pledge_id = C.pledge_id) AS membros
			FROM
				Pledge AS C
			LEFT JOIN
				Alliance AS A ON A.id = C.alliance_id
			LEFT JOIN
				user_data AS P ON P.char_id = C.ruler_id
			WHERE
				P.builder = '0' 
			ORDER BY
				C.skill_level DESC, C.name_value DESC, membros DESC
		", "WORLD");
		return $sql;
		
	}
	
	public static function OlympiadRanking() {
		
		$sql = DB::Executa("
			SELECT 
				U.char_name, 
				CASE WHEN (U.login > U.logout OR U.logout IS NULL) AND U.login IS NOT NULL THEN 1 ELSE 0 END AS online, 
				C.name AS clan_name, 
				U.subjob0_class AS base, 
				O.olympiad_point AS olympiad_points
			FROM
				user_nobless AS O
			LEFT JOIN
				user_data AS U ON U.char_id = O.char_id
			LEFT JOIN
				pledge AS C ON U.pledge_id = C.pledge_id
			WHERE
				U.builder = '0' 
				AND U.char_name NOT LIKE '%\_%' ESCAPE '\'
			ORDER BY
				O.olympiad_point DESC
		", "WORLD");
		return $sql;
		
	}
	
	public static function OlympiadAllHeroes() {
		
		$sql = DB::Executa("
			SELECT 
				C.char_name, 
				CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, 
				D.name AS clan_name, 
				A.name AS ally_name,
				C.subjob0_class AS base, 
				H.win_count AS count
			FROM
				user_nobless AS H
			LEFT JOIN
				user_data AS C ON C.char_id = H.char_id
			LEFT JOIN
				Pledge AS D ON D.pledge_id = C.pledge_id 
			LEFT JOIN
				Alliance AS A ON A.id = D.alliance_id
			WHERE
				H.win_count > '0' 
				AND C.builder = '0'
				AND C.char_name NOT LIKE '%\_%' ESCAPE '\'
			ORDER BY H.win_count DESC, C.subjob0_class ASC, C.char_name ASC
		", "WORLD");
		return $sql;
		
	}
	
	public static function OlympiadCurrentHeroes() {
		
		$sql = DB::Executa("
			SELECT 
				C.char_name, 
				CASE WHEN (C.login > C.logout OR C.logout IS NULL) AND C.login IS NOT NULL THEN 1 ELSE 0 END AS online, 
				D.name AS clan_name, 
				A.name AS ally_name,
				C.subjob0_class AS base
			FROM
				user_nobless AS H
			LEFT JOIN
				user_data AS C ON C.char_id = H.char_id
			LEFT JOIN
				Pledge AS D ON D.pledge_id = C.pledge_id 
			LEFT JOIN
				Alliance AS A ON A.id = D.alliance_id
			WHERE
				H.hero_type IN (1,2) 
				AND C.builder = '0'
				AND C.char_name NOT LIKE '%\_%' ESCAPE '\'
			ORDER BY H.win_count DESC, C.subjob0_class ASC, C.char_name ASC
		", "WORLD");
		return $sql;
		
	}
	
	public static function RaidbossStatus() {
		
		$sql = DB::Executa("
			SELECT
				npc_db_name, 
				alive, 
				time_low
			FROM
				npc_boss
			WHERE
				npc_db_name NOT LIKE 'sentinel_guard_%' 
				AND npc_db_name NOT LIKE '%_siege_%' 
				AND npc_db_name NOT LIKE '%_ordery%'
				AND npc_db_name NOT LIKE 'RestlessAzit_%'
				AND npc_db_name NOT LIKE 'farmazit%'
				AND npc_db_name NOT LIKE 'br_xmas_invisible_npc'
				AND npc_db_name NOT LIKE '%_ordery%'
				AND npc_db_name NOT LIKE 'acmboss%' 
				AND npc_db_name NOT LIKE '%b02_%' 
				AND npc_db_name NOT LIKE 'tbb%' 
				AND npc_db_name NOT LIKE 'tbf%' 
				AND npc_db_name NOT LIKE 'nurka%'  
				AND npc_db_name NOT LIKE 'devastated_%'
				AND npc_db_name NOT LIKE 'fortress_%'
				AND npc_db_name NOT LIKE '%_castle_%'
				AND npc_db_name NOT LIKE '%_dominion_%'
			ORDER BY
				time_low DESC, npc_db_name ASC
		", "WORLD");
		return $sql;
		
	}
	
	public static function Siege() {
		
		$sql = DB::Executa("
			SELECT
				CAS.id,
				CAS.name,
				CAS.next_war_time AS sdate, 
				CAS.tax_rate AS stax, 
				P.char_name, 
				C.name AS clan_name, 
				A.name AS ally_name,
				CAS.id
			FROM
				castle AS CAS
			LEFT JOIN
				Pledge AS C ON C.pledge_id = CAS.pledge_id
			LEFT JOIN
				Alliance AS A ON A.id = C.alliance_id
			LEFT JOIN
				user_data AS P ON P.char_id = C.ruler_id
		", "WORLD");
		return $sql;
		
	}
	
	public static function SiegeParticipants($castle_id) {
		
		$sql = DB::Executa("
			SELECT
				W.type, 
				C.name AS clan_name
			FROM
				castle_war AS W
			INNER JOIN
				Pledge AS C ON C.pledge_id = W.pledge_id
			WHERE
				W.castle_id = '".$castle_id."'
		", "WORLD");
		return $sql;
		
	}
	
	public static function BossJwlLoc($bossJwlIds) {
		
		$sql = DB::Executa("
		SELECT
			I.char_id AS owner_id, 
			I.item_type AS item_id, 
			SUM(I.amount) AS count, 
			U.char_name, 
			D.name AS clan_name
		FROM
			user_item AS I
		INNER JOIN
			user_data AS U ON U.char_id = I.char_id
		LEFT JOIN
			pledge AS D ON D.pledge_id = U.pledge_id
		WHERE
			I.item_type IN (".$bossJwlIds.")
		GROUP BY
			I.char_id, U.char_name, D.name, I.item_type
		ORDER BY
			count DESC, U.char_name ASC
		");
		return $sql;
		
	}

}
