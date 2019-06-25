<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_forums_front_global extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'aeddbd60dd39e017faf754f309971fb8';\n\tfunction commentTableHeader( $comment, $topic ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\n$iposted = $topic->container()->contentPostedIn();\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$idField = $topic::$databaseColumnId;\n$return .= <<<CONTENT\n\n<div>\n\t<h3 class='ipsType_sectionHead ipsContained_container'>\n\t\t\nCONTENT;\n\nif ( $topic->unread() ):\n$return .= <<<CONTENT\n\n\t\t\t<span>\n\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $topic->url( 'getNewComment' ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'first_unread_post', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' data-ipsTooltip>\n\t\t\t\t\t<span class='ipsItemStatus'><i class=\"fa \nCONTENT;\n\nif ( in_array( $topic->$idField, $iposted ) ):\n$return .= <<<CONTENT\nfa-star\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nfa-circle\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\"><\/i><\/span>\n\t\t\t\t<\/a>\n\t\t\t<\/span>\n\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( in_array( $topic->$idField, $iposted ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t<span><span class='ipsItemStatus ipsItemStatus_read ipsItemStatus_posted'><i class=\"fa fa-star\"><\/i><\/span><\/span>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<span class='ipsType_break ipsContained'>\n\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $comment->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title='\nCONTENT;\n\n$sprintf = array($topic->title); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'view_this_topic', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'sprintf' => $sprintf ) );\n$return .= <<<CONTENT\n' class='ipsTruncate ipsTruncate_line'>\nCONTENT;\n$return .= htmlspecialchars( $topic->title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\t\t<\/span>\n\t\t\nCONTENT;\n\nif ( $topic->container()->allow_rating ):\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\", 'front' )->rating( 'large', $topic->rating_hits ? ( $topic->rating_total \/ $topic->rating_hits ) : 0 );\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/h3>\n\t<p class='ipsType_normal ipsType_light ipsType_blendLinks ipsType_reset'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'in', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <a href='\nCONTENT;\n$return .= htmlspecialchars( $topic->container()->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\nCONTENT;\n$return .= htmlspecialchars( $topic->container()->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a><\/p>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction embedPost( $comment, $item, $url ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n<div data-embedInfo-maxSize='500' class='ipsRichEmbed'>\n\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"embed\", \"core\" )->embedHeader( $comment, \\IPS\\Member::loggedIn()->language()->addToStack( 'x_replied_to_a_topic', FALSE, array( 'sprintf' => array( $comment->author()->name ) ) ), $comment->mapped('date'), $url );\n$return .= <<<CONTENT\n\n\t<div class='ipsPad_double'>\n\t\t<div class='ipsRichEmbed_originalItem ipsAreaBackground_reset ipsPad ipsSpacer_bottom ipsType_blendLinks'>\n\t\t\t<div>\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"embed\", \"core\" )->embedOriginalItem( $item, TRUE );\n$return .= <<<CONTENT\n\n\t\t\t<\/div>\n\t\t<\/div>\n\n\t\t<div class='ipsType_richText ipsType_medium' data-truncate='3'>\n\t\t\t{$comment->truncated(TRUE)}\n\t\t<\/div>\n\n\t\t\nCONTENT;\n\nif ( \\IPS\\Settings::i()->reputation_enabled and \\IPS\\IPS::classUsesTrait( $comment, 'IPS\\Content\\Reactable' ) and count( $comment->reactions() ) ):\n$return .= <<<CONTENT\n\n\t\t\t<ul class='ipsList_inline ipsSpacer_top ipsSpacer_half'>\n\t\t\t\t<li>\n\t\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->reactionOverview( $comment, TRUE, 'small' );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/li>\n\t\t\t<\/ul>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\t\t\n\t<\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction embedTopic( $item, $url ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n<div data-embedInfo-maxSize='500' class='ipsRichEmbed'>\n\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"embed\", \"core\" )->embedHeader( $item, \\IPS\\Member::loggedIn()->language()->addToStack( 'x_created_topic_in', FALSE, array( 'sprintf' => array( $item->author()->name, $item->container()->_title ) ) ), $item->mapped('date'), $url );\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nif ( $contentImage = $item->contentImages() ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\n$attachType = key( $contentImage[0] );\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\n$firstPhoto = \\IPS\\File::get( $attachType, $contentImage[0][ $attachType ] );\n$return .= <<<CONTENT\n\n\t\t<div class='ipsRichEmbed_masthead ipsRichEmbed_mastheadBg ipsType_center'>\n\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' style='background-image: url( \"\nCONTENT;\n\n$return .= htmlspecialchars( str_replace( array( '(', ')' ), array( '\\(', '\\)' ), $firstPhoto->url ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" )'>\n\t\t\t\t<img src='\nCONTENT;\n$return .= htmlspecialchars( $firstPhoto->url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' class='ipsHide' alt=''>\n\t\t\t<\/a>\n\t\t<\/div>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<div class='ipsPad_double'>\n\t\t<h3 class='ipsRichEmbed_itemTitle ipsTruncate ipsTruncate_line ipsType_blendLinks'>\n\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $url, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title=\"\nCONTENT;\n$return .= htmlspecialchars( $item->mapped('title'), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n$return .= htmlspecialchars( $item->mapped('title'), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\t\t<\/h3>\n\t\t<div class='ipsType_richText ipsType_medium ipsSpacer_top ipsSpacer_half' data-truncate='3'>\n\t\t\t{$item->truncated(TRUE)}\n\t\t<\/div>\n\n\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"embed\", \"core\" )->embedItemStats( $item );\n$return .= <<<CONTENT\n\n\t<\/div>\n<\/div>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction manageFollowNodeRow( $table, $headers, $rows ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nforeach ( $rows as $row ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$contentItemClass = $row::$contentItemClass;\n$return .= <<<CONTENT\n\n\t<li class=\"ipsDataItem \nCONTENT;\n\nif ( method_exists( $row, 'tableClass' ) && $row->tableClass() ):\n$return .= <<<CONTENT\nipsDataItem_\nCONTENT;\n$return .= htmlspecialchars( $row->tableClass(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\" data-controller='core.front.system.manageFollowed' data-followID='\nCONTENT;\n$return .= htmlspecialchars( $row->_followData['follow_area'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n-\nCONTENT;\n$return .= htmlspecialchars( $row->_followData['follow_rel_id'], ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\n\t\t<div class='ipsDataItem_main'>\n\t\t\t<h4 class='ipsDataItem_title'>\n\t\t\t\t\nCONTENT;\n\nif ( $row->_locked ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<i class=\"fa fa-lock\"><\/i>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\n\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $row->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t\nCONTENT;\n$return .= htmlspecialchars( $row->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\t\t\t<\/h4>\n\t\t\t<ul class='ipsList_inline ipsType_light'>\n\t\t\t\t\nCONTENT;\n\n$count = \\IPS\\forums\\Topic::contentCount( $row, TRUE );\n$return .= <<<CONTENT\n\n\t\t\t\t<li>\nCONTENT;\n\n$pluralize = array( $count ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'posts_number', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t<\/ul>\n\t\t<\/div>\n\t\t\n\t\t<div class='ipsDataItem_generic ipsDataItem_size1 ipsType_center ipsType_large'>\n\t\t\t<span class='ipsBadge ipsBadge_icon ipsBadge_new \nCONTENT;\n\nif ( !$row->_followData['follow_is_anon'] ):\n$return .= <<<CONTENT\nipsHide\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n' data-role='followAnonymous' data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'follow_is_anon', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-eye-slash'><\/i><\/span>\n\t\t<\/div>\n\n\t\t<div class='ipsDataItem_generic ipsDataItem_size6'>\n\t\t\t<ul class='ipsList_reset'>\n\t\t\t\t<li title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'follow_when', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' data-role='followDate'><i class='fa fa-clock-o'><\/i> \nCONTENT;\n\n$val = ( $row->_followData['follow_added'] instanceof \\IPS\\DateTime ) ? $row->_followData['follow_added'] : \\IPS\\DateTime::ts( $row->_followData['follow_added'] );$return .= $val->html();\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t<li title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'follow_how', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' data-role='followFrequency'>\n\t\t\t\t\t\nCONTENT;\n\nif ( $row->_followData['follow_notify_freq'] == 'none' ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<i class='fa fa-bell-slash-o'><\/i>\n\t\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<i class='fa fa-bell'><\/i>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\n$val = \"follow_freq_{$row->_followData['follow_notify_freq']}\"; $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/li>\n\t\t\t<\/ul>\n\t\t<\/div>\n\n\t\t<div class='ipsDataItem_generic ipsDataItem_size6 ipsType_center'>\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"system\", \"core\" )->manageFollow( $row->_followData['follow_app'], $row->_followData['follow_area'], $row->_followData['follow_rel_id'] );\n$return .= <<<CONTENT\n\n\t\t<\/div>\n\n\t\t\nCONTENT;\n\nif ( $table->canModerate() ):\n$return .= <<<CONTENT\n\n\t\t\t<div class='ipsDataItem_modCheck'>\n\t\t\t\t<span class='ipsCustomInput'>\n\t\t\t\t\t<input type='checkbox' data-role='moderation' name=\"moderate[\nCONTENT;\n$return .= htmlspecialchars( $row->_id, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n]\" data-actions=\"\nCONTENT;\n\n$return .= htmlspecialchars( implode( ' ', $table->multimodActions( $row ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" data-state=''>\n\t\t\t\t\t<span><\/span>\n\t\t\t\t<\/span>\n\t\t\t<\/div>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/li>\n\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction row( $table, $headers, $topic, $showReadMarkers=TRUE ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\n$idField = $topic::$databaseColumnId;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$iPosted = isset( $table->contentPostedIn ) ? $table->contentPostedIn : ( $table AND method_exists( $table, 'container' ) AND $topic->container() !== NULL ) ? $topic->container()->contentPostedIn() : array();\n$return .= <<<CONTENT\n\n<li class=\"ipsDataItem ipsDataItem_responsivePhoto \nCONTENT;\n\nif ( $topic->unread() ):\n$return .= <<<CONTENT\nipsDataItem_unread\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( method_exists( $topic, 'tableClass' ) && $topic->tableClass() ):\n$return .= <<<CONTENT\nipsDataItem_\nCONTENT;\n$return .= htmlspecialchars( $topic->tableClass(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n \nCONTENT;\n\nif ( $topic->hidden() ):\n$return .= <<<CONTENT\nipsModerated\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\">\n\t\nCONTENT;\n\nif ( $showReadMarkers ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( $topic->unread() ):\n$return .= <<<CONTENT\n\n\t\t\t<div class='ipsDataItem_icon ipsPos_top'>\n\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $topic->url( 'getNewComment' ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'first_unread_post', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' data-ipsTooltip>\n\t\t\t\t\t<span class='ipsItemStatus'><i class=\"fa \nCONTENT;\n\nif ( in_array( $topic->$idField, $iPosted ) ):\n$return .= <<<CONTENT\nfa-star\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nfa-circle\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\"><\/i><\/span>\n\t\t\t\t<\/a>\n\t\t\t<\/div>\n\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( in_array( $topic->$idField, $iPosted ) ):\n$return .= <<<CONTENT\n\n\t\t\t\t<div class='ipsDataItem_icon ipsPos_top'>\n\t\t\t\t\t<span class='ipsItemStatus ipsItemStatus_read ipsItemStatus_posted'><i class=\"fa fa-star\"><\/i><\/span>\n\t\t\t\t<\/div>\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t<div class='ipsDataItem_icon ipsPos_top'>&nbsp;<\/div>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<div class='ipsDataItem_main'>\n\t\t<h4 class='ipsDataItem_title ipsContained_container'>\n\t\t\t\nCONTENT;\n\nif ( $topic->mapped('pinned') || $topic->mapped('featured') || $topic->hidden() === -1 || $topic->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $topic->hidden() === -1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span><span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n$return .= htmlspecialchars( $topic->hiddenBlurb(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n'><i class='fa fa-eye-slash'><\/i><\/span><\/span>\n\t\t\t\t\nCONTENT;\n\nelseif ( $topic->hidden() === 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span><span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_warning\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pending_approval', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-warning'><\/i><\/span><\/span>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\t\t\t\t\t\t\t\n\t\t\t\t\nCONTENT;\n\nif ( $topic->mapped('pinned') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span><span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'pinned', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-thumb-tack'><\/i><\/span><\/span>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $topic->mapped('featured') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<span><span class=\"ipsBadge ipsBadge_icon ipsBadge_small ipsBadge_positive\" data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'featured', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'><i class='fa fa-star'><\/i><\/span><\/span>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( $topic->prefix() ):\n$return .= <<<CONTENT\n\n\t\t\t\t<span>\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->prefix( $topic->prefix( TRUE ), $topic->prefix() );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\n\t\t\t<span class='ipsType_break ipsContained'>\n\t\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $topic->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' \nCONTENT;\n\nif ( $topic->canView() ):\n$return .= <<<CONTENT\ndata-ipsHover data-ipsHover-target='\nCONTENT;\n$return .= htmlspecialchars( $topic->url()->setQueryString('preview', 1), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' data-ipsHover-timeout='1.5' \nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n>\n\t\t\t\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t\t<strong class='ipsType_light'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'question_title', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n:<\/strong>\n\t\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\t\t\nCONTENT;\n$return .= htmlspecialchars( $topic->mapped('title'), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\t\t\t\t<\/a>\n\n\t\t\t\t\nCONTENT;\n\nif ( $topic->commentPageCount() > 1 ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t{$topic->commentPagination( array(), 'miniPagination' )}\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<\/span>\n\t\t<\/h4>\n\t\t\n\t\t<p class='ipsType_reset ipsType_medium ipsType_light'>\n\t\t\t\nCONTENT;\n\n$htmlsprintf = array($topic->author()->link()); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'byline', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'htmlsprintf' => $htmlsprintf ) );\n$return .= <<<CONTENT\n \nCONTENT;\n\n$val = ( $topic->mapped('date') instanceof \\IPS\\DateTime ) ? $topic->mapped('date') : \\IPS\\DateTime::ts( $topic->mapped('date') );$return .= $val->html();\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nif ( \\IPS\\Request::i()->controller != 'forums' ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'in', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n <a href=\"\nCONTENT;\n$return .= htmlspecialchars( $topic->container()->url(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n$return .= htmlspecialchars( $topic->container()->_title, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/p>\n\t\t<ul class='ipsList_inline ipsClearfix ipsType_light'>\n\t\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $topic->topic_answered_pid ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<li class='ipsType_success'><i class='fa fa-check-circle'><\/i> <strong>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'answered', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/strong><\/li>\n\t\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\t<li class='ipsType_light'><i class='fa fa-question'><\/i> \nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'awaiting_answer', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/li>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/ul>\n\t\t\nCONTENT;\n\nif ( count( $topic->tags() ) ):\n$return .= <<<CONTENT\n\n\t\t\t&nbsp;&nbsp;\n\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->tags( $topic->tags(), true, true );\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n\t<ul class='ipsDataItem_stats'>\n\t\t\nCONTENT;\n\nif ( $topic->isQuestion() ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<span class='ipsDataItem_stats_number'>\nCONTENT;\n\nif ( $topic->question_rating ):\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $topic->question_rating, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n0\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<span class='ipsDataItem_stats_type'>\nCONTENT;\n\n$pluralize = array( $topic->question_rating ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'votes_no_number', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t<\/li>\t\n\t\t\t\nCONTENT;\n\nforeach ( $topic->stats(FALSE) as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\nif ( $k == 'forums_comments' OR $k == 'answers_no_number' ):\n$return .= <<<CONTENT\n\n\t\t\t\t\t<li>\n\t\t\t\t\t\t<span class='ipsDataItem_stats_number'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $v );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t\t<span class='ipsDataItem_stats_type'>\nCONTENT;\n\n$pluralize = array( $v ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'answers_no_number', ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t<\/li>\n\t\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nforeach ( $topic->stats(FALSE) as $k => $v ):\n$return .= <<<CONTENT\n\n\t\t\t\t<li \nCONTENT;\n\nif ( in_array( $k, $topic->hotStats ) ):\n$return .= <<<CONTENT\nclass=\"ipsDataItem_stats_hot\" data-text='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hot_item', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' data-ipsTooltip title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'hot_item_desc', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n>\n\t\t\t\t\t<span class='ipsDataItem_stats_number'>\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->formatNumber( $v );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t\t<span class='ipsDataItem_stats_type'>\nCONTENT;\n\n$val = \"{$k}\"; $pluralize = array( $v ); $return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( $val, ENT_DISALLOWED, 'UTF-8', FALSE ), FALSE, array( 'pluralize' => $pluralize ) );\n$return .= <<<CONTENT\n<\/span>\n\t\t\t\t<\/li>\n\t\t\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/ul>\n\t<ul class='ipsDataItem_lastPoster ipsDataItem_withPhoto'>\n\t\t<li>\n\t\t\t\nCONTENT;\n\nif ( $topic->mapped('num_comments') ):\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->userPhoto( $topic->lastCommenter(), 'tiny' );\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"core\" )->userPhoto( $topic->author(), 'tiny' );\n$return .= <<<CONTENT\n\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/li>\n\t\t<li>\n\t\t\t\nCONTENT;\n\nif ( $topic->mapped('num_comments') ):\n$return .= <<<CONTENT\n\n\t\t\t\t{$topic->lastCommenter()->link()}\n\t\t\t\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\t\t\t{$topic->author()->link()}\n\t\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t<\/li>\n\t\t<li class=\"ipsType_light\">\n\t\t\t<a href='\nCONTENT;\n$return .= htmlspecialchars( $topic->url( 'getLastComment' ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n' title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'get_last_post', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n' class='ipsType_blendLinks'>\n\t\t\t\t\nCONTENT;\n\nif ( $topic->mapped('last_comment') ):\n$return .= <<<CONTENT\n\nCONTENT;\n\n$val = ( $topic->mapped('last_comment') instanceof \\IPS\\DateTime ) ? $topic->mapped('last_comment') : \\IPS\\DateTime::ts( $topic->mapped('last_comment') );$return .= $val->html();\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n\n$val = ( $topic->mapped('date') instanceof \\IPS\\DateTime ) ? $topic->mapped('date') : \\IPS\\DateTime::ts( $topic->mapped('date') );$return .= $val->html();\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\t<\/a>\n\t\t<\/li>\n\t<\/ul>\n\t\nCONTENT;\n\nif ( method_exists( $table, 'canModerate' ) AND $table->canModerate() ):\n$return .= <<<CONTENT\n\n\t\t<div class='ipsDataItem_modCheck'>\n\t\t\t<span class='ipsCustomInput'>\n\t\t\t\t<input type='checkbox' data-role='moderation' name=\"moderate[\nCONTENT;\n$return .= htmlspecialchars( $topic->tid, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n]\" data-actions=\"\nCONTENT;\n\n$return .= htmlspecialchars( implode( ' ', $table->multimodActions( $topic ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\" data-state='\nCONTENT;\n\nif ( $topic->tableStates() ):\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $topic->tableStates(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n'>\n\t\t\t\t<span><\/span>\n\t\t\t<\/span>\n\t\t<\/div>\n\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n<\/li>\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction rows( $table, $headers, $rows ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( count( $rows ) ):\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nforeach ( $rows as $row ):\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\n$return .= \\IPS\\Theme::i()->getTemplate( \"global\", \"forums\" )->row( $table, $headers, $row );\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\nendforeach;\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction searchNoPermission( $lang, $link=NULL ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n<li class=\"ipsStreamItem ipsStreamItem_contentBlock ipsAreaBackground_reset ipsPad\">\n\t<div class='ipsType_center ipsType_light ipsType_large'>\n\t\t\nCONTENT;\n$return .= htmlspecialchars( $lang, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nif ( $link ):\n$return .= <<<CONTENT\n <a href=\"\nCONTENT;\n$return .= htmlspecialchars( $link, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'enter_password', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n<\/a>\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/div>\n<\/li>\n\nCONTENT;\n\n\t\treturn $return;\n}\n\n\tfunction viewChange( $hideMobile=FALSE ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( \\IPS\\Settings::i()->forums_default_view_choose and \\IPS\\Member::loggedIn()->member_id ):\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$chooseable = json_decode( \\IPS\\Settings::i()->forums_default_view_choose, true );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\n$chosen = \\IPS\\forums\\Forum::getMemberView();\n$return .= <<<CONTENT\n\n<li>\n\t<ul class='ipsButton_split'>\n\t\t\nCONTENT;\n\nif ( $chooseable == '*' OR in_array('table', $chooseable ) ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=forums&module=forums&controller=index&do=setMethod&method=table\" . \"&csrfKey=\" . \\IPS\\Session::i()->csrfKey, null, \"forums\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class='ipsButton \nCONTENT;\n\nif ( $chosen == 'table' ):\n$return .= <<<CONTENT\nipsButton_primary\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nipsButton_link\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n ipsButton_narrow ipsButton_medium' data-ipsTooltip data-ipsTooltip-safe title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'forums_default_view_table', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t<i class='fa fa-align-justify'><\/i>\n\t\t\t\t<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( $chooseable == '*' OR in_array('grid', $chooseable ) ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=forums&module=forums&controller=index&do=setMethod&method=grid\" . \"&csrfKey=\" . \\IPS\\Session::i()->csrfKey, null, \"forums\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class='ipsButton \nCONTENT;\n\nif ( $chosen == 'grid' ):\n$return .= <<<CONTENT\nipsButton_primary\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nipsButton_link\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n ipsButton_narrow ipsButton_medium' data-ipsTooltip data-ipsTooltip-safe title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'forums_default_view_grid', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t<i class='fa fa-th-large'><\/i>\n\t\t\t\t<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t\t\nCONTENT;\n\nif ( $chooseable == '*' OR in_array('fluid', $chooseable ) ):\n$return .= <<<CONTENT\n\n\t\t\t<li>\n\t\t\t\t<a href=\"\nCONTENT;\n\n$return .= str_replace( '&', '&amp;', \\IPS\\Http\\Url::internal( \"app=forums&module=forums&controller=index&do=setMethod&method=fluid\" . \"&csrfKey=\" . \\IPS\\Session::i()->csrfKey, null, \"forums\", array(), 0 ) );\n$return .= <<<CONTENT\n\" class='ipsButton \nCONTENT;\n\nif ( $chosen == 'fluid' ):\n$return .= <<<CONTENT\nipsButton_primary\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\nipsButton_link\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n ipsButton_narrow ipsButton_medium' data-ipsTooltip data-ipsTooltip-safe title='\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'forums_default_view_fluid', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n'>\n\t\t\t\t\t<i class='fa fa-th-list'><\/i>\n\t\t\t\t<\/a>\n\t\t\t<\/li>\n\t\t\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\n\t<\/ul>\n<\/li>\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
