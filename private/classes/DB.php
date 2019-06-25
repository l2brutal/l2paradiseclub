<?php

class DB {

	private static $con;

	private static $dbnm;

	private static $conMethod;

	public static $lastInsertID;

	function __construct($conMethod, $host='', $user='', $pass='', $dbnm='', $port='') {

		if(is_array($host) && is_array($port) && is_array($dbnm) && is_array($user) && is_array($pass)) {

			DB::$conMethod = $conMethod;

			foreach($host as $key => $val) {

				if(!empty($host[$key]) && !empty($port[$key]) && !empty($dbnm[$key]) && !empty($user[$key])) {

					if($conMethod == 1) {

						# MSSQL

						DB::$con[$key] = @mssql_connect($host[$key], $user[$key], $pass[$key]) or die("Failed to connect! #MSSQL #".$key);
						DB::$dbnm[$key] = $dbnm[$key];

					} else if($conMethod == 2) {

						# SQLSRV

						DB::$con[$key] = sqlsrv_connect($host[$key], array("Database" => $dbnm[$key], "UID" => $user[$key], "PWD" => $pass[$key]));
						if(!DB::$con[$key]) {
							echo "Failed to connect! #SQLSRV #".$key;
							exit;
						}

					} else if($conMethod == 3) {

						# ODBC

						DB::$con[$key] = odbc_connect("DRIVER={SQL Server};SERVER=".$host[$key].";Port=".$port[$key].";DATABASE=".$dbnm[$key]."", $user[$key], $pass[$key]);
						if(!DB::$con[$key]) {
							echo "Failed to connect! #ODBC #".$key;
							exit;
						}

					} else if($conMethod == 4) {

						# PDO - ODBC

						try {
							DB::$con[$key] = new PDO("odbc:DRIVER={SQL Server};SERVER=".$host[$key].";Port=".$port[$key].";DATABASE=".$dbnm[$key]."");
						} catch (PDOException $e) {
							echo "Failed to connect! #PDO-ODBC #".$key;
							exit;
						}

					} else {
						return false;
					}

				} else {
					continue;
				}

			}

		} else {
			return false;
		}

	}

	public static function Executa($sql, $key='WORLD') {

		$key = strtoupper($key);

		if(!empty(DB::$con[$key]) && !empty($sql)) {

			$sql_ini = strtoupper(substr(trim($sql), 0, 6));

			if(DB::$conMethod == 1) {

				# MSSQL

				if(!empty(DB::$dbnm[$key])) {
					@mssql_select_db("".DB::$dbnm[$key]."", DB::$con[$key]) or die("Failed connection to database! #MSSQL #".$key);
				}

				$query = mssql_query($sql, DB::$con[$key]);

				if(empty($query)) {
					return false;
				}

				if($sql_ini == 'SELECT') {

					$array = array();
					while ($result = mssql_fetch_array($query)) {
						$array[] = $result;
					}

					return $array;

				} else if($sql_ini == 'INSERT') {

					$query = mssql_query("SELECT @@IDENTITY AS lastInsertID", DB::$con[$key]);
					$result = mssql_fetch_array($query);

					DB::$lastInsertID = $result['lastInsertID'];

					return true;

				} else {

					return true;

				}

			} else if(DB::$conMethod == 2) {

				# SQLSRV

				$query = sqlsrv_query(DB::$con[$key], $sql);

				if(empty($query)) {
					return false;
				}

				if($sql_ini == 'SELECT') {

					$array = array();
					while ($result = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
						$array[] = $result;
					}

					return $array;

				} else if($sql_ini == 'INSERT') {

					$query = sqlsrv_query(DB::$con[$key], "SELECT @@IDENTITY AS lastInsertID");
					$result = sqlsrv_fetch_array($query);

					DB::$lastInsertID = $result['lastInsertID'];

					return true;

				} else {

					return true;

				}

			} else if(DB::$conMethod == 3) {

				# ODBC

				$query = odbc_exec(DB::$con[$key], $sql);

				if(empty($query)) {
					return false;
				}

				if($sql_ini == 'SELECT') {

					$array = array();
					while ($result = odbc_fetch_array($query)) {
						$array[] = $result;
					}

					return $array;

				} else if($sql_ini == 'INSERT') {

					$query = odbc_exec(DB::$con[$key], "SELECT @@IDENTITY AS lastInsertID");
					$result = odbc_fetch_array($query);

					DB::$lastInsertID = $result['lastInsertID'];

					return true;

				} else {

					return true;

				}

			} else if(DB::$conMethod == 4) {

				# PDO-ODBC

				$query = DB::$con[$key]->prepare($sql);
				$query->execute();

				if(empty($query) || $query->errorCode() != 0) {
					return false;
				}

				if($sql_ini == 'SELECT') {

					$array = array();
					while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
						$array[] = $result;
					}

					return $array;

				} else if($sql_ini == 'INSERT') {

					DB::$lastInsertID = @DB::$con[$key]->lastInsertId();

					if(empty(DB::$lastInsertID)) {

						$query = DB::$con[$key]->prepare("SELECT @@IDENTITY AS lastInsertID");
						$query->execute();
						$result = $query->fetch(PDO::FETCH_ASSOC);

						DB::$lastInsertID = $result['lastInsertID'];

					}

					return true;

				} else {

					return true;

				}

			}

		}

		return false;
	}

	public static function close() {
		foreach(DB::$con as $key => $val) {
			if(DB::$conMethod == 1) {
				mssql_close(DB::$con[$key]);
			} else if(DB::$conMethod == 2) {
				sqlsrv_close(DB::$con[$key]);
			} else if(DB::$conMethod == 3) {
				odbc_close(DB::$con[$key]);
			}
			unset(DB::$con[$key]);
		}
		DB::$con = '';
	}

}
