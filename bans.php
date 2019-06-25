<?php include "topo.php"; ?>
<?php include 'seo.php'; ?>
<?php include 'conexao.php'; ?>

</section>

<section class="background"></section>

    <?php include 'status.php'; ?>

    </div>

        <div class="separator-sid"></div>
            <a href="pvp.php" class="pvp"></a>
            <a href="pk.php" class="pk"></a>
            <a href="clan.php" class="clan"></a>
            <a href="only.php" class="oly"></a>
            <a href="raid.php" class="bosses"></a>
        <div class="separator-sid"></div>

        <a href="<?php echo $register_now ?>" class="reg"></a>
        <a href="downloads.php" class="downloads"></a>
        <a href="<?php echo $forum ?>" class="community"></a>

        <div class="separator-sid"></div>

<a style="display: <?php echo $enable_rank ?> ;" href="bans.php">
        <div class="box-sid hovered">
            <div class="align">
                <div class="title">BAN LIST</div>
                <div class="subtitle">SEE THE SERVER BAN LIST</div>
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


<div class="title-page">BANLIST</div>
<div class="subtitle-page" style="text-align: center;">See the lastest ban on our server. <br> This page can have delay 72 hours to updated</div>


<div style="display: <?php echo $ranks_warning ?> ;" class="separator-content"></div>
<div style="display: <?php echo $ranks_warning ?> ;" class="warning">The rankings will be released at the  <b>OBT server end</b>! Wait!</div>


<div class="separator-content"></div>


<div style="display: <?php echo $enable_ranking ?> ;" class="rankings">
<table width="100%" cellspacing="0" cellpadding="10">
    <tr>
        <td>#</td>
        <td>Account</td>
        <td>Type</td>
        <td>Description</td>
    </tr>
        <?php

                      $bans = mssql_query("SELECT * FROM smartguard");

                      $cont1 = 0;
                      while ($retorno = mssql_fetch_object($bans)){
                          $cont1++;
                          echo "<th scope=row>$cont1</th>";
                          echo "<td>".$retorno->account."</td>";
                          echo "<td>".$retorno->type."</td>";
                          echo "<td>".$retorno->description."</td>";
                          echo "</tr>";
                      }



                      ?>
</table>

</div>




<script>
    fbq('track', 'ViewContent');
</script>        <!-- Content -->




    </div>
</section>
							      	  <div style="display:none !important"><a href="https://info.flagcounter.com/IFpg"><img src="https://s01.flagcounter.com/count2/IFpg/bg_FFFFFF/txt_000000/border_CCCCCC/columns_2/maxflags_10/viewers_0/labels_0/pageviews_0/flags_0/percent_0/" alt="Flag Counter" border="0"></a>			</div>
<section class="footer">
<div class="container">
    <div class="text-credits"><?php echo $footer ?></div>
</div>
</section>
</body>
</html>