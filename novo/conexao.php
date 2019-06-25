<?php
	$myServer = "149.56.79.107";
	$myUser = "sitedb_connect";
	$myPass = "rdbconnect2020!#@$";
	$myDB = "lin2world";

	$dbhandle = mssql_connect($myServer, $myUser, $myPass)
	  or die("Couldn't connect to SQL Server on $myServer"); 

	mssql_select_db($myDB);
	

?>
