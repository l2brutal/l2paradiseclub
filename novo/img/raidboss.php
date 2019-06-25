<?php include "conexao.php"; ?>
<?php 
ini_set( 'display_errors', true );
error_reporting( E_ALL ); ?>
<?php include "topo.php"; 


$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlcore = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29006');
$sqlorfen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');
$sqlqueen = $conn->query('SELECT * FROM grandboss_data WHERE boss_id = 29001');

?>

<div class="section internas">
	<div class="container">
		<div class="col-md-12 conteudoInterno sombra">
			<h3 class="titulosPrincipais">
                Raid Bosses!
              </h3>
              <?php
              $queen = $sqlqueen->fetchAll(PDO::FETCH_OBJ);
              $core = $sqlcore->fetchAll(PDO::FETCH_OBJ);
              
							foreach($queen as $k) {
								if($k->status > 0) {
									echo "<font color='F00000'>Morto</font>";
								}else {
									echo "<font color='00FF00'>Vivo</font>";
								}
							}

               ?> 


              <table width="100%" border="0" id="table_ranking" class="table table-hover text-center">
   <thead>
  <tr>
    <th width="40%" align="center" nowrap="nowrap" style="text-align: center;">Nome</th>
    <th width="20%" align="center" nowrap="nowrap" style="text-align: center;">Level</th>
    <th width="20%" align="center" nowrap="nowrap" style="text-align: center;">Status</th>
    <th width="20%" align="center" nowrap="nowrap" style="text-align: center;">Respawn</th>
  </tr>
  </thead>
  <tr>
  <?php
	foreach($queen as $k) {
  ?>

    <td width="40%" align="center" style="padding:7px;">Queen Ant<br />
      <img src="img/queen_ant.jpg" width="130" height="100" class="img_grand_boss" /></td>
    <td width="20%" valign="middle" align="center">40</td>
    <td width="20%" align="center">
    <?php
		if($k->status > 0) {
			echo "<font color='F00000'>Morto</font>";
		}else {
			echo "<font color='00FF00'>Vivo</font>";
		}
	
  ?>
    </td>
    <td  width="20%" align="center">
    <?php
  if($k->status > 0) {
    $respawntime = date('d/m/Y H:i:s',($k->respawn_time / 1000));

                echo '<font color="eeeeee">'.$respawntime.'</font>';
  }else {
    echo "<font color='00FF00'>Raid Boss Vivo</font>";
  }
}
  ?>
    </td>
  </tr>
  <!-- core -->
  <tr>
  <?php
  foreach($core as $c) {
  ?>

    <td width="40%" align="center" style="padding:7px;">Core<br />
      <img src="img/core.jpg" width="130" height="100" class="img_grand_boss" /></td>
    <td width="20%" valign="middle" align="center">50</td>
    <td width="20%" align="center">
    <?php
    if($c->status > 0) {
      echo "<font color='F00000'>Morto</font>";
    }else {
      echo "<font color='00FF00'>Vivo</font>";
    }
  
  ?>
    </td>
    <td  width="20%" align="center">
    <?php
  if($c->status > 0) {
    $respawntime = date('d/m/Y H:i:s',($c->respawn_time / 1000));

                echo '<font color="eeeeee">'.$respawntime.'</font>';
  }else {
    echo "<font color='00FF00'>Raid Boss Vivo</font>";
  }
}
  ?>
    </td>
  </tr>

</table>

		</div>
	</div>
</div>
asdijaosidj
<?php include "rodape.php"; ?>
