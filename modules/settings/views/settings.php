<h3>Theme Settings</h3>

<form method="POST">
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
	<input type="submit" />
</form>