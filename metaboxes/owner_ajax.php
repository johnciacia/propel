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

				<select name="propel_post_author" id="propel_post_author">
					<?php foreach($users as $user) : ?>
						 <option value="<?php echo $user->ID; ?> "><?php esc_html_e($user->display_name); ?></option>
					<?php endforeach; ?>
				</select>
		</td>
<?php } else {
   echo "No User Found.";	
}
?>
