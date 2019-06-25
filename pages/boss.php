<!-- Global site tag (gtag.js) - Google Analytics --><script async src="https://www.googletagmanager.com/gtag/js?id=UA-119574268-1"></script><script>  window.dataLayer = window.dataLayer || [];  function gtag(){dataLayer.push(arguments);}  gtag('js', new Date());  gtag('config', 'UA-119574268-1');</script><?php if(!$indexing || $dpage['bosstt'] != 1) { echo "<script>document.location.replace('./');</script>"; exit; } ?>

<h1>Boss Status</h1>

<?php

require('private/includes/rankoptions.php');

?>

<hr />

<?php

if(substr($gmt, 0, 1) == '-') { $gmtn = substr($gmt, 1); } else { $gmtn = "-".$gmt; } $gmtf = $gmtn*3600;

$cacheFile = "cache/boss.xml";
$genNew = 0;

if(!file_exists($cacheFile)) {
	$genNew = 1;
} else {
	
	$xml = simplexml_load_file($cacheFile);
	$configs = $xml->configs;
	$updated = intval($configs->updated);
	$delay = 1;
	
	if(($updated+($delay*60)) < time()) {
		$genNew = 1;
	}
	
}

if($genNew) {
	
	if(!file_exists("cache")) { @mkdir("cache", 0775, true); @chmod("cache", 0775); }
	if(!file_exists("cache/index.html")) { $secIndexFile = fopen("cache/index.html","w+"); @fclose($secIndexFile); }
	if(!file_exists("cache/.htaccess")) { $secHtacsFile = fopen("cache/.htaccess","w+"); @fwrite($secHtacsFile, "Options -Indexes"); @fclose($secHtacsFile); }
	
	$wFile = fopen($cacheFile,"w+");
	
	$updated = time();
	$line = "\n<configs>\n<atualstudio>Cache script by Atualstudio.com</atualstudio>\n<updated>".$updated."</updated>\n</configs>";
	
	if(!class_exists('Stats')) { require_once('private/classes/classStats.php'); }

	$query = Stats::RaidbossStatus();
	if(count($query) > 0) {
		
		for($i=0, $c=count($query); $i < $c; $i++) {
			
			if($query[$i]['time_low'] > 0) {
				$respawn = date('d/m/Y H:i',($query[$i]['time_low'])-$gmtf);
				$status = $LANG[12029];
			} else {
				$status = $LANG[12030];
				$respawn = '-';
			}
			
			$line .= "\n<line>\n";
			$line .= "<name>".ucwords(str_replace('_', ' ', $query[$i]['npc_db_name']))."</name>\n";
			$line .= "<status>".$status."</status>\n";
			$line .= "<respawn>".$respawn."</respawn>\n";
			$line .= "</line>";
			
		}
		
	} else {
		$deleteCache = 1;
	}
	
	@fwrite($wFile, utf8_encode("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<boss>".$line."\n</boss>"));
	@fclose($wFile);
	
	$xml = simplexml_load_file($cacheFile);
	
	if(isset($deleteCache) && file_exists($cacheFile)) {
		unlink($cacheFile);
	}
	
}

?>

<h2>Raid Bosses</h2>

<table cellspacing='0' cellpadding='0' border='0' class='default'>
	
	<tr>
		<th><?php echo $LANG[12013]; ?></th>
		<th>Status</th>
		<th>Defeat date</th>
	</tr>

	<?php
	
	$line = $xml->line;
	
	for($i=0, $c=count($line); $i < $c; $i++) {
		
		echo "
		<tr".(($i % 2 == 0) ? " class='two'" : "").">
			<td>".$line[$i]->name."</td>
			<td style='font-weight:bold;'>".strtr($line[$i]->status, array("".$LANG[12030]."" => "<font color='#0d8d00'>".$LANG[12030]."</font>", "".$LANG[12029]."" => "<font color='red'>".$LANG[12029]."</font>"))."</td>
			<td>".$line[$i]->respawn."</td>
		</tr>
		";
		
	}
	
	?>

</table>