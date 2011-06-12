<div id="propel-rss-<?php echo $id; ?>">&nbsp;</div>

<script type="text/javascript">


var data = {
	action: 'propel-rss',
	feed: '<?php echo $feed; ?>'
};


jQuery.post(ajaxurl, data, function(response) {
	console.log(response);
	jQuery("#propel-rss-" + <?php echo $id; ?> + "").html(response);
	
});

</script>