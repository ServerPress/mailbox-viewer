<?php
/**
 * Plugin Name: Mailbox Viewer
 * Plugin URL: https://serverpress.com/plugins/mail-proxy
 * Description: Provides quick developer offline viewing of mail delivery services.
 * Version: 1.0.11
 * Author: Stephen Carnam
 * Author URI: http://steveorevo.com
 *
 * @package     DS3_Mailbox_Viewer
 */

/**
 * Adds the domain name of the site sending an email to the mail headers for display in mail viewer
 * @param PHPMailer $phpmailer The mailer instance to add SMTP headers to
 */
function ds_phpmailer_init( $phpmailer )
{
	$phpmailer->AddCustomHeader( 'X-WP-Domain: ' . parse_url( site_url(), PHP_URL_HOST ) );
}
add_action( 'phpmailer_init', 'ds_phpmailer_init');
