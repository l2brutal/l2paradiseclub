<?php

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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'gludio_castle'"
);
$aden = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'aden_castle'"
);

$goddard = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'godad_castle'"
);
$schuttgart = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'schuttgart_castle'"
);
$innadril = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'innadrile_castle'"
);
$giran = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'giran_castle'"
);
$oren = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'oren_castle'"
);
$rune = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'rune_castle'"
);
$dion = mssql_query("
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
  user_data AS P ON P.char_id = C.ruler_id
WHERE CAS.name = 'dion_castle'"
);
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
 ?>
