<?php require_once('../private/configs.php'); $siteVinculed = 1;

###########################################################
##                   Configurações                       ##
###########################################################
$panel_url = 'l2paradiseclub.com/ucp'; // Digite exatamente o URL onde se encontra este painel (exemplo: www.l2server.com/ucp)
$itemDelivery = 1; // Seu servidor conta com o sistema Delivery? Se possuir, você pode utilizá-lo para entrega de itens. Caso não possua ou prefira não usá-lo, usaremos o sistema de cacheD para entregar os itens in-game. (1 = Usar Delivery | 0 = Usar CacheD)
$themeColor = 'default'; // Qual a tonalidade de cor predominante na template? (Escolha: default, black, blue, red, green ou purple)
$defaultLang = 'EN'; // Idioma padrão do painel (Escolha entre: PT, EN ou ES) - O painel conta com um sistema inteligente que detecta o idioma do navegador do usuário e exibe tudo naquele idioma, mas caso não consigamos detectar ou caso o navegador esteja num idioma diferente dos três citados anteriormente, o idioma setado aqui será o exibido
$gmt = '0'; // Se os scripts do painel estiverem num horário adiantado ou atrasado, altere o GMT. Exemplo: -1 (-1 hora), +3 (+3 horas), etc


###########################################################
##              Controle de funcionalidades              ##
###########################################################
// Quais funcionalidades estão disponíveis para os jogadores? (1 = Disponível | 0 = Indisponível)
$funct['regist'] = 1; // Se cadastrar através do painel
$funct['forgot'] = 1; // Recuperar conta através do painel
$funct['donate'] = 1; // Fazer doações/adquirir moedas
$funct['trnsf1'] = 0; // Transferir moedas online para um personagem in-game - Possibilita converter seu saldo para coins/ticket in-game
$funct['trnsf2'] = 0; // Transferir moedas online para outra conta
$funct['trnsf3'] = 0; // Transferir moedas de um personagem in-game para saldo online - Possibilita acrescentar saldo removendo coins/ticket in-game (POR SEGURANCA, ESSA FUNCAO FICA DESATIVADA. CASO QUEIRA ATIVA-LA, SOLICITE A ATUALSTUDIO)
$funct['gamst1'] = 1; // Game Stats - Top PvP
$funct['gamst2'] = 1; // Game Stats - Top PK
$funct['gamst3'] = 1; // Game Stats - Top Clan
$funct['gamst4'] = 1; // Game Stats - Top Online
$funct['gamst5'] = 1; // Game Stats - Grand Olympiad
$funct['gamst6'] = 1; // Game Stats - Boss Status
$funct['gamst7'] = 1; // Game Stats - Castle & Siege
$funct['gamst8'] = 1; // Game Stats - Top Level
$funct['gamst9'] = 1; // Game Stats - Top Adena
$funct['gams10'] = 0; // Game Stats - Boss Jewels Control
$funct['config'] = 1; // Configurações (alterar dados da conta)


###########################################################
##                Cadastro e Recuperação                 ##
###########################################################
// Caso indisponibilize acima as opções de "Se cadastrar através do painel" ou "Recuperar conta através do painel", você pode inserir abaixo links externos para que os jogadores possam se cadastrar ou recuperar suas contas em uma página externa (caso deixe em branco, as opções irão sumir)
$link_regist = ""; // Link da página externa de cadastro
$link_forgot = ""; // Link da página externa de recuperar


###########################################################
##                  Aquisição de Saldo                   ##
###########################################################
$coinName = 'Cash'; // Nome da moeda online que representa o saldo (usada apenas no painel de usuário)
$coinName_mini = 'Cash'; // Nome resumido da moeda
$coinQntV = 100; // Qual a quantidade comercializada? Você definirá o valor dessa quantidade logo abaixo. (ex: se definir 10 aqui e nas configurações dos "Modulos de doação" abaixo definir 1.00 como valor, o usuário poderá adquirir 10 por R$ 1,00, 20 por R$ 2,00, etc)

// Bonus em porcentagem ao adquirir moeda online em altas quantidades (Exemplo: a cada 100 moedas compradas, ganha 10%, ou seja, paga pelas 100, mas recebe 110)
$bonusActived = 1; // Deseja habilitar a bonificação por compra em quantidade? (1 = Sim | 0 = Não)

// Você pode inserir até 3 bonificações! Caso não queira usar alguma, basta setar os valores como '0' que será desconsiderada.

// Bonificação 1:
$buyCoins['bonus_count'][1] = '10000'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][1] = '20'; // Qual a porcentagem de bonificação?

// Bonificação 2:
$buyCoins['bonus_count'][2] = '20000'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][2] = '20'; // Qual a porcentagem de bonificação?

// Bonificação 3:
$buyCoins['bonus_count'][3] = '40000'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][3] = '20'; // Qual a porcentagem de bonificação?

// Exclusão de fatura
$delFatura = 1; // O usuário pode excluir uma fatura? (1 = Sim | 0 = Não) - OBS: Uma fatura nunca é excluída, ela é ocultada, mas sempre permanecerá no banco de dados.


###########################################################
##         Transferência por coin/ticket in-game         ##
###########################################################
// Caso esteja habilitada a funcionalidade "Transferir moedas online para um personagem in-game", o jogador poderá converter seu saldo online para moedas in-game! Precisamos definir algumas informações...
$coinGame = 'Cash'; // Nome da moeda donate in-game (geralmente Coin, Ticket ou Gold)
$coinID = 0; // ID da moeda


###########################################################
##                   Modulos de doação                   ##
###########################################################

$autoDelivery = 1; // Você deseja que a entrega do saldo seja feita de forma automática? (1 = Sim | 0 = Não) (se optar de forma manual, as doações pagas ficarão com status "Paga". Você terá que ir até o painel admin e concluí-las clicando no botão "Entregar". Quando concluir, o saldo será adicionado e o status passará a ser "Entregue")
$donateEmail = 'donate@l2paradiseclub.com'; // Email que receberá os comprovantes de pagamento para as transações bancárias e módulos de confirmação manual

// PAGSEGURO CONFIGS:
$PagSeguro['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PagSeguro['email'] = 'gamerskut@live.com'; // Email da conta que receberá as doações
$PagSeguro['token'] = '310D1C07A0524D9790065C4849ADD8B9'; // Token gerado no PagSeguro
$PagSeguro['token_sandbox'] = '9A1DF42B9E9547209C33C74197AEA774'; // Token gerado no ambiente de testes do PagSeguro
$PagSeguro['testando'] = 0; // Está testando o sistema através do PagSeguro Sandbox? (1 = Sim | 0 = Não)
$PagSeguro['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Reais)

// PAYPAL CONFIGS:
$PayPal['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PayPal['business_email'] = 'marciohattori@live.com'; // Email da conta que receberá as doações
$PayPal['USD']['coin_price'] = '0.27'; // Valor da quantidade comercializada (em Dolar)
$PayPal['BRL']['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Reais)
$PayPal['EUR']['coin_price'] = '0.23'; // Valor da quantidade comercializada (em Euros)
$PayPal['testando'] = 1; // Está testando o sistema através do PayPal Sandbox? (1 = Sim | 0 = Não)

// TRANSACAO BANCARIA:
$Banking['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$Banking['currency'] = 'BRL'; // Código da moeda
$Banking['coin_price'] = '1.00'; // Valor da quantidade comercializada
$Banking['bank_dados'] = '
<b>CAIXA ECONÔMICA FEDERAL OU CASAS LOTÉRICAS</b><br />
<b>AGÊNCIA:</b> 0000<br />
<b>OPERAÇÃO:</b> 013<br />
<b>CONTA POUPANÇA:</b> 000-1<br />
<b>TITULAR:</b> ADMIN NAME';
