<?php

class Banners {

	public static function listBanners() {
		
		$sql = "SELECT * FROM site_banners ORDER BY pos ASC";
		return DB::Executa($sql, "SITE");
		
	}

	public static function findLastBannerPos() {
		
		$sql = "SELECT TOP 1 pos FROM site_banners ORDER BY pos DESC";
		return DB::Executa($sql, "SITE");
		
	}

	public static function insertBanner($imgurl_pt, $imgurl_en, $imgurl_es, $link, $target, $vis, $pos) {
		
		$sql = "INSERT INTO site_banners (imgurl_pt, imgurl_en, imgurl_es, pos, link, target, vis) VALUES ('".$imgurl_pt."', '".$imgurl_en."', '".$imgurl_es."', '".$pos."', '".$link."', '".$target."', '".$vis."')";
		return DB::Executa($sql, "SITE");
		
	}

	public static function editBanner($bid, $imgurl_pt, $imgurl_en, $imgurl_es, $link, $target, $vis) {
		
		$sql = "UPDATE site_banners SET ".((!empty($imgurl_pt)) ? "imgurl_pt = '".$imgurl_pt."'," : "")." ".((!empty($imgurl_en)) ? "imgurl_en = '".$imgurl_en."'," : "")." ".((!empty($imgurl_es)) ? "imgurl_es = '".$imgurl_es."'," : "")." link = '".$link."', target = '".$target."', vis = '".$vis."' WHERE bid = '".$bid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function findBanner($bid) {
		
		$sql = "SELECT TOP 1 * FROM site_banners WHERE bid = '".$bid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function deleteBanner($bid) {
		
		$sql = "DELETE FROM site_banners WHERE bid = '".$bid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function reorder($pos, $itemID) {
		
		$sql = "UPDATE site_banners SET pos = '".$pos."' WHERE bid = '".$itemID."'";
		return DB::Executa($sql, "SITE");
		
	}

}
