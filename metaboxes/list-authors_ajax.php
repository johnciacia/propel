<?php 

$user = mysql_real_escape_string(trim($_POST['user']));

require_once("../../../../wp-load.php");

if($user){

	$sql = "SELECT * FROM {$wpdb->prefix}users WHERE user_login LIKE '%$user%'";
	$users = $wpdb->get_results($sql, OBJECT);
					   
} else {
		 $users = get_users( array('orderby' => 'display_name', 
								   'order' => 'ASC' ) ); 
}
?>

<div id="propel_list_users" class="categorydiv">
	<ul id="propel_list_users-tabs" class="category-tabs">
		<li class="tabs">
			<a href="#propel_user-all">Add Users</a>
		</li>
	</ul>

	<div id="propel_user-all" class="tabs-panel" style="display: block; ">
		<ul id="propel_userschecklist" class="list:propel_category categorychecklist form-no-clear">

		<?php foreach($users as $user) : ?>
			<li id="propel_user-<?php esc_attr_e($user->ID); ?>" class="popular-category">
				<label class="selectit">
					<input value="<?php  esc_attr_e($user->user_login); ?>" type="checkbox" name="coauthors[]" id="in-propel_user-<?php echo $user->ID; ?>" <?php 
					if( propel_is_coauthor( $user->ID ) ) { echo "checked='checked' "; }
					if( propel_is_parent_coauthor( $user->ID ) ) { echo "disabled='disabled'"; }
					?>> <?php esc_html_e($user->display_name); ?>
					<?php
					if( propel_is_parent_coauthor( $user->ID ) ) {
					?>
						<input value="<?php  esc_attr_e($user->user_login); ?>" type="hidden" name="coauthors[]" />
					<?php
					}
					?>
				</label>
			</li>
		<?php endforeach; ?>				
		</ul>
	</div>
</div>

<?php
function get_coauthors( $post_id = 0, $args = array() ) {
global $post, $post_ID, $coauthors_plus, $wpdb;
$COAUTHOR_TAXONOMY = 'author';
$coauthors = array();
$post_id = (int)$post_id;
if( !$post_id && $post_ID ) $post_id = $post_ID;
if( !$post_id && $post ) $post_id = $post->ID;

$defaults = array( 'orderby' => 'term_order', 'order' => 'ASC' );
$args = wp_parse_args( $args, $defaults );

if($post_id) { 
	$coauthor_terms = wp_get_post_terms( $post_id, $COAUTHOR_TAXONOMY, $args );

	if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
		foreach($coauthor_terms as $coauthor) {
			$post_author =  get_user_by( 'login', $coauthor->name );
			// In case the user has been deleted while plugin was deactivated
			if(!empty($post_author)) $coauthors[] = $post_author;
		}
	} else {
		if($post) {
			$post_author = get_userdata($post->post_author);
		} else {
			$post_author = get_userdata($wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d", $post_id)));
		}
		if(!empty($post_author)) $coauthors[] = $post_author;
	}
}
return $coauthors;
}

wp_nonce_field( 'coauthors-edit', 'coauthors-nonce' );
function propel_is_coauthor( $user_id ) {
	$coauthors = get_coauthors();
	foreach($coauthors as $coauthor) {
		if($coauthor->ID == $user_id) return true;
	}
	return false;
}

function propel_is_parent_coauthor( $user_id ) {
	$post = get_post( get_the_ID() );
	if( $post->post_type != 'propel_task' || $post->post_parent == 0 ) return false;

	$coauthors = get_coauthors( $post->post_parent );
	foreach($coauthors as $coauthor) {
		if($coauthor->ID == $user_id) return true;
	}
	return false;
}