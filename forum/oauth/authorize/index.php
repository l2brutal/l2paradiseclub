<?php
/**
 * @brief		OAuth Server Authorize Endpoint
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @since		29 Apr 2017
 */

define('REPORT_EXCEPTIONS', TRUE);
require '../../init.php';

class oAuthServerAuthorizationRequest
{
	/**
	 * @brief	Client
	 */
	protected $client;
	
	/**
	 * @brief	Redirect URI to use
	 */
	protected $redirectUri;
	
	/**
	 * @brief	Redirect URI provided
	 */
	protected $providedRedirectUri;
	
	/**
	 * @brief	State
	 */
	protected $state;
	
	/**
	 * @brief	Response Type
	 */
	protected $responseType;
	
	/**
	 * @brief	Scope
	 */
	protected $scope = array();
	
	/**
	 * Init
	 *
	 * @param	string		$clientId		Client ID
	 * @param	string		$redirectUri	Redirect URI, if provided
	 * @param	string|NULL	$state			The state, if provided
	 * @return	void
	 */
	public static function init( $clientId, $redirectUri = NULL, $state = NULL )
	{
		$obj = new static;
		
		/* Get the client */
		try
		{
			$obj->client = \IPS\Api\OAuthClient::load( $clientId );
			if ( !$obj->client->enabled )
			{
				throw new \OutOfRangeException;
			}
		}
		catch ( \OutOfRangeException $e )
		{
			throw new \IPS\Login\Handler\OAuth2\InitException('oauth_err_invalid_client');
		}
		
		/* Set the Redirect URI */
		$allowedRedirectUris = json_decode( $obj->client->redirect_uris );
		if ( $redirectUri )
		{
			if ( !in_array( $redirectUri, $allowedRedirectUris ) )
			{
				throw new \IPS\Login\Handler\OAuth2\InitException('oauth_err_invalid_redirect_uri');
			}
			else
			{
				$obj->redirectUri = \IPS\Http\Url::external( $redirectUri );
			}
		}
		elseif ( count( $allowedRedirectUris ) === 1 )
		{
			$obj->redirectUri = \IPS\Http\Url::external( array_shift( $allowedRedirectUris ) );
		}
		else
		{
			throw new \IPS\Login\Handler\OAuth2\InitException('oauth_err_invalid_redirect_uri');
		}
		$obj->providedRedirectUri = $redirectUri;
		
		/* Set the state, if appliable */
		if ( $state )
		{
			$obj->state = $state;
		}
		
		return $obj;
	}
	
	/**
	 * Set the response type
	 *
	 * @param	string	$responseType	The response type
	 * @return	void
	 */
	public function setResponseType( $responseType )
	{
		if ( $responseType === 'code' )
		{
			if ( !in_array( 'authorization_code', explode( ',', $this->client->grant_types ) ) )
			{
				throw new \IPS\Login\Handler\OAuth2\Exception('unsupported_response_type');
			}
		}
		elseif ( $responseType === 'token' )
		{
			if ( !in_array( 'implicit', explode( ',', $this->client->grant_types ) ) )
			{
				throw new \IPS\Login\Handler\OAuth2\Exception('unsupported_response_type');
			}
		}
		else
		{
			throw new \IPS\Login\Handler\OAuth2\Exception( 'invalid_request', "response_type parameter is required" );
		}
		
		$this->responseType = $responseType;
	}
	
	/**
	 * Set the requested scopes
	 *
	 * @param	string	$scope	Scopes
	 * @return	void
	 */
	public function setScope( $scope )
	{
		$availableScopes = json_decode( $this->client->scopes, TRUE );
		$scopes = explode( ' ', $scope );
		foreach ( $scopes as $requestedScope )
		{
			if ( !array_key_exists( $requestedScope, $availableScopes ) )
			{
				throw new \IPS\Login\Handler\OAuth2\Exception('invalid_scope');
			}
		}
		$this->scope = $scopes;
	}
	
	/**
	 * Get URL to redirect the user back to the client after successful authorization
	 *
	 * @param	\IPS\Member	$member	The member
	 * @param	array		$scopes	The authorized scopes
	 * @return	void
	 */
	public function authorized( \IPS\Member $member, $scopes )
	{
		$scopes = $this->client->choose_scopes ? $scopes : $this->scope;
		
		if ( $this->responseType === 'code' )
		{
			do
			{
				$authorizationCode = \IPS\Login::generateRandomString( 64 );
			}
			while ( \IPS\Db::i()->select( 'COUNT(*)', 'core_oauth_server_authorization_codes', array( 'client_id=? AND code=?', $this->client->client_id, $authorizationCode ) )->first() );
			
			\IPS\Db::i()->insert( 'core_oauth_server_authorization_codes', array(
				'client_id'		=> $this->client->client_id,
				'redirect_uri'	=> $this->providedRedirectUri ?: NULL,
				'member_id'		=> $member->member_id,
				'expires'		=> time() + 60,
				'code'			=> $authorizationCode,
				'scope'			=> $scopes ? json_encode( $scopes ) : NULL
			) );
			
			return $this->redirect( array( 'code' => $authorizationCode ) );
		}
		else
		{
			$accessToken = $this->client->generateAccessToken( $member, $scopes, 'implicit', TRUE );
			
			$response = array( 'access_token' => $accessToken['access_token'], 'token_type' => 'bearer' );
			if ( $accessToken['access_token_expires'] )
			{
				$response['expires_in'] = $accessToken['access_token_expires'] - time();
			}
			
			return $this->redirect( $response );
		}
	}
	
	/**
	 * Get URL to redirect the user back to the client
	 *
	 * @param	array	$response	Response parameters
	 * @return	void
	 */
	public function redirect( array $response )
	{
		if ( $this->state )
		{
			$response['state'] = $this->state;
		}
				
		if ( $this->responseType === 'token' )
		{
			return $this->redirectUri->setFragment( http_build_query( $response, '', '&' ) );
		}
		else
		{
			return $this->redirectUri->setQueryString( $response );
		}
	}
	
	/**
	 * Do we need to be prompted?
	 *
	 * @param	string	$requestedPromptType	Requested prompt type, if provided
	 * @return	bool|array
	 */
	public function promptRequired( $requestedPromptType )
	{
		/* If we're not logged in, we definitely do, unless we cancelled */
		if ( !\IPS\Member::loggedIn()->member_id and ( !isset( \IPS\Request::i()->allow ) or \IPS\Request::i()->allow ) )
		{
			return TRUE;
		}
		
		/* Have we gone through it already? */
		if ( isset( \IPS\Request::i()->allow ) and \IPS\Login::compareHashes( (string) \IPS\Session::i()->csrfKey, (string) \IPS\Request::i()->csrfKey ) )
		{
			if ( !\IPS\Request::i()->allow )
			{
				throw new \IPS\Login\Handler\OAuth2\Exception('access_denied');
			}
			return FALSE;
		}
		
		/* Does the client require it? */
		if ( $this->client->prompt !== 'automatic' )
		{
			return TRUE;
		}
				
		/* Did we request it? */
		if ( $requestedPromptType === 'login' or $requestedPromptType === 'reauthorize' )
		{
			return TRUE;
		}
		
		/* Do we already have an access token with these scopes? */
		$accessToken = $this->client->getAccessToken( \IPS\Member::loggedIn(), $this->scope );
		if ( $accessToken )
		{
			\IPS\Request::i()->grantedScope = array_combine( json_decode( $accessToken['scope'], TRUE ), array_fill( 0, count( json_decode( $accessToken['scope'], TRUE ) ), TRUE ) );
			return FALSE;
		}
		
		/* No? We need a new token */
		return TRUE;
	}
	
	/**
	 * Show authorization form
	 *
	 * @param	string	$requestedPromptType	Requested prompt type, if provided
	 * @param	bool	$loggedIn				Has the user logged in?
	 * @return	void
	 */
	public function prompt( $requestedPromptType, $loggedIn )
	{
		/* We mustn't have the redirect_uri in the URL when displaying the page as this needs to be handled securely (it will
			probably include a client-issued CSRF key) and if we use it in the URL, any third party scripts that may be being
			used on the community (tracking or advertisements, for example) will have access to it - this also makes the URI
			a bit cleaner */
		if ( isset( \IPS\Request::i()->client_id ) )
		{
			$key = md5( uniqid() );
			\IPS\Request::i()->setCookie( 'oauth_authorize', $key );
			
			\IPS\Db::i()->insert( 'core_oauth_authorize_prompts', array(
				'session_id'	=> $key,
				'client_id'		=> $this->client->client_id,
				'response_type'	=> $this->responseType,
				'redirect_uri'	=> $this->providedRedirectUri ?: NULL,
				'scope'			=> implode( ' ', $this->scope ),
				'state'			=> $this->state,
				'timestamp'		=> time(),
				'logged_in'		=> FALSE
			), TRUE );
						
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'oauth/authorize/', 'interface' ) );
		}
		
		/* Construct the URL for this page */
		$url = \IPS\Http\Url::internal( 'oauth/authorize/', 'interface' );
		
		/* Get the scope definitions */
		$scopes = array();
		$availableScopes = json_decode( $this->client->scopes, TRUE );
		foreach ( $this->scope as $scope )
		{
			$scopes[ $scope ] = $availableScopes[ $scope ]['description'];
		}
				
		/* Do we need them to login? */
		if ( !\IPS\Member::loggedIn()->member_id or ( ( $this->client->prompt === 'login' or $requestedPromptType === 'login' ) and !$loggedIn ) )
		{
			$login = new \IPS\Login( $url );

			$member = NULL;
			$error = NULL;
			try
			{
				if ( $success = $login->authenticate() )
				{
					\IPS\Db::i()->update( 'core_oauth_authorize_prompts', array( 'logged_in' => TRUE ), array( 'session_id=?', \IPS\Request::i()->cookie['oauth_authorize'] ) );
					
					if ( $success->mfa() )
					{
						$_SESSION['processing2FA'] = array( 'memberId' => $success->member->member_id, 'anonymous' => $success->anonymous, 'remember' => $success->rememberMe, 'destination' => (string) $url, 'handler' => $success->handler->id );
						\IPS\Output::i()->redirect( \IPS\Http\Url::internal( "app=core&module=system&controller=login", 'front', 'login' )->setQueryString( '_mfaLogin', 1 ) );
					}
					$success->process();
					
					\IPS\Output::i()->redirect( $url );
				}
			}
			catch ( \IPS\Login\Exception $e )
			{
				if ( $e->getCode() === \IPS\Login\Exception::MERGE_SOCIAL_ACCOUNT )
				{
					$e->member = $e->member->member_id;
					$e->handler = $e->handler->id;
					$_SESSION['linkAccounts'] = json_encode( $e );
					
					\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=core&module=system&controller=login&do=link', 'front', 'login' )->setQueryString( 'ref', base64_encode( $url ) ) );
				}
				
				$error = $e->getMessage();
			}
			
			if ( $member === NULL )
			{
				\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'login', 'core', 'global' )->oauthLogin( $url, $this->client, $scopes, $login, $error );
				\IPS\Dispatcher::i()->finish();	
			}
		}

		/* Still here? Show an authorization screen */
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'login', 'core', 'global' )->oauthAuthorize( $url, $this->client, $scopes );
		\IPS\Dispatcher::i()->finish();			
	}
}

/* Init */
\IPS\Session\Front::i();
\IPS\Dispatcher\External::i();
\IPS\Output::i()->bodyClasses[] = 'ipsLayout_minimal';
\IPS\Output::i()->bodyClasses[] = 'ipsLayout_minimalNoHome';
\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'oauth_authorize', FALSE, array( 'sprintf' => array( \IPS\Settings::i()->board_name ) ) );
\IPS\Output::i()->httpHeaders['X-Frame-Options'] = 'DENY';

/* Check we are not IP banned */
$ipBanned = \IPS\Request::i()->ipAddressIsBanned();
if ( $ipBanned )
{
	\IPS\Output::i()->showBanned();
}

/* Handle the OAuth request */
try
{
	/* Get our params */
	$loggedIn = FALSE;
	if ( !isset( \IPS\Request::i()->client_id ) )
	{
		try
		{
			$row = \IPS\Db::i()->select( '*', 'core_oauth_authorize_prompts', array( 'session_id=?', \IPS\Request::i()->cookie['oauth_authorize'] ) )->first();
			$clientId = $row['client_id'];
			$responseType = $row['response_type'];
			$redirectUri = $row['redirect_uri'];
			$scope = $row['scope'];
			$state = $row['state'];
			$loggedIn = $row['logged_in'];
		}
		catch ( \UnderflowException $e )
		{
			throw new \IPS\Login\Handler\OAuth2\InitException('oauth_err_invalid_client');
		}
	}
	else
	{
		$clientId = \IPS\Request::i()->client_id;
		$responseType = \IPS\Request::i()->response_type;
		$redirectUri = \IPS\Request::i()->redirect_uri;
		$scope = \IPS\Request::i()->scope;
		$state = \IPS\Request::i()->state;
	}
	
	/* Init, validating client_id and redirect_uri */
	$request = oAuthServerAuthorizationRequest::init( $clientId, $redirectUri, $state );
	
	/* If site is offline, return temporarily_unavailable */
	if ( ( isset( \IPS\Settings::i()->setup_in_progress ) AND \IPS\Settings::i()->setup_in_progress ) or !\IPS\Settings::i()->site_online )
	{
		throw new \IPS\Login\Handler\OAuth2\Exception('temporarily_unavailable');
	}
	
	/* HTTPs only */
	if ( \IPS\OAUTH_REQUIRES_HTTPS and !\IPS\Request::i()->isSecure() )
	{
		throw new \IPS\Login\Handler\OAuth2\Exception( 'invalid_request', "request must be made with https" );
	}
	
	/* Set data */
	$request->setResponseType( $responseType );	
	if ( $scope )
	{
		$request->setScope( $scope );
	}
	
	/* Do we need them to be prompted? */
	$authorizedUrl = NULL;
	if ( $request->promptRequired( \IPS\Request::i()->prompt ) )
	{
		$request->prompt( \IPS\Request::i()->prompt, $loggedIn );
	}
	
	/* Still here? Go ahead */
	\IPS\Db::i()->delete( 'core_oauth_authorize_prompts', array( 'session_id=?', \IPS\Request::i()->cookie['oauth_authorize'] ) );
	\IPS\Request::i()->setCookie( 'oauth_authorize', NULL );
	\IPS\Output::i()->redirect( $request->authorized( \IPS\Member::loggedIn(), isset( \IPS\Request::i()->grantedScope ) ? array_keys( \IPS\Request::i()->grantedScope ) : array() ), NULL, 302 );
}
catch ( \IPS\Login\Handler\OAuth2\InitException $e )
{
	\IPS\Output::i()->error( $e->getMessage(), '2S361/2', 403 );
}
catch ( \IPS\Login\Handler\OAuth2\Exception $e )
{
	$response = array( 'error' => $e->getMessage() );
	if ( $e->description )
	{
		$response['error_description'] = $e->description;
	}
	\IPS\Output::i()->redirect( $request->redirect( $response ), NULL, 302 );
}
catch ( Exception $e )
{
	\IPS\Output::i()->redirect( $request->redirect( array( 'error' => 'server_error', 'error_description' => $e->getMessage() ) ), NULL, 302 );
}
