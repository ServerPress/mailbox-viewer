<?php
/**
 * Create a menu item within our localhost tools pull down menu.
 */
global $ds_runtime;
if ( !$ds_runtime->is_localhost )				// Not localhost
	return;
if ( FALSE !== $ds_runtime->last_ui_event )		// Not interested in events
	return;

/**
 * Add our menu to the localhost page.
 */
$ds_runtime->add_action( 'append_tools_menu', 'mailbox_viewer_tools_menu' );
function mailbox_viewer_tools_menu()
{
	echo '<li><a href="http://localhost/ds-plugins/mailbox-viewer/page.php">Mailbox Viewer</a></li>';
}

/**
 * Determines the location where DesktopServer places the email files
 * @return string The directory where email temp files are located
 */
function mailbox_viewer_temp_dir()
{
	global $ds_runtime;

	// 'xampp' is part of the directory name for versions < 3.9
	if ( FALSE !== stripos( __FILE__, 'xampp' ) ) {
		if ( 'Darwin' === PHP_OS ) {
			$temp_dir = '/Applications/XAMPP/xamppfiles/temp/mail';
		} else {
			$temp_dir = 'C:/xampplite/tmp/mail';
		}
	} else {
		// for 3.9+ use the environment variable
		$temp_dir = getenv( 'DS_RUNTIME' ) . '/temp/mail';
	}

	return $temp_dir;
}
