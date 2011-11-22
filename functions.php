<?php

class Propel_Functions {
	
	var $args = array();
	var $post_type;
	var $post;
	var $action;
	var $status;
	var $cb;
	public static function register_post_status( $status, $args ) {
		register_post_status( $status );

		$functions = new Propel_Functions();
		$functions->status = $status;
		$functions->args = $args;
		add_filter( 'parse_query', array( $functions, 'parse_query' ) );
		add_action( 'admin_footer', array( $functions, 'admin_footer' ) );
	}

	/**
	 * $args['post_type']
	 * $args['action']
	 */
	public static function add_post_action( $args, $cb ) {
		if( isset($_GET['post_type']) && $_GET['post_type'] != $args['post_type']) return;

		$functions = new Propel_Functions();
		$functions->args = $args;
		$functions->args['cb'] = $cb;

		add_action( 'admin_footer', array( $functions, 'admin_footer_action' ) );
		add_filter( 'post_row_actions', array( $functions, 'post_row_actions' ) );
		add_action( 'admin_action_' . $args['action'], array( $functions, 'do_action' ) );
	}

	/**
	 * @todo verify that the current user can perform said action
	 */
	public function do_action() {

		if( is_array( $_REQUEST['post'] ) ) {
			foreach( $_REQUEST['post'] as $post => $post_id) {
				call_user_func($this->args['cb'], $post_id);	
			}
		} else {
			call_user_func($this->args['cb'], $_GET['post']);
		}

		wp_redirect( $_SERVER['HTTP_REFERER'] );
		die();
	}

	public function post_row_actions( $actions ) {
		if( !isset($_GET['post_type']) || $_GET['post_type'] != $this->args['post_type']) return $actions;
		$actions[$this->args['action']] = "<a href='post.php?post=" . get_the_ID() . "&action=" . $this->args['action'] . "'>" . $this->args['label'] . "</a>";
		return $actions;
	}

	public function admin_footer_action() {
		if( !isset($_GET['post_type']) || $_GET['post_type'] != $this->args['post_type']) return;
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('<option>').val("<?php echo $this->args['action']; ?>").text("<?php echo $this->args['label']; ?>").appendTo("select[name='action']");
				jQuery('<option>').val("<?php echo $this->args['action']; ?>").text("<?php echo $this->args['label']; ?>").appendTo("select[name='action2']");
			});
		</script>
		<?php
	}


	/**
	 * @since 2.0
	 */
	public function parse_query($query) {
		global $pagenow;
		if ( !isset( $_GET['post_type'] ) )
			return $query;

		if( $pagenow != "edit.php" && $_GET['post_type'] != $this->args['post_type'] )
			return $query;

		if( isset($_GET['post_status'] ) && $_GET['post_status'] == $this->status ) {
			$query->query_vars['post_type'] = $this->args['post_type'];
			$query->query_vars['post_status'] = $this->status;
		}
	}

	/**
	 * JavaScript hacks to add custom bulk action and custom post status 
	 * @since 2.0
	 */
	public function admin_footer() {
		global $wpdb;

		if(isset($_GET['post'])) :
			$post = get_post($_GET['post']);
			if( $post->post_type == $this->args['post_type']) :
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('<option>').val("<?php echo $this->status; ?>").text("<?php echo $this->args['label']; ?>").appendTo("#post_status");
					<?php if( get_post_status( get_the_ID() ) == $this->status) : ?>
					$("label[for='post_status']").html("Status: <strong><?php echo $this->args['label']; ?></strong>");
					$("#save-post").val("Save <?php echo $this->args['label']; ?>");
					$('#post_status').val("<?php echo $this->status; ?>")
					<?php endif; ?>
				});
			</script>
			<?php
			endif;
		endif;

		if(isset($_GET['post_type']) && $_GET['post_type'] != $this->args['post_type']) return;
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '".$this->args['post_type']."' && post_status = '$this->status';" ) );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("<li>").html(" | <a href='edit.php?post_status=" 
						+ "<?php echo $this->status ?>"
						+ "&post_type="
						+ "<?php echo $this->args['post_type']; ?>'>" 
						+ "<?php echo $this->args['label'] ?>"
						+ "  <span class='count'>(" 
						+ "<?php echo $count; ?>"
						+ ")</span></a>").appendTo('.subsubsub')
			});
		</script>
		<?php
	}
	
}



/* DASHBOARD METABOXES */
/* DASHBOARD METABOXES */

/* YOUR TASKS */
function dashboard_tasks_assigned_to_you_metabox() {

	function dashboard_tasks_assigned_to_you_content() {
?>
		
		<ol>
			<?php
			global $post;
			$current_user = wp_get_current_user();
			$args = array( 'post_type' => 'propel_task', 'numberposts' => 5, 'orderby' => 'post_date', 'author' => $current_user->ID );
			$myposts = get_posts( $args );
			foreach( $myposts as $post ) :	setup_postdata($post);
			?>
				<li>
					<p style="padding: 0; margin: 0;"><b><a href="post.php?post=<?php echo $post->post_parent; ?>&action=edit"><?php echo get_the_title($post->post_parent) ?></a></b> <a href="post.php?post=<?php echo get_the_ID(); ?>&action=edit"><?php the_title(); ?></a></p>
					<p style="padding: 0; margin: 0; font-size: 11px; color: #999; font-style: italic;">Contributors: <?php coauthors_nicknames(); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
		
		<p style="text-align: right;"><a href="edit.php?post_type=propel_task" style="color: orange; font-size 16px; font-weight: bold;">View All Tasks >></a></p>

<?php
	}
	
	wp_add_dashboard_widget( 'dashboard_tasks_assigned_to_you_content', __( 'Your Tasks' ), 'dashboard_tasks_assigned_to_you_content' );

}

add_action('wp_dashboard_setup', 'dashboard_tasks_assigned_to_you_metabox');


/* YOUR PROJECTS */
function dashboard_projects_assigned_to_you_metabox() {

	function dashboard_projects_assigned_to_you_content() {

		$current_user = wp_get_current_user();
?>
		
		<ol>
			<?php
			global $post;
			$current_user = wp_get_current_user();
			$args = array( 'post_type' => 'propel_project', 'numberposts' => 5, 'order' => DESC, 'orderby' => 'post_date', 'author' => $current_user->ID );
			/* $args = array( 'post_type' => 'propel_project', 'numberposts' => 100, 'order' => DESC, 'orderby' => 'post_date', 'meta_key' => '_coauthor', 'meta_value' => $current_user->ID ); */
			$myposts = get_posts( $args );
		    $user_id = WP_CRM_F::get_first_value($object['ID']);
		    
			foreach( $myposts as $post ) :	setup_postdata($post);
			?>
			
			<?php
				$user_id = get_post_meta($post->ID, '_propel_owner', true);
				$user = get_userdata($user_id);
			?>
				<li>
					<p style="padding: 0; margin: 0;"><b><a href="admin.php?page=wp_crm_add_new&user_id=<?php echo $user_id; ?>"><?php echo $user->user_login; ?></a></b> <a href="post.php?post=<?php echo get_the_ID(); ?>&action=edit"><?php the_title(); ?></a></p>
					<p style="padding: 0; margin: 0; font-size: 11px; color: #999; font-style: italic;">Contributors: <?php coauthors_nicknames(); ?></p>
					<p style="padding: 0; margin: 0; font-size: 11px; color: #999; font-style: italic;"><?php if($user_id) { WP_CRM_F::get_user_activity_stream("user_id={$user_id} "); } ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
		
		<p style="text-align: right;"><a href="edit.php?post_type=propel_project" style="color: orange; font-size 16px; font-weight: bold;">View All Projects >></a></p>

<?php
	}
	
	wp_add_dashboard_widget( 'dashboard_projects_assigned_to_you_content', __( 'Your Projects' ), 'dashboard_projects_assigned_to_you_content' );

}
add_action('wp_dashboard_setup', 'dashboard_projects_assigned_to_you_metabox');


/* ADD TASK */
function dashboard_add_tasks_metabox() {

	function dashboard_add_tasks_content() {

		require_once( __DIR__ . '/metaboxes/add-task.php' );
	}
	
	wp_add_dashboard_widget( 'dashboard_add_tasks_content', __( 'Add A New Task' ), 'dashboard_add_tasks_content' );

}
add_action('wp_dashboard_setup', 'dashboard_add_tasks_metabox');

/* ADDS DRAG AND DROP SORTING TO PROPEL */
wp_enqueue_script( 'simple-page-ordering', plugin_dir_url( __FILE__ ) . '/js/simple-page-ordering-for-propel.js', array('jquery-ui-sortable'), '0.9.7', true );
?>