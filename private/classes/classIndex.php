<?php

class Index {

	public static function News($pgBeg=0, $pgMax=3) {
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY post_date DESC) AS row, t1.* FROM site_news AS t1 WHERE t1.vis = '1') AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		return $sql;
		
	}
	
	public static function CountNews() {
		
		$sql = DB::Executa("SELECT COUNT(*) AS quant FROM site_news WHERE vis = '1'", "SITE");
		return $sql;
		
	}
	
	public static function NewsExcept($limit=3, $newID) {
		
		$sql = DB::Executa("SELECT TOP ".$limit." * FROM site_news WHERE vis = '1' AND nid <> '".$newID."' ORDER BY post_date DESC", "SITE");
		return $sql;
		
	}
	
	public static function ViewNew($newID) {
		
		$sql = DB::Executa("SELECT TOP 1 * FROM site_news WHERE vis = '1' AND nid = '".$newID."'", "SITE");
		return $sql;
		
	}
	
	public static function Banners() {
		
		$sql = DB::Executa("SELECT * FROM site_banners WHERE vis = '1' ORDER BY pos ASC", "SITE");
		return $sql;
		
	}
	
}
