<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v3.1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php include "topo.php"; ?>

    <div class="section conteudo">
      <div class="container">

          <div class="col-md-3">
            <div class="row downloads">
              <h3 class="titulosPrincipais">
                Downloads
              </h3>
              <a href="" class="botaoDownload bt1"><img src="img/download.png"> Cliente <span>High Five</span></a>
              <a href="" class="botaoDownload bt2"><img src="img/download.png"> Patch <span>Ultima L2</span></a>
              <a href="" class="botaoDownload bt3"><img src="img/download.png"> Updater <span>Ultima L2</span></a>
            </div>
            <div class="row serverInfos">
              <h3 class="titulosPrincipais">
                Server Infos
              </h3>

                <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#rates" aria-controls="rates" role="tab" data-toggle="tab">Rates</a></li>
                  <li role="presentation"><a href="#hardware" aria-controls="hardware" role="tab" data-toggle="tab">Hardware</a></li>

                </ul>


                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active sombra" id="rates">
                    <ul class="list-unstyled">
                      <li>XP/SP: <span class="dourado">7x</span></li>
                      <li>Adena: <span class="dourado">3x</span></li>
                      <li>Itens: <span class="dourado">3x / 1x (qtde)</span></li>
                      <li>Spoil: <span class="dourado">7x / 3x (qtde)</span></li>
                      <li>Recipe: <span class="dourado">5% (todos) / 1x (qtde)</span></li>
                      <li>Raid Boss: <span class="dourado">7x / 1x (qtde)</span></li>
                      <li>Epic Boss: <span class="dourado">1x / 1x (qtde)</span></li>
                    </ul>
                  </div>
                  <div role="tabpanel" class="tab-pane sombra" id="hardware">
                    <ul class="list-unstyled">
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                      <li>XP/SP: <span class="dourado">X5</span></li>
                    </ul>
                  </div>
                </div>
            </div>
            <div class="row raidboss">
              <a href="raidboss.php"><img src="img/raidboss.jpg" class="img-responsive"></a>
            </div>
            <div class="row estatisticas">
              <div class="col-md-4 cEstatic">
                <h4>Chars</h4>
                <p>1kk+</p>
                <img src="img/fundoChars.png" class="img-responsive">
              </div>
              <div class="col-md-4 cEstatic">
                <h4>Chars</h4>
                <p>1kk+</p>
                <img src="img/fundoChars.png" class="img-responsive">
              </div>
              <div class="col-md-4 cEstatic">
                <h4>Chars</h4>
                <p>1kk+</p>
                <img src="img/fundoChars.png" class="img-responsive">
              </div>
            </div>
            <div class="row tops">
              <h3 class="titulosPrincipais">
                Tops
              </h3>
              <ul class="nav nav-tabs" role="tablist">
                  <li role="presentation" class="active"><a href="#pvp" aria-controls="pvp" role="tab" data-toggle="tab">PVP</a></li>
                  <li role="presentation"><a href="#pk" aria-controls="pk" role="tab" data-toggle="tab">PK</a></li>
                  <li role="presentation"><a href="#online" aria-controls="online" role="tab" data-toggle="tab">ONLINE</a></li>

                </ul>


                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active sombra" id="pvp">
                    <table class="table table-hover">
                      <thead class="titulosTabela">
                        <tr>
                          <th>#</th>
                          <th>Jogador</th>
                          <th>Pontos</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php

                      $pvps = mssql_query("SELECT TOP 10 * FROM user_data order by Duel DESC");

                      $cont1 = 0;
                      while ($retorno = mssql_fetch_object($pvps)){
                          $cont1++;
                          echo "<tr>";
                          echo "<th scope=row>$cont1</th>";
                          echo "<td>".$retorno->char_name."</td>";
                          echo "<td>".$retorno->Duel."</td>";
                          echo "</tr>";
                      }



                      ?>

                     </tbody> </table>
                  </div>
                  <div role="tabpanel" class="tab-pane sombra" id="pk">
                    <table class="table table-hover">
                      <thead class="titulosTabela">
                        <tr>
                          <th>#</th>
                          <th>Jogador</th>
                          <th>Pontos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $pks = mssql_query("SELECT TOP 10 * FROM user_data order by PK DESC");

                          $cont1 = 0;
                          while ($retorno = mssql_fetch_object($pks)){
                            $cont1++;
                            echo "<tr>";
                            echo "<th scope=row>$cont1</th>";
                            echo "<td>".$retorno->char_name."</td>";
                            echo "<td>".$retorno->PK."</td>";
                            echo "</tr>";
                          }
                        ?>
                     </tbody> </table>
                  </div>
                  <div role="tabpanel" class="tab-pane sombra" id="online">
                   <table class="table table-hover">
                      <thead class="titulosTabela">
                        <tr>
                          <th>#</th>
                          <th>Jogador</th>
                          <th>Tempo</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $online = mssql_query("SELECT TOP 10 * FROM user_data order by use_time DESC");

                          $cont1 = 0;
                          while ($retorno = mssql_fetch_object($online)){
                            $cont1++;
                            echo "<tr>";
                            echo "<th scope=row>$cont1</th>";
                            echo "<td>".$retorno->char_name."</td>";
                            $seconds =  $retorno->use_time/60;
                            $hours = floor($seconds / 3600);
                            $minutes = floor($seconds % 3600 / 60);
                            $seconds = $seconds % 60;
                            echo "<td>". sprintf("%d:%02d:%02d", $hours, $minutes, $seconds) ."</td>";
                            echo "</tr>";
                          }
                        ?>
                     </tbody> </table>
                  </div>
                </div>
            </div>
            <div class="row social">
              <div class="fb-page" data-href="https://www.facebook.com/UltimaL2/" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/UltimaL2/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/UltimaL2/">UltimaL2</a></blockquote></div>
            </div>
          </div>
          <div class="col-md-9 controladorEsquerda">

            <div class="chamadaPrincipal sombra">
              <div class="titulodestaque">BÔNUS EXTRA NO FIM DE SEMANA</div>
              <div class="col-md-5 imagemChamadaPrincipal">
                <img src="img/chamadaPrincipal.png" class="img-responsive">
              </div>
              <div class="col-md-7 textoChamadaPrincipal">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris in tincidunt diam. Etiam est metus, euismod in interdum quis, dignissim et massa. Donec gravida nunc eu dolor feugiat congue. Praesent quis lobortis nisi. Maecenas porttitor, dui eget congue eleifend, magna massa sollicitudin nibh, id euismod nisi ante vitae enim. Donec venenatis tempus egestas. Magna massa sollicitudin nibh, id euismod nisi ante vitae enim. Donec venenatis tempus egestas.</p>
              </div>
            </div>
            <div class="clearboth"></div>
           <div class="chamadaPrincipal noticias">
             <h3 class="titulosPrincipais">
                Últimas Notícias
              </h3>

            <div class="col-md-12 noticia sombra">
              <div class="col-md-4 imagemNoticia"><img src="img/chamadaPrincipal.png" class="img-responsive"></div>
              <div class="col-md-8 textoNoticia">
                <h4>Titulo da notícia</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris in tincidunt diam. Etiam est metus, euismod in interdum quis, dignissim et massa. Donec gravida nunc eu dolor feugiat congue. Praesent quis lobortis nisi. </p>
                <a href="">+ ler a notícia</a>
              </div>
            </div>

            <div class="col-md-12 noticia sombra">
              <div class="col-md-4 imagemNoticia"><img src="img/chamadaPrincipal.png" class="img-responsive"></div>
              <div class="col-md-8 textoNoticia">
                <h4>Titulo da notícia</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris in tincidunt diam. Etiam est metus, euismod in interdum quis, dignissim et massa. Donec gravida nunc eu dolor feugiat congue. Praesent quis lobortis nisi. </p>
                <a href="">+ ler a notícia</a>
              </div>
            </div>

            <div class="col-md-12 noticia sombra">
              <div class="col-md-4 imagemNoticia"><img src="img/chamadaPrincipal.png" class="img-responsive"></div>
              <div class="col-md-8 textoNoticia">
                <h4>Titulo da notícia</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris in tincidunt diam. Etiam est metus, euismod in interdum quis, dignissim et massa. Donec gravida nunc eu dolor feugiat congue. Praesent quis lobortis nisi. </p>
                <a href="">+ ler a notícia</a>
              </div>
            </div>

            <div class="col-md-12 noticia sombra">
              <div class="col-md-4 imagemNoticia"><img src="img/chamadaPrincipal.png" class="img-responsive"></div>
              <div class="col-md-8 textoNoticia">
                <h4>Titulo da notícia</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris in tincidunt diam. Etiam est metus, euismod in interdum quis, dignissim et massa. Donec gravida nunc eu dolor feugiat congue. Praesent quis lobortis nisi. </p>
                <a href="">+ ler a notícia</a>
              </div>
            </div>
            <div class="col-md-12 siege sombra">
              <h3 class="titulosPrincipais">
                Siege
              </h3>

<?php include "script.php"; ?>
               <?php while ($retorno = mssql_fetch_object($gludio)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/gludio.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Gludio Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>

             <?php while ($retorno = mssql_fetch_object($aden)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/aden.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Aden Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>

             <?php while ($retorno = mssql_fetch_object($goddard)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/goddard.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Goddard Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <?php while ($retorno = mssql_fetch_object($schuttgart)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/schuttgart.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Schuttgart Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <?php while ($retorno = mssql_fetch_object($innadril)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/innadril.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Innadril Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <?php while ($retorno = mssql_fetch_object($giran)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/giran.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Giran Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <?php while ($retorno = mssql_fetch_object($oren)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/oren.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Oren Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <?php while ($retorno = mssql_fetch_object($rune)){ ?>
               <div class="col-md-6 cSiege">
                 <div class="col-md-4"><img src="img/rune.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Rune Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
             <div class="limpa"></div>
             <?php while ($retorno = mssql_fetch_object($dion)){ ?>
               <div class="finalSiege cSiege">
                 <div class="col-md-4"><img src="img/dion.jpg" class="img-responsive"></div>
                 <div class="col-md-8">
                   <h4>Dion Castle</h4>
                   <p>Próxima siege: <span><?php echo date('D\, j M Y H\:i',$retorno->sdate); ?></span></p>
                   <p>Clan: <span><?php echo $retorno->clan_name; ?></span></p>
                   <p>Ally: <span><?php echo $retorno->ally_name; ?></span></p>
                   <p>Dono: <span><?php echo $retorno->char_name; ?></span></p>
                 </div>
               </div>
             <?php } ?>
            </div>

           </div>
          </div>
      </div>
    </div>
<?php include "rodape.php"; ?>
