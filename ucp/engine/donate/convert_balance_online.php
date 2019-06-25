<?php

if(!$indexing) { exit; }

if($logged != 1) { fim('Access denied!', 'RELOAD'); }

if($funct['trnsf3'] != 1) { fim($LANG[40003], 'ERROR', './'); }

fim('Temporarily disabled'); // Apos testar, remover isso

$count = !empty($_POST['count']) ? intval(trim($_POST['count'])) : 0;
$personagem = !empty($_POST['char']) ? intval(trim($_POST['char'])) : 0;
$captcha = !empty($_POST['captcha']) ? vCode($_POST['captcha']) : '';

if(empty($count) || empty($personagem)) {
	fim($LANG[12058]);
}

if($count < 0) {
	fim($LANG[12055].' #INVALIDNUMBER');
}

require_once('captcha/securimage.php');
$securimage = new Securimage();
if($securimage->check($captcha) == false) {
	fim($LANG[12057]);
}

require('private/classes/classDonate.php');

$findChar = Donate::findChar($_SESSION['acc'], $personagem);
if(count($findChar) == 0) { fim($LANG[10026], 'ERROR', './?module=donate&page=transfer'); }

if($findChar[0]['online'] == '1') { fim($LANG[10174].' '.$findChar[0]['char_name'].' '.$LANG[10175]); }

$checkExistCount = Donate::checkExistCount($coinID, $personagem);
if(intval(trim($checkExistCount[0]['count'])) < $count) {
	fim($LANG[10097]);
}

require('private/includes/cacheD.php');

if(!is_resource(l2_cached_open())) {
	fim($LANG[12055].' #CacheD');
}

l2_cached_close();

$countExist=0; $inINVE=0; $inWARE=0;
$searchItemINVE = DB::Executa("SELECT TOP 1 amount, item_id FROM user_item WHERE char_id = '".$char."' AND item_type = '".$coinID."' AND warehouse = '0'", "WORLD");
$searchItemWARE = DB::Executa("SELECT TOP 1 amount, item_id FROM user_item WHERE char_id = '".$char."' AND item_type = '".$coinID."' AND warehouse > '0'", "WORLD");
if(count($searchItemINVE) > 0) { $countExist += intval($searchItemINVE[0]['amount']); $inINVE=intval($searchItemINVE[0]['amount']); }
if(count($searchItemWARE) > 0) { $countExist += intval($searchItemWARE[0]['amount']); $inWARE=intval($searchItemWARE[0]['amount']); }
if($countExist < $count) {
	fim($LANG[10097].' #2');
}

if($inINVE > 0 && $inINVE <= $count) {
	
	$cached_op = pack("cV", 54, intval($searchItemINVE[0]['item_id']), 1);
	$result = l2_cached_push(pack("s", strlen($cached_op)+2).$cached_op.ansi2unicode('site-atualstudio'));
	if($result != '1') { fim($LANG[12055].' #CHANGECACHED1'); }

} else if($inINVE > $count) {
	
	$cached_op = pack("cVVVVVVVVVV", 14, intval($personagem), 0, intval($searchItemINVE[0]['item_id']), intval($coinID), intval($inINVE - $count), 0, 0, 0, 0, 0, 1);
	$result = l2_cached_push(pack("s", strlen($cached_op)+2).$cached_op.ansi2unicode('site-atualstudio'));
	if($result != '1') { fim($LANG[12055].' #CHANGECACHED2'); }

}

if($count > $inINVE)  {
	$tirarDoWare = $count - $inINVE;
	if($tirarDoWare == $inWARE) {
		
		$cached_op = pack("cV", 54, intval($searchItemWARE[0]['item_id']), 1);
		$result = l2_cached_push(pack("s", strlen($cached_op)+2).$cached_op.ansi2unicode('site-atualstudio'));
		if($result != '1') { fim($LANG[12055].' #CHANGECACHED3'); }
		
	} else {
		
		$cached_op = pack("cVVVVVVVVVV", 14, intval($personagem), 1, intval($searchItemWARE[0]['item_id']), intval($coinID), intval($inWARE - $tirarDoWare), 0, 0, 0, 0, 0, 1);
		$result = l2_cached_push(pack("s", strlen($cached_op)+2).$cached_op.ansi2unicode('site-atualstudio'));
		if($result != '1') { fim($LANG[12055].' #CHANGECACHED4'); }
		
	}
}

/*
$removeCoins = Donate::removeIngameCoins($coinID, $count, $personagem);
if(!$removeCoins) {
	fim($LANG[12055].' #REMOVE');
}
*/

$insertBalance = Donate::insertBalance($_SESSION['acc'], $count);
if(!$insertBalance) {
	fim($LANG[12055].' #INSERT');
} else {
	@Donate::convertOnlineLog($count, $_SESSION['acc'], $personagem);
	fim($LANG[12056], 'OK', './?module=donate&page=transfer');
}
