<?php

include "conexao.php";

$gludio = mssql_query("
SELECT
  CAS.id,
  CAS.name,
  CAS.next_war_time AS sdate,
  CAS.tax_rate AS stax,
  P.char_name,
  C.name AS clan_name,
  A.name AS ally_name,
  CAS.id
FROM
  castle AS CAS

LEFT JOIN
  Pledge AS C ON C.pledge_id = CAS.pledge_id
LEFT JOIN
  Alliance AS A ON A.id = C.alliance_id
LEFT JOIN
  user_data AS P ON P.char_id = C.ruler_id"
);

while($retorno = mssql_fetch_object($gludio)){
  var_dump($retorno)."<br>";
}

?>
