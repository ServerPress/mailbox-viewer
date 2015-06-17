<?php
/**
 * Display our mail viewer page as a localhost extension
 */

global $ds_runtime;
if ( !$ds_runtime->is_localhost ) return;
$ds_runtime->add_action( 'ds_head', 'mailbox_viewer_head' );
function mailbox_viewer_head() {
	// Inject our css into the header stuff
	?>
	<link href="http://localhost/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/mailbox-viewer/css/jquery.splitter.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/mailbox-viewer/css/mailbox.css" rel="stylesheet"/>
<?php
}

include_once( $ds_runtime->htdocs_dir . '/header.php');
include_once( 'class-mail-decoder.php' );
include_once( 'string.php' );
?>
	<div class="container">
		<div class="btn-group btn-group-sm" role="group">
			<button type="button" class="btn btn-default" id="btn-refresh">Refresh</button>
		</div>
		<div class="btn-group btn-group-sm" role="group">
			<button type="button" class="btn btn-default" id="btn-empty">Empty All</button>
		</div>
		<p></p>
		<div id="mail">
			<div class="list">
				<table id="mail-list" class="table mailbox">
					<thead>
					<tr>
						<th id="mail-date">Date:</th>
						<th id="mail-to">To:</th>
						<th id="mail-from">From:</th>
						<th id="mail-subject">Subject:</th>
					</tr>
					</thead>
					<tbody>
					<?php
					/**
					 * List all email by date
					 */
					if ( PHP_OS === 'Darwin' ){
						$mail_folder = '/Applications/XAMPP/xamppfiles/temp/mail';
					}else{
						$mail_folder = 'c:/xampplite/tmp/mail';
					}
					if ( file_exists( $mail_folder ) ) {
						$dir = new DirectoryIterator( $mail_folder );
						$files = array();
						$n = 0;
						foreach ($dir as $fileInfo) {
							if ( $fileInfo->isDot() || $fileInfo->getFilename() === '.DS_Store' ) {
								continue;
							}
							if ( substr($fileInfo->getRealPath(), -4 ) === '.eml' ) {
								$files[ $fileInfo->getMTime() . ' - ' . sprintf( '%08d', $n ) ] = $fileInfo->getRealPath();
							}
							$n++;
						}
						krsort( $files );
						foreach ( $files as $date => $file ) {
							$md = new MailDecoder( $file, true );
							echo '<tr class="envelope" filename="' . basename( $file ) . '">';
							$d = date( "M d, Y g:i", $date );
							if ( date( 'a', $date ) === 'am' ) {
								$d .= 'a';
							}else{
								$d .= 'p';
							}
							echo '<td class="mail-date"> ' . $d . ' </td><td class="mail-to">' . $md->to . '</td>';
							echo '<td class="mail-from"> ' . $md->from . '</td><td class="mail-subject">' . $md->subject . '</td>';
							echo '</tr>';
						}
					}
					?>
					</tbody>
				</table>
			</div><!-- .list -->
			<div class="detail">
				<div class="tabbable">
					<div class="tab-content">
						<div id="html" class="tab-pane active">
						</div>
						<div id="text" class="tab-pane">
						</div>
						<div id="raw" class="tab-pane">
						</div>
					</div><!-- /.tab-content -->
				</div><!-- /.tabbable -->
			</div>
		</div><!-- #mail -->
		<div id="views" style="margin-top:22px;">
			<ul class="nav nav-pills">
				<li class="active" id="tab-html"><a href="#html" data-toggle="tab">HTML</a></li>
				<li><a href="#text" id="tab-text" data-toggle="tab">Text</a></li>
				<li><a href="#raw" id="tab-raw" data-toggle="tab">Raw</a></li>
			</ul>
		</div>
	</div>
<?php include_once( 'footer.php' );