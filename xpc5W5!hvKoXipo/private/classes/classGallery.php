<?php

class Gallery {

	public static function listGallery() {
		
		$sql = "SELECT * FROM site_gallery ORDER BY pos ASC";
		return DB::Executa($sql, "SITE");
		
	}

	public static function reorderAllGallery() {
		
		$sql = "UPDATE site_gallery SET pos = (pos + 1)";
		return DB::Executa($sql, "SITE");
		
	}

	public static function insertGallery($url, $pos, $isvideo, $vis) {
		
		$sql = "INSERT INTO site_gallery (url, pos, isvideo, vis) VALUES ('".$url."', '".$pos."', '".$isvideo."', '".$vis."')";
		return DB::Executa($sql, "SITE");
		
	}

	public static function editGallery($gid, $vis) {
		
		$sql = "UPDATE site_gallery SET vis = '".$vis."' WHERE gid = '".$gid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function findGallery($gid) {
		
		$sql = "SELECT TOP 1 * FROM site_gallery WHERE gid = '".$gid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function deleteGallery($gid) {
		
		$sql = "DELETE FROM site_gallery WHERE gid = '".$gid."'";
		return DB::Executa($sql, "SITE");
		
	}

	public static function reorder($pos, $itemID) {
		
		$sql = "UPDATE site_gallery SET pos = '".$pos."' WHERE gid = '".$itemID."'";
		return DB::Executa($sql, "SITE");
		
	}

}
