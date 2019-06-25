<?php

###########################################################
##                   Configurações                       ##
###########################################################
$server_name = 'L2Paradise'; // Nome do servidor
$server_chronicle = 'High Five'; // Crônica do servidor
$server_url = 'l2paradiseclub.com'; // Digite exatamente o URL onde se encontra este site (exemplo: www.omassivo.com.br)


###########################################################
##                   Banco de dados                      ##
###########################################################

# Qual método de conexão você irá utilizar?
$conMethod = 1; // 1 = MsSQL, 2 = SQLSRV, 3 = ODBC, 4 = PDO-ODBC

# Banco de dados de autenticações
$dbnm['DB'] = 'lin2db'; // Nome do banco (geralmente lin2db)
$host['DB'] = '149.56.79.107'; // Endereço do host/server
$port['DB'] = 1433; // Porta do host/server (padrão: 1433)
$user['DB'] = 'sitedb_connect'; // Usuário
$pass['DB'] = 'rdbconnect2020!#@$'; // Senha

# Banco de dados world
$dbnm['WORLD'] = 'lin2world'; // Nome do banco (geralmente lin2world)
$host['WORLD'] = '149.56.79.107'; // Endereço do host/server
$port['WORLD'] = 1433; // Porta do host/server (padrão: 1433)
$user['WORLD'] = 'sitedb_connect'; // Usuário
$pass['WORLD'] = 'rdbconnect2020!#@$'; // Senha

# Banco de dados das tabelas do site
$dbnm['SITE'] = 'lin2site'; // Nome do banco (geralmente lin2site)
$host['SITE'] = '149.56.79.107'; // Endereço do host/server
$port['SITE'] = 1433; // Porta do host/server (padrão: 1433)
$user['SITE'] = 'sitedb_connect'; // Usuário
$pass['SITE'] = 'rdbconnect2020!#@$'; // Senha


###########################################################
##                    Server Status                      ##
###########################################################
$serverIp = '149.56.79.107'; // IP do DB (para buscar o status do servidor)
$loginPort = '2106'; // porta do login/auth
$gamePort = '7777'; // porta do game

// Forçar o site a exibir um certo status (on = Online | off = Offline | auto = Status Real)
$forceLoginStatus = 'auto'; // Auth Status (Padrão: auto)
$forceGameStatus = 'auto'; // Game Status (Padrão: auto)


###########################################################
##                        CacheD                         ##
###########################################################
$cachedIP = '149.56.79.107'; // Qual o IP do dedicado onde está o CacheD?
$cachedPort = 2012; // Qual a porta do CacheD? (Padrão: 2012)


###########################################################
##              Atualstudio Web Admin 3.0                ##
###########################################################
$admpass = 'Pannic123@'; // Senha do painel admin


###########################################################
##               Configurações de e-mail                 ##
###########################################################
$admin_email = 'support@l2paradiseclub.com'; // Endereço de e-mail que os jogadores podem utilizar para entrar em contato
$server_email = 'support@l2paradiseclub.com'; // Seu endereço de e-mail utilizado para enviar e-mails automáticos (exemplo: nao-responda@seuservidor.com)
$vcmemail = 1; // É permitido criar várias contas com um mesmo endereço de e-mail? (1 = Sim | 0 = Não)
$cofemail = 0; // Ao criar conta é necessário confirmar e-mail? (1 = Sim | 0 = Não)
$chaemail = 0; // Os jogadores podem alterar o endereço de e-mail de suas contas? (1 = Sim | 0 = Não)
$chaemail_confirm = 1; // Para alterar o endereço de e-mail é necessário confirmar? Se sim, será enviado um e-mail para o endereço de e-mail atual solicitando confirmação. Caso a conta não possua endereço de e-mail, essa opção será ignorada (1 = Sim | 0 = Não)

# SMTP:
$useSMTP = 1; // Enviar e-mails via SMTP? (1 = Sim | 0 = Não)
$SMTP_host = 'br394.hostgator.com.br'; // Endereço do Host do SMTP
$SMTP_port = 465; // Porta de conexão para a saída de e-mails (consulte seu host, mas geralmente é 587 ou 465)
$SMTP_secu = 'ssl'; // Qual protocolo de segurança? ssl ou tls?
$SMTP_user = 'support@l2paradiseclub.com'; // Usuário de autenticação do SMTP (geralmente o e-mail remetente)
$SMTP_pass = 'Pannic123@'; // Senha de autenticação do SMTP (geralmente a senha do e-mail remetente)


###########################################################
##                        Captcha                        ##
###########################################################
// O captcha é um gerador de códigos que são obrigatórios o preenchimento ao se registrar, logar no painel admin e etc.
$captcha_register_on = 1; // Captcha no formulário de registro (1 = Sim | 0 = Não)
$captcha_cp_on = 0; // Captcha ao logar no painel de controle do usuário (1 = Sim | 0 = Não)
$captcha_forgotpass_on = 1; // Captcha ao enviar pedido de recuperação de conta para e-mail (1 = Sim | 0 = Não)


###########################################################
##                     Diretórios                        ##
###########################################################
$dir_gallery = 'imgs/gallery/'; // Diretório das imagens da galeria
$dir_banners = 'imgs/banners/'; // Diretório das imagens dos banners
$dir_newsimg = 'imgs/news/'; // Diretório das imagens das notícias


###########################################################
##                 Contagem regressiva                   ##
###########################################################
$counterActived = 1; // Ativar contagem regressiva na página inicial? (1 = Sim | 0 = Não)
$cDia = '01'; // Dia
$cMes = '11'; // Mês
$cAno = '2018'; // Ano
$cHor = '13'; // Hora
$cMin = '00'; // Minuto
$cGMT = '-3'; // GMT/UTC
$sumH = 0; // Caso a hora esteja sendo exibida incorretamente, acrescente ou diminua o valor aqui (ex: caso precise diminuir 2 hrs, insira "-2", caso precise acrescentar 3, insira "3" (sem +)


###########################################################
##                  Cadastro de Contas                   ##
###########################################################
$suffixActive = 1; // Ativar sufixo no login? (método de segurança que acrescenta 3 valores aleatórios no login do usuário, para evitar roubo de contas através de listas de logins com senhas que outros admins possuem) (1 = Sim | 0 = Não)
$forceSuffix = 0; // O sufixo é obrigatório? (1 = Sim | 0 = Não) (Se definir '0', os usuários terão a opção "não quero isso" que ignora o sufixo)
$downRegfile = 1; // Download de arquivo TXT após cadastro bem sucedido? (1 = Sim | 0 = Não)
$passRegfile = 1; // Exibir senha no arquivo TXT gerado após cadastro bem sucedido? (1 = Sim | 0 = Não)

# Data de liberação do cadastro (antes dessa data não será possível criar contas) - Caso queira desabilitar, basta inserir uma data que já passou.
$reg['dia'] = '20'; // Dia
$reg['mes'] = '10'; // Mês
$reg['ano'] = '2016'; // Ano
$reg['hr'] = '13'; // Hora
$reg['min'] = '00'; // Minuto


###########################################################
##            Controle de exibição de páginas            ##
###########################################################
// Quais páginas estão disponíveis para acesso? (1 = Disponível | 0 = Indisponível)
$dpage['bosstt'] = 1; // Boss Status
$dpage['bossjl'] = 0; // Boss Jewelry Location
$dpage['galler'] = 1; // Galeria
$dpage['olyall'] = 1; // Olympiad - Histórico de heroes
$dpage['olyher'] = 0; // Olympiad - Heroes atuais
$dpage['olyrak'] = 0; // Olympiad - Ranking
$dpage['csiege'] = 1; // Castle & Siege
$dpage['toppvp'] = 1; // Top PvP
$dpage['toppkp'] = 1; // Top Pk
$dpage['toponl'] = 0; // Top Online
$dpage['toplvl'] = 0; // Top Level
$dpage['topadn'] = 0; // Top Adena
$dpage['topcla'] = 1; // Top Clan
$dpage['unstuk'] = 0; // Unstuck


###########################################################
##                 Rankings e Exibições                  ##
###########################################################
$cacheDelayMin = 5; // Intervalo em minutos que o cache de rankings e estatísticas são atualizados. Ex: se inserir '1' serão atualizados a cada 1 minuto
$countTopPlayers = 50; // Quantidade de jogadores exibidos nos Tops PvP, Pk, Online, Level e Adena
$countTopClan = 50; // Quantidade de clans no Top Clan
$galleryMax = 30; // Quantidade de imagens/vídeos exibidos em cada página da galeria
$galleCount = 6; // Quantidade de imagens/videos exibidos na galeria na lateral do site
$inewsCount = 5; // Quantidade de notícias na página inicial do site
$showPlayersOn = 0; // Exibir quantidade de jogadores online? (1 = Sim | 0 = Não)
$fakePlayers = 1.0; // Multiplicação da quantidade de jogadores online. A quantidade de jogadores online será multiplicada pelo valor inserido aqui. (1.0 = Quantidade real / 1.5 = multiplicado por 1,5 / 2.0 = multiplicado por 2...) - IMPORTANTE: INSIRA PONTO AO INVÉS DE VÍRGULA
$srvOffZeroPl = 1; // Caso o server status esteje off, forçar exibição de 0 players online mesmo que hajam jogadores online? (1 = Sim | 0 = Não)
$olyExibPoint = 0; // Ranking da Grand Olympiad deve exibir os pontos dos jogadores? (1 = Sim | 0 = Não)
$showRankReg = 0; // Exibir rankings antes da data de liberação do cadastro? (1 = Sim | 0 = Não)
$bossJwlIds = "6656,6657,6658,6659,6660,6661,8191"; // IDs das Boss Jewels (Baium Ring, Antharas Earring, etc)
$adnBillionItem = 0; // Existe algum item no servidor que corresponde a 1kkk de adena? Se sim, insira o ID desse item. Ele será somado às adenas no Top Adena.


###########################################################
##                      Facebook                         ##
###########################################################
$facePopupOn = 1; // Exibir popup do Facebook? (1 = Sim | 0 = Não)
$fbPopupDelay = 1; // De quantos em quantos dias o popup deve aparecer novamente? Ex: Se setar 1 ele aparecerá todo dia
$faceBoxOn = 1; // Exibir box do Facebook na página inicial? (1 = Sim | 0 = Não)
$facePage = 'https://www.facebook.com/L2Paradise/'; // Página no Facebook


###########################################################
##              Página de Doações Pública                ##
###########################################################
$coinName = 'Cash (₡)'; // Nome do item donate
$coinPer = '100'; // Quantidade de coins
$coinCur = '$'; // Moeda dessa quantidade
$coinCos = '1.00'; // Valor dessa quantidade


###########################################################
##                Outras Configurações                   ##
###########################################################

$defaultLang = 'EN'; // Idioma padrão do site (Escolha entre: PT, EN ou ES) - O site conta com um sistema inteligente que detecta o idioma do navegador do usuário e exibe tudo naquele idioma, mas caso não consigamos detectar ou caso o navegador esteja num idioma diferente dos três citados anteriormente, o idioma setado aqui será o exibido

$gmt = '-3'; // Se os scripts do site estiverem num horário adiantado ou atrasado, altere o GMT. Exemplo: -1 (-1 hora), +3 (+3 horas), etc

$bannerDelay = 4; // De quantos em quantos segundos os banners na página inicial se revesam?

// Locs X, Y e Z utilizados no 'unstuck' do painel de usuário
$unstuck_loc_x = '83257'; // Padrão: 83257
$unstuck_loc_y = '149058'; // Padrão: 149058
$unstuck_loc_z = '-3400'; // Padrão: -3400
