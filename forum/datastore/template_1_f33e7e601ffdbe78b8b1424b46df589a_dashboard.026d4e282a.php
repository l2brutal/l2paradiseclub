<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_nexus_admin_dashboard extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'd1b5490a77d02de1ac2056265b4ddaaf';\n\tfunction pendingActions( $pendingTransactions, $pendingShipments, $pendingWithdrawals, $openSupportRequests, $pendingAdvertisements, $hostingErrors ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n<div class='ipsGrid ipsGrid_collapsePhone'>\n\t<div class='ipsGrid_span6'>\n\t\t<ul class=\"ipsDataList\" id='elNexusActions'>\n\t\t\t\nCONTENT;\n\nif ( $pendingTransactions !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=transactions&filter=tstatus_hold\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingTransactions < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingTransactions > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingTransactions );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_transactions', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $pendingShipments !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class='ipsDataItem'>\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=shipping&filter=sstatus_pend\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingShipments < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingShipments > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingShipments );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_shipments', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $pendingWithdrawals !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=payouts&filter=postatus_pend\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingWithdrawals < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingWithdrawals > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingWithdrawals );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_widthdrawals', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/dli>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/ul>\n\t<\/div>\n\t<div class='ipsGrid_span6'>\n\t\t<ul class=\"ipsDataList\" id='elNexusActions'>\n\t\t\t\nCONTENT;\n\nif ( $openSupportRequests !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=support&controller=requests\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $openSupportRequests < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $openSupportRequests > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $openSupportRequests );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'open_support_requests', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $pendingAdvertisements !== NULL ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=core&module=promotion&controller=advertisements\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingAdvertisements < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingAdvertisements > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingAdvertisements );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_advertisements', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $hostingErrors !== NULL and \\IPS\\Member::loggedIn()->hasAcpRestriction( 'nexus', 'hosting', 'errors_manage' ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=hosting&controller=errors\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $hostingErrors < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $hostingErrors > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $hostingErrors );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hosting_errors', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/a>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/ul>\n\t<\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
