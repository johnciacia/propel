<?php


add_action( 'propel_deprecated_options', 'propel_deprecated_options' );
function propel_deprecated_options( $options ) {
	echo '<p><input name="propel_options[theme]" id="theme" type="text" value="'.$options['theme'].'" /> (default: <code>' . WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css</code>)</p>';
	echo '<br />';
}
?>