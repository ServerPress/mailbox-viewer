<?php
/**
 * Create a menu item within our localhost tools pull down menu.
 */
global $ds_runtime;
if ( !$ds_runtime->is_localhost ) return; // Not localhost
if ( $ds_runtime->last_ui_event !== false ) return; // Not interested in events

/**
 * Add our menu to the localhost page.
 */
$ds_runtime->add_action( 'append_tools_menu', 'mailbox_viewer_tools_menu' );
function mailbox_viewer_tools_menu() {
	echo '<li><a href="http://localhost/ds-plugins/mailbox-viewer/page.php">Mailbox Viewer</a></li>';
}