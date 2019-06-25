<?php

return <<<'VALUE'
"namespace IPS\\Theme;\n\tfunction email_html_core_registration_complete( $member, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_reg_complete\", FALSE, array( 'sprintf' => array( \\IPS\\Settings::i()->board_name ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\n<br \/>\n<br \/>\n<a href='\nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\n' style=\"color: #ffffff; font-family: 'Helvetica Neue', helvetica, sans-serif; text-decoration: none; font-size: 12px; background: \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->email_color;\n$return .= <<<CONTENT\n; line-height: 32px; padding: 0 10px; display: inline-block; border-radius: 3px;\">\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_go_to_site\", FALSE, array( 'sprintf' => array( \\IPS\\Settings::i()->board_name ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n<\/a>\n\n<br \/><br \/>\n<em style='color: #8c8c8c'>&mdash; \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n<\/em>\nCONTENT;\n\n\t\treturn $return;\n}\n\tfunction email_plaintext_core_registration_complete( $member, $email ) {\n\t\t$return = '';\n\t\t$return .= <<<CONTENT\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_reg_complete\", FALSE, array( 'sprintf' => array( \\IPS\\Settings::i()->board_name ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n\n\n\nCONTENT;\n$return .= htmlspecialchars( $email->language->addToStack(\"email_go_to_site\", FALSE, array( 'sprintf' => array( \\IPS\\Settings::i()->board_name ) ) ), ENT_QUOTES | ENT_DISALLOWED, 'UTF-8', FALSE );\n$return .= <<<CONTENT\n: \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->base_url;\n$return .= <<<CONTENT\n\n\n-- \nCONTENT;\n\n$return .= \\IPS\\Settings::i()->board_name;\n$return .= <<<CONTENT\n\nCONTENT;\n\n\t\treturn $return;\n}"
VALUE;
