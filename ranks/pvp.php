<?php include "../topo.php"; ?>
<?php include '../seo.php'; ?>
<?php include '../conexao.php'; ?>

</section>

<section class="background"></section>

    <?php include 'status.php'; ?>

    </div>

        <div class="separator-sid"></div>
            <a href="../ranks/pvp.php" class="pvp"></a>
            <a href="../ranks/pk.php" class="pk"></a>
            <a href="../ranks/clan.php" class="clan"></a>
            <a href="../ranks/only.php" class="oly"></a>
            <a href="../ranks/boss.php" class="bosses"></a>
        <div class="separator-sid"></div>

        <a href="register.php" class="reg"></a>
        <a href="downloads.php" class="downloads"></a>
        <a href="#" class="community"></a>

        <div class="separator-sid"></div>

<a href="rankings.php">
        <div class="box-sid hovered">
            <div class="align">
                <div class="title">BAN LIST</div>
                <div class="subtitle">SEE THE FIRST TO DEFEAT GREAT MONSTERS</div>
            </div>
        </div>
</a>

        <div class="box-sid hovered">
            <div class="align">
                <div class="title">STORE ACOINS</div>
                <div class="subtitle" style="width:250px;">Buy in our store and contribute the server</div>
            </div>
        </div>
    </div>

    <div class="cont">

        <!-- Content -->
<div style="display: <?php echo $ranks_warning ?> ;" class="separator-content"></div>
<div style="display: <?php echo $ranks_warning ?> ;" class="warning">The rankings will be released at the  <b>OBT server end</b>! Wait!</div>

<div class="title-page">RANKING PVP</div>
<div class="subtitle-page">The players that kill the most on our server</div>

<div class="separator-content"></div>


<div style="display: <?php echo $enable_rank ?> ;" class="rankings">
<table width="100%" cellspacing="0" cellpadding="10">
    <tr>
        <td>#</td>
        <td>Char Name</td>
        <td>Title</td>
        <td>Level</td>
        <td>Pvpkills</td>
    </tr>
        <?php

                      $pvps = mssql_query("SELECT TOP 30 * FROM user_data order by Duel DESC");

                      $cont1 = 0;
                      while ($retorno = mssql_fetch_object($pvps)){
                          $cont1++;
                          echo "<th scope=row>$cont1</th>";
                          echo "<td>".$retorno->char_name."</td>";
                          echo "<td>".$retorno->nickname."</td>";
                          echo "<td>".$retorno->Lev."</td>";
                          echo "<td>".$retorno->Duel."</td>";
                          echo "</tr>";
                      }



                      ?>
</table>

</div>


<div class="separator-content"></div>


<script>
    fbq('track', 'ViewContent');
</script>        <!-- Content -->




    </div>
</section>
							      	  <div style="display:none !important"><a href="https://info.flagcounter.com/IFpg"><img src="https://s01.flagcounter.com/count2/IFpg/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_10/viewers_0/labels_0/pageviews_0/flags_0/percent_0/" alt="Flag Counter" border="0"></a>			</div>
<section class="footer">
<div class="container">
    <div class="text-credits">ALL RIGHTS RESERVED LINEAGE II LORDS</div>
</div>
</section>
</body>
</html>