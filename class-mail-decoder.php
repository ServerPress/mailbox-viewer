<?php

/**
 * The DesktopServer Mail Decoder class expects an email filename to decode and
 * parse. The various properties furnish the data for consumption/rendering.
 *
 * @author Stephen J Carnam
 * @package DesktopServer
 * @uses String class
 * @since 4.0.0
 */

class MailDecoder {
	public $attachments = [];
	public $subject = "";
	public $from = "";
	public $html = "";
	public $text = "";
	public $raw = "";
	public $to = "";
	public $cc = "";

	/**
	 * Accept a filename to parse on creation.
	 *
	 * @param $filename String containing a complete path to the email file.
	 */
	function __construct( $filename = '', $label_only = false ) {
		if ( $filename !== '' ) {
			$this->parse( $filename, $label_only );
		}
	}

	/**
	 * Parse the given email file.
	 *
	 * @param $filename String containing a complete path the email file.
	 * @param $label_only Boolean determines if we should omit parsing html/text body and attachments.
	 */
	function parse( $filename, $label_only = false ) {

		// Obtain the file contents if present and valid.
		if ( ! file_exists( $filename ) ) return;
		$this->raw = file_get_contents( $filename );
		$this->raw = str_replace( "\r\n", "\n", $this->raw );
		$email = new String( $this->raw );

		// Get the sender/destination address label, subject, etc.
		$label = new String();
		while( strpos( $email, 'Content-Type: ') !== false ) {
			$label = $label->concat( $email->getLeftMost( 'Content-Type: ' ) );
			$email = $email->delLeftMost( 'Content-Type: ');
			if ( ( $label->contains( 'From: ') ) && ( $label->contains('To: ') ) ) {
				break;
			}
		}
		$email = new String( 'Content-Type: ' . $email );
		if ( $label->contains( 'Subject: ') ) {
			$this->subject = $label->delLeftMost( 'Subject: ' )->getLeftMost( "\n" )->__toString();
		}
		$this->from = $label->delLeftMost( 'From: ' )->getLeftMost( "\n" )->__toString();
		$this->to = $label->delLeftMost( 'To: ' )->getLeftMost( ':' )->delRightMost( "\n" )->__toString();
		if ( $label->contains( 'Cc:') ) {
			$this->cc = $label->delLeftMost( 'Cc: ' )->getLeftMost( ':' )->delRightMost( "\n" )->__toString();
		}
		if ( $label_only ) return;

		// Get the text/plain email
		if ( $email->contains( 'Content-Type: text/plain;' ) ) {
			$text = $email->delLeftMost( 'Content-Type: text/plain;' )->delLeftMost( "\n\n" );
			$text = $text->getLeftMost( 'Content-Type: ')->delRightMost( "\n--" );
			$encode = $email->getLeftMost( 'Content-Type: text/plain;' )->getRightMost( "\n--" );
			$encode = $encode->concat( $email->delLeftMost( 'Content-Type: text/plain;' )->getLeftMost( "\n\n" ) );
			$text = $text->__toString();
			if ( $encode->contains( 'quoted-printable') ) {
				$text = quoted_printable_decode( $text );
			}
			if ( $encode->contains( 'base64') ) {
				$text = base64_decode( $text );
			}
			if ( $encode->contains( 'iso-8859-1' ) ) {
				$text = utf8_encode( $text );
			}
			$this->text = $text;
		}

		// Get the text/html email
		if ( $email->contains( 'Content-Type: text/html;' ) ) {
			$html = $email->delLeftMost( 'Content-Type: text/html;' )->delLeftMost( "\n\n" );
			$html = $html->getLeftMost( 'Content-Type: ')->delRightMost( "\n--" )->delRightMost(">")->concat(">");
			$html = quoted_printable_decode( $html );
			$this->html = $html;
		}


		// Get all base64 encodes
		$base64_data = [];
		$base64_type = [];
		$base64_name = [];
		$email = $email->replace( 'Content-Id: <', 'Content-ID: <');
		while ( strpos( $email, 'Content-Transfer-Encoding: base64') !== false ) {
			$base64 = $email->getLeftMost( 'Content-Transfer-Encoding: base64' )->getRightMost( "\n--" );
			$email = $email->delLeftMost( 'Content-Transfer-Encoding: base64' );
			$base64 = $base64->concat( 'Content-Transfer-Encoding: base64' );
			$base64 = $base64->concat( $email->getLeftMost( "\n--" ) );
			$email = $email->delLeftMost( "\n--" );
			if ( !$base64->contains( 'Content-ID: <' ) ) {
				continue;
			}
			$name = $base64->delLeftMost( 'Content-ID: <' )->getLeftMost( ">\n" );
			$type = $base64->getLeftMost( "\n\n" )->delLeftMost( "Content-Type: " )->getLeftMost( ";" );
			$data = $base64->delLeftMost( "\n\n" );
			array_push( $base64_data, $data );
			array_push( $base64_type, $type );
			array_push( $base64_name, $name );
		}

		// Embed images in html mail or keep as attachment
		if ( $this->html !== '' ) {
			for ( $n = 0; $n < count( $base64_name ); $n ++ ) {
				$find = ' src="cid:' . $base64_name[ $n ] . '"';
				$data = ' src="data:' . $base64_type[ $n ] . ';base64,' . $base64_data[ $n ] . '"';
				if ( strpos( $this->html, $find ) !== false ) {
					$this->html = str_replace( $find, $data, $this->html );
				}else{
					$data = 'Name: ' . $base64_name[ $n ] . ';Content-Type: ' . $base64_type[ $n ] . ';' . $base64_data[ $n ];
					array_push( $this->attachments, $data  );
				}
			}
		}
	}
}