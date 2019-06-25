<?php

class Action {

	public static function Unstuck($cid, $x, $y, $z) {
		
		$sql = DB::Executa("UPDATE user_data SET xloc = '".$x."', yloc = '".$y."', zloc = '".$z."' WHERE char_id = '".$cid."' AND align = '0'", "WORLD");
		return $sql;
		
	}
	
}
