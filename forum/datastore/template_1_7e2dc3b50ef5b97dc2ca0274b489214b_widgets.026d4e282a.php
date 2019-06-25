<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_nexus_front_widgets extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = '219a465162417b884b4278d1aeb82cb2';\n\tfunction donations(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<h3 class=\"ipsType_reset ipsWidget_title\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'current_donation_goals', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n\n<div class='ipsWidget_inner'>\n\t<ul class='ipsDataList'>\n\t\t\nCONTENT;\n\nforeach ( \\IPS\\nexus\\Donation\\Goal::roots() as $goal ):\n$return .= <<<CONTENT\n\n\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $goal->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\" data-ipsDialog data-ipsDialog-title=\"\nCONTENT;\n$return .= htmlspecialchars( $goal->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\n\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t<div class='ipsType_large ipsType_break ipsType_blendLinks'>\nCONTENT;\n$return .= htmlspecialchars( $goal->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/div>\n\t\t\t\t\t\t\nCONTENT;\n\nif ( $goal->goal ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t<div class=\"ipsType_center ipsSpacer_top\">\n\t\t\t\t\t\t\t\t\t<div class=\"cDonateProgressBar cDonateProgressBar_condensed\">\n\t\t\t\t\t\t\t\t\t\t<div class=\"cDonateProgressBar_progress\" style=\"width:\nCONTENT;\n\n$return .= htmlspecialchars( number_format( 100\/$goal->goal*$goal->current, 2, '.', ''), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n%\"><\/div>\n\t\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\t\t\t<span class=\"ipsType_light ipsType_small\">\n\t\t\t\t\t\t\t\t\t\t\nCONTENT;\n\n$sprintf = array(new \\IPS\\nexus\\Money( $goal->current, $goal->currency ), new \\IPS\\nexus\\Money( $goal->goal, $goal->currency )); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'donate_progress', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t\t\t<\/span>\n\t\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<div class=\"ipsType_light ipsType_small\">\n\t\t\t\t\t\t\t\t\nCONTENT;\n\n$sprintf = array(new \\IPS\\nexus\\Money( $goal->current, $goal->currency )); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'donate_sofar', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t\t<\/div>\n\t\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t<\/ul>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction pendingActions( $pendingTransactions, $pendingShipments, $pendingWithdrawals, $openSupportRequests, $pendingAdvertisements, $hostingErrors ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n<h3 class=\"ipsType_reset ipsWidget_title\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'block_pendingActions', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n<ul class=\"ipsDataList\" id='elNexusActions'>\n\t\nCONTENT;\n\nif ( $pendingTransactions !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<li class=\"ipsDataItem\">\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=transactions&filter=tstatus_hold\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingTransactions < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingTransactions > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingTransactions );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_transactions', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $pendingShipments !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<li class='ipsDataItem'>\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=shipping&filter=sstatus_pend\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingShipments < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingShipments > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingShipments );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_shipments', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $pendingWithdrawals !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<li class=\"ipsDataItem\">\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=payments&controller=payouts&filter=postatus_pend\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingWithdrawals < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingWithdrawals > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingWithdrawals );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_widthdrawals', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $openSupportRequests !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<li class=\"ipsDataItem\">\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=support&controller=requests\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $openSupportRequests < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $openSupportRequests > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $openSupportRequests );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'open_support_requests', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $pendingAdvertisements !== NULL ):\n$return .= <<<CONTENT\n\n\t\t<li class=\"ipsDataItem\">\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=core&module=promotion&controller=advertisements\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $pendingAdvertisements < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $pendingAdvertisements > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $pendingAdvertisements );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_advertisements', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $hostingErrors !== NULL and \\IPS\\Member::loggedIn()->hasAcpRestriction( 'nexus', 'hosting', 'errors_manage' ) ):\n$return .= <<<CONTENT\n\n\t\t<li class=\"ipsDataItem\">\n\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=hosting&controller=errors\", \"admin\", \"\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t<div class='ipsDataItem_generic ipsDataItem_size1'>\n\t\t\t\t\t<span class='cNexusActionBadge \nCONTENT;\n\nif ( $hostingErrors < 1 ):\n$return .= <<<CONTENT\ncNexusActionBadge_off\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\nCONTENT;\n\nif ( $hostingErrors > 99 ):\n$return .= <<<CONTENT\n99+\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $hostingErrors );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/div>\n\t\t\t\t<div class='ipsDataItem_main ipsPos_middle ipsType_normal'>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hosting_errors', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/div>\n\t\t\t<\/a>\n\t\t<\/li>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/ul>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction subscriptions(  ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<h3 class=\"ipsType_reset ipsWidget_title\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'nexus_subs_widget_title', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/h3>\n<div class='ipsWidget_inner'>\n\t<ul class='ipsDataList'>\n\t\t\nCONTENT;\n\nforeach ( \\IPS\\nexus\\Subscription\\Package::roots() as $package ):\n$return .= <<<CONTENT\n\n\t\t\t<li class=\"ipsDataItem\">\n\t\t\t\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $package->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" class=\"ipsType_blendLinks\">\n\t\t\t\t\t<div class='ipsDataItem_main'>\n\t\t\t\t\t\t<div class='cWidgetSubscription ipsPad_half'>\n\t\t\t\t\t\t\t<div class=\"cWidgetSubscription_bg\" style='background-image: url( \"\nCONTENT;\n\n$return .= htmlspecialchars( str_replace( array( '(', ')' ), array( '\\(', '\\)' ), $package->_image->url ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" )'><\/div>\n\t\t\t\t\t\t\t<span class='cWidgetSubscription_text'>\n                                \nCONTENT;\n\n$sprintf = array($package->_title, $package->priceBlurb()); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'nexus_subs_widget_title_and_price', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n\n                                \nCONTENT;\n\nif ( \\IPS\\Member::loggedIn()->language()->checkKeyExists('nexus_tax_explain_val') ):\n$return .= <<<CONTENT\n<span class='cNexusPrice_tax ipsType_light'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'nexus_tax_explain_val', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/span>\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n                            <\/span>\n\t\t\t\t\t\t<\/div>\n\t\t\t\t\t<\/div>\n\t\t\t\t<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t<\/ul>\n\t<p class='cWidgetSubscription_linkbox ipsType_reset ipsPad_half ipsType_right ipsType_small'>\n\t\t<a href='\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=nexus&module=subscriptions&controller=subscriptions\", null, \"nexus_subscriptions\", array(), 0 ) );\n$return .= <<<CONTENT\n' class='ipsType_light'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'nexus_subs_show_all_subs', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <i class='fa fa-caret-right'><\/i><\/a>\n\t<\/p>\n<\/div>\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
