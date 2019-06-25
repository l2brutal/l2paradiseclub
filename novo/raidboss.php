<?php include "topo.php"; ?>

<div class="section internas">
	<div class="container">
		<div class="col-md-12 conteudoInterno sombra">
			<h3 class="titulosPrincipais">
                Raid Boss Status
              </h3>
              <table class="table table-hover">
                                <thead class="titulosTabela">
                                    <tr>
                                      <th>Nome do Raid Boss</th>
                                      <th>Status do Boss</th>
                                     
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        $boss = mssql_query("SELECT * FROM npc_boss WHERE npc_db_name NOT LIKE 'sentinel_guard_%'
        AND npc_db_name NOT LIKE '%_siege_%'
        AND npc_db_name NOT LIKE '%_ordery%'
        AND npc_db_name NOT LIKE 'RestlessAzit_%'
        AND npc_db_name NOT LIKE 'farmazit%'
        AND npc_db_name NOT LIKE 'br_xmas_invisible_npc'
        AND npc_db_name NOT LIKE '%_ordery%'
        AND npc_db_name NOT LIKE 'acmboss%'
        AND npc_db_name NOT LIKE '%b02_%'
        AND npc_db_name NOT LIKE 'tbb%'
        AND npc_db_name NOT LIKE 'tbf%'
        AND npc_db_name NOT LIKE 'nurka%'
        AND npc_db_name NOT LIKE 'devastated_%'
        AND npc_db_name NOT LIKE 'fortress_%'
        AND npc_db_name NOT LIKE '%_castle_%'
        AND npc_db_name NOT LIKE '%_dominion_%'
        ORDER BY time_low DESC");

                          while ($retorno = mssql_fetch_object($boss)){
                            echo "<tr>";

                            echo "<td>".ucwords(str_replace ("_", " ", $retorno->npc_db_name))."</td>";
                            if($retorno->alive == 0){
                              echo "<td><span class=vermelho>Morto</span></td>";
                              

                            }
                            else echo "<td><span class=verde>Vivo</span></td><td></td>";


                            echo "</tr>";
                          }

                        ?>
                               </tbody>
                            </table>

		</div>
	</div>
</div>

<?php include "rodape.php"; ?>
