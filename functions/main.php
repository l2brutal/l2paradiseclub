<?php 

class Index {

	public static function countAccount()
	{
		$sql = "SELECT COUNT(*) AS quant FROM user_auth";
		return Index::Executa($sql, "DB");
	}

	public static function countChars() {
		$sql = "SELECT COUNT(*) AS quant FROM user_data";
		return Index::Executa($sql, "WORLD");
	}

	public static function countOnline() {
		$sql = "SELECT COUNT(*) AS quant FROM user_data WHERE (login > logout OR logout IS NULL) AND login IS NOT NULL";
		return Index::Executa($sql, "WORLD");
	}

	public static function countClans() {
		$sql = "SELECT COUNT(*) AS quant FROM Pledge";
		return Index::Executa($sql, "WORLD");
	}

}

  $countOnline = Index::countOnline(); $countOnline = intval($countOnline[0]['quant']);


 ?>