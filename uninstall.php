<?php 
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

/**
 * @todo: prompt the user if they want to remove 
 * all the terms and custom posts
 */
function uninstall() {	
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}projects");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}tasks");
	delete_option( 'propel_theme' );
	delete_option( 'PROPEL_ERROR' );
	delete_option( 'PROPEL_DBVERSION' );
	delete_option( 'PROPEL_INCLUDE_CSS' );
	delete_option( 'propel_authors_version' );
}

uninstall();

?>