<?php

class Donate {
	
	public static function listChars($acc) {
		
		$sql = DB::Executa("SELECT TOP 7 char_name, char_id AS obj_Id FROM user_data WHERE account_name = '".$acc."' AND char_name NOT LIKE '%\_%' ESCAPE '\'", "WORLD");
		return $sql;
		
	}
	
	public static function findChar($acc, $personagem) {
		
		$sql = DB::Executa("SELECT TOP 1 char_id AS obj_Id, CASE WHEN (login > logout OR logout IS NULL) AND login IS NOT NULL THEN 1 ELSE 0 END AS online FROM user_data WHERE account_name = '".$acc."' AND char_id = '".$personagem."'", "WORLD");
		return $sql;
		
	}
	
	public static function insertDonation($acc, $personagem, $metodo_pgto, $qtdCoins, $qtdBonus, $valor, $price, $curr) {
		
		$sql = DB::Executa("INSERT INTO site_donations (account, personagem, price, currency, metodo_pgto, quant_coins, coins_bonus, valor, data) VALUES ('".$acc."', '".intval($personagem)."', '".$price."', '".$curr."', '".$metodo_pgto."', '".$qtdCoins."', '".$qtdBonus."', '".$valor."', '".time()."')", "SITE");
		return $sql;
		
	}
	
	public static function findDonation($acc, $protocolo='') {
		
		$sql = DB::Executa("SELECT * FROM site_donations WHERE account = '".$acc."' ".(!empty($protocolo) ? "AND protocolo = '".$protocolo."'" : "")." AND status <> '2' ORDER BY data DESC", "SITE");
		return $sql;
		
	}
	
	public static function listConverts($acc) {
		
		$sql = DB::Executa("SELECT * FROM site_log_convertcoins WHERE account = '".$acc."' ORDER BY cdata DESC", "SITE");
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
	
	public static function listTransfers($acc) {
		
		$sql = DB::Executa("SELECT * FROM site_log_transfercoins WHERE remetente = '".$acc."' ORDER BY tdata DESC", "SITE");
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
	
	public static function deleteDonation($acc, $protocolo) {
		
		$sql = DB::Executa("UPDATE site_donations SET status = '2' WHERE account = '".$acc."' AND protocolo = '".$protocolo."'", "SITE");
		return $sql;
		
	}
	
	public static function findReceptor($dest) {
		
		$sql = DB::Executa("SELECT TOP 1 account_name, CASE WHEN (login > logout OR logout IS NULL) AND login IS NOT NULL THEN 1 ELSE 0 END AS online, char_id AS obj_Id FROM user_data WHERE char_name = '".$dest."'", "WORLD");
		return $sql;
		
	}
	
	public static function insertBalance($dest, $count) {
		
		$checkExists = DB::Executa("SELECT TOP 1 * FROM site_balance WHERE account = '".$dest."'", "SITE");
		if(count($checkExists) > 0) {
			$sql = DB::Executa("UPDATE site_balance SET saldo = (saldo+".$count.") WHERE account = '".$dest."'", "SITE");
		} else {
			$sql = DB::Executa("INSERT INTO site_balance (account, saldo) VALUES ('".$dest."', '".$count."')", "SITE");
		}
		
		return $sql;
		
	}
	
	public static function transferLog($count, $acc, $receptor, $dest) {
		
		$sql = DB::Executa("INSERT INTO site_log_transfercoins VALUES ('".$count."', '".$acc."', '".$receptor."', '".$dest."', GETDATE())", "SITE");
		return $sql;
		
	}
	
	public static function convertLog($count, $acc, $receptor) {
		
		$sql = DB::Executa("INSERT INTO site_log_convertcoins VALUES ('".$count."', '".$acc."', '".$receptor."', GETDATE())", "SITE");
		return $sql;
		
	}
	
	public static function insertCoinInGame($cid, $coinID, $count) {
		
		$whatsTable = DB::Executa("SELECT TOP 1 * FROM SYSOBJECTS WHERE NAME = 'ItemDelivery' AND XTYPE = 'U'", "WORLD");
		if(count($whatsTable) > 0) {
			
			# Delivery 1 (ItemDelivery)
			$find = DB::Executa("SELECT TOP 1 * FROM ItemDelivery WHERE item_id = '".$coinID."' AND char_id = '".$cid."'", "WORLD");
			if(count($find) > 0) {
				$insert = DB::Executa("UPDATE ItemDelivery SET item_amount = (item_amount+".$count.") WHERE item_id = '".$coinID."' AND char_id = '".$cid."'", "WORLD");
			} else {
				$insert = DB::Executa("INSERT INTO ItemDelivery (char_id, item_id, item_amount, enchant) VALUES (".$cid.", ".$coinID.", ".$count.", 0)", "WORLD");
			}
			
		} else {
			
			# Delivery 2 (user_delivery)
			$findAccID = DB::Executa("SELECT TOP 1 uid FROM user_account WHERE account = '".$_SESSION['acc']."'", "DB");
			$findCharName = DB::Executa("SELECT TOP 1 char_name FROM user_data WHERE char_id = '".$cid."'", "WORLD");
			$insert = DB::Executa("INSERT INTO user_delivery (account_id, char_id, account_name, char_name, item_id, quantity, enchant, status) VALUES ('".$findAccID[0]['uid']."', ".$cid.", '".$_SESSION['acc']."', '".$findCharName[0]['char_name']."', ".$coinID.", ".$count.", 0, 0)", "WORLD");
			
		}
		
		return $insert;
		
	}
	
	public static function checkExistCount($itemID, $char) {
		
		$sql = DB::Executa("SELECT SUM(amount) AS count FROM user_item WHERE item_type = '".$itemID."' AND char_id = '".$char."'", "WORLD");
		return $sql;
		
	}
	
	public static function removeIngameCoins($coinID, $count, $char) {
		
		$countExist=0; $inINVE=0; $inWARE=0;
		$searchItemINVE = DB::Executa("SELECT TOP 1 amount, item_id FROM user_item WHERE char_id = '".$char."' AND item_type = '".$coinID."' AND warehouse = '0'", "WORLD");
		$searchItemWARE = DB::Executa("SELECT TOP 1 amount, item_id FROM user_item WHERE char_id = '".$char."' AND item_type = '".$coinID."' AND warehouse > '0'", "WORLD");
		if(count($searchItemINVE) > 0) { $countExist += intval($searchItemINVE[0]['amount']); $inINVE=intval($searchItemINVE[0]['amount']); }
		if(count($searchItemWARE) > 0) { $countExist += intval($searchItemWARE[0]['amount']); $inWARE=intval($searchItemWARE[0]['amount']); }
		if($countExist < $count) {
			return false;
		}
		
		if($inINVE > 0 && $inINVE <= $count) {
			if(!DB::Executa("DELETE FROM user_item WHERE item_id = '".$searchItemINVE[0]['item_id']."'", "WORLD")) {
				return false;
			}
		} else if($inINVE > $count) {
			if(!DB::Executa("UPDATE user_item SET amount = (amount-".$count.") WHERE item_id = '".$searchItemINVE[0]['item_id']."'", "WORLD")) {
				return false;
			}
		}
		
		if($count > $inINVE)  {
			$tirarDoWare = $count - $inINVE;
			if($tirarDoWare == $inWARE) {
				if(!DB::Executa("DELETE FROM user_item WHERE item_id = '".$searchItemWARE[0]['item_id']."'")) {
					return false;
				}
			} else {
				if(!DB::Executa("UPDATE user_item SET amount = (amount-".$tirarDoWare.") WHERE item_id = '".$searchItemWARE[0]['item_id']."'")) {
					return false;
				}
			}
		}
		
		return true;
		
	}
	
	public static function convertOnlineLog($count, $acc, $receptor) {
		
		$sql = DB::Executa("INSERT INTO site_log_convertcoins_online VALUES ('".$count."', '".$acc."', '".$receptor."', GETDATE())", "SITE");
		return $sql;
		
	}
	
	public static function listConvertsOnline($acc) {
		
		$sql = DB::Executa("SELECT * FROM site_log_convertcoins_online WHERE account = '".$acc."' ORDER BY cdata DESC", "SITE");
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
	
}

