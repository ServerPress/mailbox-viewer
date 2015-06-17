<?php
/**
 * Provide the email data back to our ajax client for the given filename
 */
global $ds_runtime;
if ( !$ds_runtime->is_localhost ) return;
include_once( 'class-mail-decoder.php' );
include_once( 'string.php' );
if ( PHP_OS === 'Darwin' ){
	$mail_folder = '/Applications/XAMPP/xamppfiles/temp/mail';
}else{
	$mail_folder = 'c:/xampplite/tmp/mail';
}

// Check if we're asked to delete all files.
if ( isset( $_GET['empty'] ) ) {
	$files = glob( $mail_folder . '/*.eml' );
	foreach( $files as $file ) {
		if( is_file($file) ) {
			unlink( $file );
		}
	}
	return;
}
$email = new MailDecoder( $mail_folder . '/' . $_GET['fn'] );
$result = [];
$result['html'] = rawurlencode($email->html);
$result['text'] = rawurlencode( str_replace( "\n", "<br/>", str_replace( '>', '&lt;', str_replace( '<', '&gt;', $email->text) ) ) );
$result['raw'] = rawurlencode( '<pre>' . str_replace( "\n", "<br/>", str_replace( '>', '&gt;', str_replace( '<', '&lt;', $email->raw) ) ) . '</pre>'  );
echo json_encode( $result );
