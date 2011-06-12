<div id="picasso-general" class="wrap">
<?php screen_icon('options-general'); ?>
<h2>Propel</h2>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<form action="admin-post.php" method="POST">

	<?php if(get_option('PROPEL_INCLUDE_CSS') == true): ?>
		<input type="checkbox" name="propel_include_css" checked /> Include default CSS?<br /><br />
	<?php else: ?>
		<input type="checkbox" name="propel_include_css" /> Include default CSS?<br /><br />
	<?php endif; ?>
	
	<filedset>
		<?php 
		foreach($themes as $theme => $path) {
			if($path == get_option('propel_theme'))
				echo "<input type='radio' name='propel_theme' value='$path' checked> $theme<br />";
			else 
				echo "<input type='radio' name='propel_theme' value='$path'> $theme<br />";
		}
		
		?>
	</filedset>
	<br />
	<input type="hidden" name="action" value="propel-update-settings" />
	<input type="submit" />

</form>

</div>
</div>