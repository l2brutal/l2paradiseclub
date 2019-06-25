<?php

class Index {

	public static function countNews() {
		
		$sql = "SELECT COUNT(*) AS quant FROM site_news";
		return DB::Executa($sql, "SITE");
		
	}

	public static function countBanners() {
		
		$sql = "SELECT COUNT(*) AS quant FROM site_banners";
		return DB::Executa($sql, "SITE");
		
	}

	public static function countGallery() {
		
		$sql = "SELECT COUNT(*) AS quant FROM site_gallery";
		return DB::Executa($sql, "SITE");
		
	}

	public static function countAccounts() {
		
		$sql = "SELECT COUNT(*) AS quant FROM user_auth";
		return DB::Executa($sql, "DB");
		
	}

	public static function countChars() {
		
		$sql = "SELECT COUNT(*) AS quant FROM user_data";
		return DB::Executa($sql, "WORLD");
		
	}

	public static function countOnline() {
		
		$sql = "SELECT COUNT(*) AS quant FROM user_data WHERE (login > logout OR logout IS NULL) AND login IS NOT NULL";
		return DB::Executa($sql, "WORLD");
		
	}

	public static function countClans() {
		
		$sql = "SELECT COUNT(*) AS quant FROM Pledge";
		return DB::Executa($sql, "WORLD");
		
	}

	public static function donates($perBegin, $perEnd) {
		
		$sql = "SELECT * FROM site_donations WHERE data > '".$perBegin."' AND data < '".$perEnd."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function beginDonateYear() {
		
		$sql = "SELECT TOP 1 data FROM site_donations ORDER BY data ASC";
		return DB::Executa($sql, "SITE");
		
	}

}
