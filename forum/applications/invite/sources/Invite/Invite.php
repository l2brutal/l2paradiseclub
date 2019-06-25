<?php

namespace IPS\invite;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

class _Invite
{
	/**
	 * Send the invitation via email
	 *
	 * @return	string
	 */
	public static function sendInvitation( $sendToEmail, $sendToName, $invitationCode, $fromEmail, $fromName, $date )
	{
		$inviter		= \IPS\Settings::i()->is_show_inviter_email ? \IPS\Member::loggedIn()->name : '';
		$mail 			= \IPS\Email::buildFromTemplate( 'invite', 'invite_system', array( $sendToName, $invitationCode, $date, $sendToEmail, $inviter ) );

		if( \IPS\Settings::i()->mail_method != 'smtp' )
		{
			$mail->from 	= $fromEmail;
			$mail->fromName = $fromName;
		}

		/* Set contact email as Reply-To instead of From, so that it will pass Spam/SPF checks */
		//$mail->headers['Reply-To'] = '=?UTF-8?B?' . base64_encode( $fromName ) . "?= <" . $fromEmail . '>';
		$mail->send( $sendToEmail );
	}

	public function dummy()
	{
		//nothing here
	}
}