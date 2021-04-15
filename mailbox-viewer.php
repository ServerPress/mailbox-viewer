<?php
/**
 * Plugin Name: Mailbox Viewer
 * Plugin URI: https://serverpress.com/plugins/mail-proxy
 * Description: Provides quick developer offline viewing of mail delivery services.
 * Version: 1.0.15
 * Author: Stephen Carnam
 * Text Domain: mailbox-viewer
 * Author URI: http://steveorevo.com
 *
 * @package     DS3_Mailbox_Viewer
 */

if ( FALSE === stripos( __DIR__, 'ds-plugins' ) ) {
	// detect if not in the ds-plugins folder
	if ( is_admin() )
		add_action( 'admin_notices', 'mailbox_viewer_install_message' );
	return;		// do not initialize the rest of the plugin
}

/**
 * Display admin notification to install plugin in correct directory
 */
function mailbox_viewer_install_message()
{
	if ( 'Darwin' === PHP_OS )
		$correct_dir = '/Applications/XAMPP/ds-plugins/';		// mac directory
	else
		$correct_dir = 'C:\\xampplite\\ds-plugins\\';			// Windows directory

	echo '<div class="notice notice-error">',
		'<p>',
		sprintf( __('<b>Notice:</b> The Mailbox Viewer plugin needs to be installed in Desktop Server\'s ds-plugins directory.<br/>Please install in %1$smailbox-viewer', 'mailbox-viewer' ),
			$correct_dir),
		'</p>',
		'</div>';
}


/**
 * Adds the localized date and domain name of the site sending an email to the mail headers for display in mail viewer
 * @param PHPMailer $phpmailer The mailer instance to add SMTP headers to
 */
function ds_phpmailer_init( $phpmailer )
{
	$phpmailer->MessageDate = date( 'r', current_time( 'timestamp' ) );
	$phpmailer->AddCustomHeader( 'X-WP-Domain: ' . parse_url( site_url(), PHP_URL_HOST ) );
}

// only initialize when running under DesktopServer and localhost #16
if ( defined( 'DESKTOPSERVER' ) && ( isset( $_SERVER['REMOTE_ADDR'] ) && '127.0.0.1' === $_SERVER['REMOTE_ADDR'] ) )
	add_action( 'phpmailer_init', 'ds_phpmailer_init');
