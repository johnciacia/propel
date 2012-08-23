<?php
/*
 * Ajax update for post meta preference as admin
 */

	require_once('../../../wp-load.php');
	
	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);
	update_post_meta( $static_id, '_propel_preference',"admin");

?>