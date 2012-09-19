<?php
/* this will be an ajax search for the Owners / Authors module
 */
require_once("../../../../wp-load.php");
    $user = mysql_real_escape_string(trim($_POST['user']));
	if($user){
			$sql = "SELECT * FROM {$wpdb->prefix}users WHERE user_login LIKE '%$user%'";
			$users = $wpdb->get_results($sql, OBJECT);
	} else {
			$sql = "SELECT * FROM {$wpdb->prefix}users";
			$users = $wpdb->get_results($sql, OBJECT);
	}
?>
<?php if ($users){ ?>
        <td>Select Owner</td>
		<td>

				<div id="propel_post_author_select">
					<?php foreach($users as $user) : ?>
						 <div class="search_itm" style="width:100%" id="<?php echo $user->ID; ?>">&nbsp;&nbsp;<?php esc_html_e($user->display_name); ?></div>
					<?php endforeach; ?>
				</div>
		</td>
<?php } else {
   echo "No User Found.";	
}
?>
