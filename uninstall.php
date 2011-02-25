<?php 
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();
	
function uninstall() {	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}projects");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tasks");
	delete_option( 'propel_theme' );
	delete_option( 'PROPEL_DBVERSION' );
}

uninstall();

?>