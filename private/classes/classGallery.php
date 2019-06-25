<?php

class Gallery {

	public static function listAll() {
		
		$sql = DB::Executa("SELECT * FROM site_gallery WHERE vis = '1' ORDER BY pos ASC", "SITE");
		return $sql;
		
	}
	
	public static function Show($pgBeg=3, $pgMax='') {
		
		$sql = DB::Executa("SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY pos ASC) AS row, t1.* FROM site_gallery AS t1 WHERE t1.vis = '1') AS t2 WHERE t2.row BETWEEN ".($pgBeg+1)." AND ".($pgBeg+$pgMax)."", "SITE");
		return $sql;
		
	}
	
	public static function CountItens() {
		
		$sql = DB::Executa("SELECT COUNT(*) AS quant FROM site_gallery WHERE vis = '1'", "SITE");
		return $sql;
		
	}
	
}
