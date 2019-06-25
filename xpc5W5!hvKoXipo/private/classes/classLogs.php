<?php

class Logs {

	public static function countShopLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (log_cid = '".$buscarN."' OR log_item_id = '".$buscarN."' OR log_item_name LIKE '%".$buscar."%' OR log_item_sa LIKE '%".$buscar."%' OR log_pack_id = '".$buscarN."' OR log_amount = '".$buscarN."' OR log_price = '".$buscarN."' OR log_item_objs_id LIKE '%".$buscar."%' OR log_account LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_shop ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listShopLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (log_cid = '".$buscarN."' OR log_item_id = '".$buscarN."' OR log_item_name LIKE '%".$buscar."%' OR log_item_sa LIKE '%".$buscar."%' OR log_pack_id = '".$buscarN."' OR log_amount = '".$buscarN."' OR log_price = '".$buscarN."' OR log_item_objs_id LIKE '%".$buscar."%' OR log_account LIKE '%".$buscar."%')";
		}
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY log_date DESC) AS row, t1.* FROM site_log_shop AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['log_cid']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		
	}

	public static function countServicesLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (log_cid = '".$buscarN."' OR log_account LIKE '%".$buscar."%' OR log_value LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_services ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listServicesLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (log_cid = '".$buscarN."' OR log_account LIKE '%".$buscar."%' OR log_value LIKE '%".$buscar."%')";
		}
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY log_date DESC) AS row, t1.* FROM site_log_services AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['log_cid']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		return DB::Executa($sql, "SITE");
		
	}

	public static function countTransfDonateLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR remetente LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_transfercoins ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listTransfDonateLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR remetente LIKE '%".$buscar."%')";
		}
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY tdata DESC) AS row, t1.* FROM site_log_transfercoins AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['destinatario_char']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		
	}

	public static function countConvDonateLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR account LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_convertcoins ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listConvDonateLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR account LIKE '%".$buscar."%')";
		}
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY cdata DESC) AS row, t1.* FROM site_log_convertcoins AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['destinatario']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		
	}

	public static function countAdminLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			$whereAdd .= "WHERE (log_value LIKE '%".$buscar."%' OR log_ip LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) as quant FROM site_log_admin ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listAdminLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			$whereAdd .= "WHERE (log_value LIKE '%".$buscar."%' OR log_ip LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY log_date DESC) AS row, t1.* FROM site_log_admin AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."";
		return DB::Executa($sql, "SITE");
		
	}

	public static function countConvOnlineDonateLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR account LIKE '%".$buscar."%')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_convertcoins_online AS T INNER JOIN user_data AS C ON C.char_id = T.personagem ".$whereAdd;
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY cdata DESC) AS row, t1.* FROM site_log_convertcoins_online AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['personagem']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		return DB::Executa($sql, "SITE");
		
	}

	public static function listConvOnlineDonateLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (quantidade = '".$buscarN."' OR account LIKE '%".$buscar."%')";
		}
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY cdata DESC) AS row, t1.* FROM site_log_convertcoins_online AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['personagem']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		
	}

	public static function countEnchantLogs($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (oid = '".$buscarN."' OR cid = '".$buscarN."' OR iid = '".$buscarN."' OR ench_old = '".$buscarN."' OR ench_new = '".$buscarN."' OR price = '".$buscarN."')";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_log_enchant ".$whereAdd;
		return DB::Executa($sql);
		
	}

	public static function listEnchantLogs($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			if(is_numeric($buscar)) { $buscarN = $buscar; } else { $buscarN = 0; }
			$whereAdd .= "WHERE (oid = '".$buscarN."' OR cid = '".$buscarN."' OR iid = '".$buscarN."' OR ench_old = '".$buscarN."' OR ench_new = '".$buscarN."' OR price = '".$buscarN."')";
		}
		
		$sql = DB::Executa("SELECT * FROM site_log_enchant ".$whereAdd." ORDER BY edate DESC LIMIT ".$pgBeg.", ".$pgMax, "SITE");
		if(count($sql) > 0) {
			for($i=0; $i < count($sql); $i++) {
				$sql2 = DB::Executa("SELECT char_name FROM user_data WHERE char_id = '".$sql[$i]['cid']."'", "WORLD");
				$merged[] = array_merge($sql[$i], $sql2[0]);
			}
			return $merged;
		} else {
			return $sql;
		}
		
	}

}
