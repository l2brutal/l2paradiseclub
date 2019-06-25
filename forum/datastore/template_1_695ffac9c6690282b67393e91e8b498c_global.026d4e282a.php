<?php

return <<<'VALUE'
"namespace IPS\\Theme;\nclass class_nexus_admin_global extends \\IPS\\Theme\\Template\n{\n\tpublic $cache_key = 'd1b5490a77d02de1ac2056265b4ddaaf';\n\tfunction userLink( $member ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n\nif ( $member->member_id ):\n$return .= <<<CONTENT\n\n\t<a href=\"\nCONTENT;\n$return .= htmlspecialchars( $member->acpUrl(), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\">\nCONTENT;\n\nif ( $member->cm_name ):\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $member->cm_name, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\nCONTENT;\n$return .= htmlspecialchars( $member->name, ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n<\/a>\n\nCONTENT;\n\nelse:\n$return .= <<<CONTENT\n\n\t\nCONTENT;\n\n$return .= \\IPS\\Member::loggedIn()->language()->addToStack( htmlspecialchars( 'guest', ENT_DISALLOWED, 'UTF-8', FALSE ), TRUE, array(  ) );\n$return .= <<<CONTENT\n\n\nCONTENT;\n\nendif;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}}"
VALUE;
