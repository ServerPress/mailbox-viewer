<footer class="footer">
	<div class="container">
		<p class="text-muted">You are running DesktopServer <?php echo $ds_runtime->preferences->edition; ?> edition version <?php echo $ds_runtime->preferences->version; ?></p>
		<?php $ds_runtime->do_action("ds_footer"); ?>
	</div>
</footer>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="http://localhost/js/jquery.min.js"></script>
<script src="http://localhost/js/bootstrap.min.js"></script>
<script src="http://localhost/js/jquery.tablesorter.js"></script>
<script src="http://localhost/ds-plugins/mailbox-viewer/js/jquery-ui.min.js"></script>
<script src="http://localhost/ds-plugins/mailbox-viewer/js/jquery.floatThead.min.js"></script>
<script src="http://localhost/ds-plugins/mailbox-viewer/js/jquery.splitter-0.15.0.js"></script>
<script>
	(function($){
		$(function(){
			$('#btn-refresh').click(function(){
				window.location = window.location;
			});
			$('#btn-empty').click(function(){
				$.get('http://localhost/ds-plugins/mailbox-viewer/detail.php?empty=1', function(r){
					window.location = window.location;
				});
			});
			$('#mail').css({height:300}).split({orientation: 'horizontal', limit: 10});
			$(window).resize(function(){
				$('#mail').css({height:( $(window).height() -190 )});
			}).resize();
			setTimeout(function(){
				$(window).resize();
			}, 1000);
			$('.detail').css({height:( $(window).height() -308)});
			$("table.mailbox").tablesorter({
			});
			var $table = $('table.mailbox');
			$table.floatThead({
				scrollContainer: function($table){
					return $table.closest('.list');
				}
			});
			$('tr.envelope').click(function(el){
				// adjust class to show which email is selected
				$('tr.envelope').removeClass('selected');
				$(el.currentTarget).addClass('selected');

				var fn = $(this).attr('filename');
				$.get('http://localhost/ds-plugins/mailbox-viewer/detail.php?fn='+fn, function(r){
					var m = jQuery.parseJSON(r);

					if (m.html !== ''){
						$('#html').html(unescape(m.html)); // unescape isn't like either of the decodeURI's
						$('#tab-html').show();
					}else{
						$('#tab-html').hide();
					}
					if (m.text !== ''){
						$('.detail.bottom_panel').scrollTop(0);
						$('#text').html(unescape(m.text));
						$('#tab-text').show();
					}else{
						$('#tab-text').hide();
					}
					if (m.raw !== ''){
						$('#raw').html(unescape(m.raw));
					}
					var tab = '#tab-html';
					if (m.html == ''){
						if (m.text !== ''){
							tab = '#tab-text';
						}else{
							tab = '#tab-raw';
						}
					}
					$(tab).click();
					$(tab + ' a').click();
				});
			});
		});
	})(jQuery);
</script>
</body>
</html>