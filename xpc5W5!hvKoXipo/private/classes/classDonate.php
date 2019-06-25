<?php

class Donate {

	public static function countDonations( $buscar='', $status='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			$whereAdd .= "AND (metodo_pgto LIKE '%".$buscar."%' OR account LIKE '%".$buscar."%' ";
			if(intval($buscar) > 0) {
				$whereAdd .= "
				OR protocolo LIKE '%".intval($buscar)."%' 
				OR (quant_coins + coins_bonus) = '".intval($buscar)."' 
				OR quant_coins = '".intval($buscar)."' 
				OR valor = '".save_preco($buscar)."'
				";
			}
			$whereAdd .= ")";
		}

		if(!empty($status)) {
			$whereAdd .= " AND status = '".$status."'";
		}
		
		$sql = "SELECT COUNT(*) AS quant FROM site_donations WHERE status <> '2' ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listDonations($pgBeg, $pgMax, $buscar='', $status='') {
		
		$whereAdd = "";

		if(!empty($buscar)) {
			$whereAdd .= "AND (metodo_pgto LIKE '%".$buscar."%' OR account LIKE '%".$buscar."%' ";
			if(intval($buscar) > 0) {
				$whereAdd .= "
				OR protocolo LIKE '%".intval($buscar)."%' 
				OR (quant_coins + coins_bonus) = '".intval($buscar)."' 
				OR quant_coins = '".intval($buscar)."' 
				OR valor = '".save_preco($buscar)."'
				";
			}
			$whereAdd .= ")";
		}

		if(!empty($status)) {
			$whereAdd .= " AND status = '".$status."'";
		}
		
		$sql = "SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY data DESC) AS row, t1.* FROM site_donations AS t1 WHERE status <> '2' ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."";
		return DB::Executa($sql, "SITE");
		
	}

	public static function findConcluidas() {
		
		$sql = "SELECT COUNT(*) AS quant FROM site_donations WHERE status = '3' OR status = '4'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function searchValTotal() {
		
		$sql = "SELECT SUM(valor) AS val, currency FROM site_donations WHERE status = '3' OR status = '4' GROUP BY currency";
		return DB::Executa($sql, "SITE");
		
	}

	public static function countPending($buscar='') {
		
		$whereAdd = "";
		
		if(!empty($buscar)) {
			$whereAdd .= "AND (metodo_pgto LIKE '%".$buscar."%' OR account LIKE '%".$buscar."%' ";
			if(intval($buscar) > 0) {
				$whereAdd .= "
				OR protocolo LIKE '%".intval($buscar)."%' 
				OR (quant_coins + coins_bonus) = '".intval($buscar)."' 
				OR quant_coins = '".intval($buscar)."' 
				OR valor = '".save_preco($buscar)."'
				";
			}
			$whereAdd .= ")";
		}

		$sql = "SELECT COUNT(*) AS quant FROM site_donations WHERE (status = '1' OR status = '3') ".$whereAdd;
		return DB::Executa($sql, "SITE");
		
	}

	public static function listPending($pgBeg, $pgMax, $buscar='') {
		
		$whereAdd = "";

		if(!empty($buscar)) {
			$whereAdd .= "AND (metodo_pgto LIKE '%".$buscar."%' OR account LIKE '%".$buscar."%' ";
			if(intval($buscar) > 0) {
				$whereAdd .= "
				OR protocolo LIKE '%".intval($buscar)."%' 
				OR (quant_coins + coins_bonus) = '".intval($buscar)."' 
				OR quant_coins = '".intval($buscar)."' 
				OR valor = '".save_preco($buscar)."'
				";
			}
			$whereAdd .= ")";
		}

		$sql = "SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY status DESC, data DESC) AS row, t1.* FROM site_donations AS t1 WHERE (status = '1' OR status = '3') ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."";
		return DB::Executa($sql, "SITE");
		
	}

	public static function findDonation($protocolo) {
		
		$sql = "SELECT TOP 1 * FROM site_donations WHERE status <> '2' AND protocolo = '".$protocolo."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function paidDonation($protocolo, $data, $coinsEntregues) {
		
		$sql = "UPDATE site_donations SET status = '4', coins_entregues = '".$coinsEntregues."', ultima_alteracao = '".$data."' WHERE protocolo = '".$protocolo."' AND status <> '2'";
		return DB::Executa($sql, "SITE");
		
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

	public static function deleteDonation($protocolo) {
		
		$sql = "UPDATE site_donations SET status = '2' WHERE protocolo = '".$protocolo."' AND status <> '2'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function contBalances($buscar='') {
		$whereAdd = "";

		if(!empty($buscar)) {
			$whereAdd .= " WHERE (account LIKE '%".$buscar."%' OR saldo = '".$buscar."')";
		}

		$sql = "SELECT COUNT(*) AS quant FROM site_balance ".$whereAdd."";
		return DB::Executa($sql, "SITE");
	}

	public static function listBalances($pgBeg, $pgMax, $buscar='') {
		$whereAdd = "";

		if(!empty($buscar)) {
			
			$whereAdd .= " WHERE (t1.account LIKE '%".$buscar."%' ";
			
			if(is_numeric($buscar)){
				$whereAdd .= "OR t1.saldo = '".$buscar."'";
			}
			
			$whereAdd .= ")";
			
		}

		$sql = "SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY account ASC) AS row, t1.* FROM site_balance AS t1 ".$whereAdd.") AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."";
		return DB::Executa($sql, "SITE");
	}

	public static function checkBalance($account) {
		$sql = "SELECT TOP 1 * FROM site_balance WHERE account = '".$account."'";
		return DB::Executa($sql, "SITE");
	}

	public static function updateBalance($account, $saldo) {
		$sql = "UPDATE site_balance SET saldo = '".$saldo."' WHERE account = '".$account."'";
		return DB::Executa($sql, "SITE");
	}

	public static function addBalance($account, $saldo) {
		$sql = "INSERT INTO site_balance (account, saldo) VALUES ('".$account."', '".$saldo."')";
		return DB::Executa($sql, "SITE");
	}

	public static function findAccount($account) {
		$sql = "SELECT TOP 1 * FROM user_account WHERE account = '".$account."'";
		return DB::Executa($sql, "DB");
	}

}
