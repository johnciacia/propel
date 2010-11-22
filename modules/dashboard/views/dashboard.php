<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
	<div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes($this->pagehook, 'side', $data); ?>
	</div>
	
	<div id="post-body" class="has-sidebar">
		<div id="post-body-content" class="has-sidebar-content">
			<?php do_meta_boxes($this->pagehook, 'normal', $data); ?>
			<?php do_meta_boxes($this->pagehook, 'additional', $data); ?>
		</div>
	</div>
	
	<br class="clear"/>
			
</div>	

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready( function($) {
	// close postboxes that should be closed
	$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	// postboxes setup
	postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
});
//]]>
</script>