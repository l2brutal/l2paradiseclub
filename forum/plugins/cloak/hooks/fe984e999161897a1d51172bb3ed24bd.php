//<?php

class hook103 extends _HOOK_CLASS_
{
    public function content()
    {
	try
	{
	        $content = parent::content();
	        $member = \IPS\Member::loggedIn();
	        $container = $this->container();
	
	        /* Only run if they aren't editing content, at the same time check if they are using a supported application */
	        if ( \IPS\Request::i()->do !== 'edit' && ( ( \IPS\Settings::i()->cloak_forums == 0 || in_array( $container->id, explode( ",", \IPS\Settings::i()->cloak_forums ) ) ) ) )
	        {
	            /* Is the user in one of the groups? */
	            if ( \IPS\Settings::i()->cloak_groups == '*' OR $member->inGroup( explode( ",", \IPS\Settings::i()->cloak_groups ) ) )
	            {
	                /* Links, Images, and Attachments */
	                if ( \IPS\Settings::i()->cloak_links OR \IPS\Settings::i()->cloak_attachments )
	                {
	                    /* Find all links and hide accordingly */
	                    preg_match_all('/<a(.*?)>(.*?)<\/a>/s', $content, $links);
	                    for ( $i = 0 ; $i < count( $links[0] ) ; $i++ )
	                    {
	                        /* Don't hide the @mentions */
	                        if ( !mb_strpos( $links[0][$i], "data-mention" ) )
	                        {
	                            /* Replace Attachments */
	                            if ( \IPS\Settings::i()->cloak_attachments )
	                            {
	                                if ( mb_strpos( $links[0][$i], "ipsAttachLink" ) )
	                                {
	                                    $content = str_replace( $links[0][$i], $this->cloakError( 'attachment' ), $content );
	                                }
	                            }
	
	                            /* Replace Links */
	                            if ( \IPS\Settings::i()->cloak_links )
	                            {
	                                if ( !mb_strpos( $links[0][$i], "ipsAttachLink" ) AND !mb_strpos( $links[0][$i], "ipsAttachLink ipsAttachLink_image" ) )
	                                {
	                                    $content = str_replace( $links[0][$i], $this->cloakError( 'link' ), $content );
	                                }
	                            }
	                        }
	                    }
	                }
	
	                /* Replace Images (<img>) */
	                if ( \IPS\Settings::i()->cloak_images )
	                {
	                    preg_match('/(<img.*?>)/', $content, $images);
	                    for ( $i = 0 ; $i < count( $images ) ; $i++ )
	                    {
	                        if ( !mb_strpos( $images[$i], "emoticon" ) )
	                        {
	                            $content = str_replace( $images[$i], $this->cloakError( 'image' ), $content );
	                        }
	                    }
	                }
	
	                /* Replace Code */
	                if( \IPS\Settings::i()->cloak_code )
	                {
	                    $content = preg_replace('#<pre(.*?)>(.*?)</pre>#si', $this->cloakError( 'code' ), $content);
	                }
	
	                /* Replace Quotes */
	                if ( \IPS\Settings::i()->cloak_quotes )
	                {
	                    preg_match_all('/<blockquote(.*)>(.*)<\/blockquote>/s', $content, $quotes);
	                    for ( $i = 0 ; $i < count( $quotes[0] ) ; $i++ )
	                    {
	                        if ( mb_strpos( $quotes[0][$i], "ipsQuote" ) )
	                        {
	                            $content = str_replace( $quotes[0][$i], $this->cloakError( 'quote' ), $content );
	                        }
	                    }
	                }
	
	                /* Replace Spoilers */
	                if( \IPS\Settings::i()->cloak_spoilers )
	                {
	                    $content = preg_replace('#<div class="ipsSpoiler"(.*)>(.*?)</div>#s', $this->cloakError( 'spoiler' ), $content);
	                }
	            }
	        }
	
	        return $content;
	}
	catch ( \RuntimeException $e )
	{
		if ( method_exists( get_parent_class(), __FUNCTION__ ) )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
		else
		{
			throw $e;
		}
	}
    }

    protected function cloakError( $n )
    {
	try
	{
	        try
	        {
	            $member = \IPS\Member::loggedIn();
	
	            switch ( \IPS\Settings::i()->cloak_message_type ) {
	                case 'inline':
	                    if ( !$member->member_id )
	                    {
	                        return "<span class=\"ipsBadge ipsBadge_negative\">" . \IPS\Member::loggedIn()->language()->addToStack('cloak_login_or_register', FALSE, array( 'sprintf' => array( $n ) ) ) . "</span> ";
	                    }
	                    else
	                    {
	                        return "<span class=\"ipsBadge ipsBadge_negative\">" . \IPS\Member::loggedIn()->language()->addToStack('cloak_not_allowed', FALSE, array( 'sprintf' => array( $n ) ) ) . "</span>";
	                    }
	                    break;
	
	                case 'box':
	                    if ( !$member->member_id )
	                    {
	                        return "<p class='ipsMessage ipsMessage_error cloak'>" . \IPS\Member::loggedIn()->language()->addToStack('cloak_login_or_register', FALSE, array( 'sprintf' => array( $n ) ) ) . "</p> ";
	                    }
	                    else
	                    {
	                        return "<p class='ipsMessage ipsMessage_error cloak'>" . \IPS\Member::loggedIn()->language()->addToStack('cloak_not_allowed', FALSE, array( 'sprintf' => array( $n ) ) ) . "</p> ";
	                    }
	                    break;
	
	                case 'invisible':
	                    return NULL;
	                    break;
	
	                case 'custom':
	                    if ( !$member->member_id )
	                    {
	                        return \IPS\Settings::i()->cloak_guest_message;
	                    }
	                    else
	                    {
	                        return \IPS\Settings::i()->cloak_user_message;
	                    }
	                    break;
	            }
	        }
	        catch ( \RuntimeException $e )
	        {
	            if ( method_exists( get_parent_class(), __FUNCTION__ ) )
	            {
	                return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
	            }
	            else
	            {
	                throw $e;
	            }
	        }
	}
	catch ( \RuntimeException $e )
	{
		if ( method_exists( get_parent_class(), __FUNCTION__ ) )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
		else
		{
			throw $e;
		}
	}
    }
}