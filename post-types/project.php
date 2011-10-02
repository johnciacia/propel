<?php

/**
 * @todo: add custom post status "Archived" - archiving a project also archives the tasks associated with it
 * @todo: deleting a project deletes the tasks associated with it
 * @todo: add ability to filter start date, and end date
 * @todo: add metabox to add a task
 * @todo: verify that start, end, priority, and complete get sorted properly
 * @todo: add metabox for archived tasks
 * @todo: update tasks metabox
 */

Post_Type_Project::init();

class Post_Type_Project {
	
	const POST_TYPE = 'propel_project';

	/**
	 * @since 2.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'register_sortable_columns' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_columns', array( __CLASS__, 'register_columns' ) );
		add_filter( 'parse_query', array( __CLASS__, 'parse_query' ) );
	}

	/**
	 * @since 2.0
	 */
	public static function parse_query($query) {
		global $pagenow;
		if ( !isset( $_GET['post_type'] ) )
			return $query;

		if( $pagenow != "edit.php" && $_GET['post_type'] != self::POST_TYPE )
			return $query;

		if( isset( $_GET['client'] ) ) {
			$query->query_vars['meta_key'] = "_propel_owner";
			$query->query_vars['meta_value'] = $_GET['client'];
			$query->query_vars['post_type'] = "propel_project";
			return $query;
		}

	}

	/**
	 * @since 2.0
	 */
	public static function restrict_manage_posts() {
		echo "";
	}

	/**
	 * @since 2.0
	 */
	public static function register_post_type()  {
		$labels = array(
			'name' => _x( 'Categories', 'taxonomy general name' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Categories' ),
			'all_items' => __( 'All Categories' ),
			'parent_item' => __( 'Parent Category' ),
			'parent_item_colon' => __( 'Parent Category:' ),
			'edit_item' => __( 'Edit Category' ), 
			'update_item' => __( 'Update Category' ),
			'add_new_item' => __( 'Add New Category' ),
			'new_item_name' => __( 'New Category Name' ),
			'menu_name' => __( 'Categories' )); 	

		register_taxonomy('propel_category', 'propel_project', array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'propel_category' ),
		));

		unset($labels);
 		$labels = array(
			'name' => _x( 'Propel', 'post type general name' ),
    		'singular_name' => _x( 'Propel', 'post type singular name' ),
    		'add_new' => _x( 'Add New', 'project' ),
    		'add_new_item' => __( 'Add New Project' ),
    		'edit_item' => __( 'Edit Project' ),
    		'new_item' => __( 'New Project' ),
    		'all_items' => __( 'All Projects' ),
    		'view_item' => __( 'View Project' ),
    		'search_items' => __( 'Search Projects' ),
    		'not_found' =>  __( 'No projects found' ),
    		'not_found_in_trash' => __( 'No projects found in Trash' ), 
    		'parent_item_colon' => '',
    		'menu_name' => 'Propel'
    	);
    	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title','editor','comments', 'author', 'custom-fields', 'revisions' )
		);
		
		register_post_type( self::POST_TYPE, $args );

	}

	/**
	 * @since 2.0
	 */
	public static function admin_menu() {
		global $submenu;
		unset($submenu['edit.php?post_type=propel_project'][10]);
	}
	
	/**
	 * @since 2.0
	 * @see http://shibashake.com/wordpress-theme/add-custom-post-type-columns
	 */
	public static function register_columns($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x( 'Project Name', 'column name' );
		$new_columns['client'] = __( 'Client', 'propel' );
		$new_columns['author'] = __( 'Manager', 'propel' );
		$new_columns['start'] = __( 'Start Date', 'propel' );
		$new_columns['end'] = __( 'End Date', 'propel' );
		$new_columns['priority'] = __( 'Priority', 'propel' );
		$new_columns['complete'] = __( 'Progress', 'propel' );
		$new_columns['comments'] = $columns['comments'];
		return $new_columns;
	}

	/**
	 * @since 2.0
	 * @see http://scribu.net/wordpress/custom-sortable-columns.html
	 */
	public static function register_sortable_columns( $x ) {
		$columns['client'] = 'client';
		$columns['start'] = 'start';
		$columns['end'] = 'end';
		$columns['priority'] = 'priority';
		$columns['complete'] = 'complete';
		$columns['author'] = 'author';
		return $columns;
	}

	/**
	 * @since 2.0
	 */		 
	public static function manage_columns($column_name, $id) {
		global $wpdb;

		switch ($column_name) {
			case 'id':
				echo $id;
				break;

			case 'client':
				$id = get_post_meta($id, '_propel_owner', true);
				if(!$id) {
					echo "admin";
					break;
				}

				$user = get_userdata($id);
				if(!$user) {
					echo "admin";
					break;
				}

				echo "<a href='edit.php?post_type=propel_project&client=" . $user->ID . "'>" . $user->display_name . "</a>";
				break;

			case 'start':
				$date = get_post_meta( $id, '_propel_start_date', true );
				if($date) {
					echo date( "M. jS, Y" , $date );
				}
				break;

			case 'end':
				$date = get_post_meta( $id, '_propel_end_date', true );
				if($date) {
					echo date( "M. jS, Y" , $date );
				}
				break;

			case 'priority':
				echo get_post_meta( $id, '_propel_priority', true );
				break;

			case 'complete':
				echo "" . get_post_meta( $id, '_propel_complete', true ) . "%";
				break;

			default:
				break;
		}
	}

	/**
	 *
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'propel_project_meta', __('Project', 'propel' ),
			array( __CLASS__, 'edit_project_meta'), 'propel_project', 'side' );

		if( isset($_GET['action']) && $_GET['action'] == "edit" ) {
		add_meta_box('propel_project_tasks', __('Project Tasks', 'propel'),
			array( __CLASS__ , 'project_tasks'), 'propel_project', 'normal', 'high' );

		add_meta_box('propel_add_task', __('Add Task', 'propel'), array( __CLASS__, 'add_task' ), 'propel_project', 'side');
		}
	}

	/**
	 *
	 */
	public static function project_tasks() {
		$args = array(
			'order'=> 'ASC',
			'post_parent' => get_the_ID(),
			'post_status' => 'publish',
			'post_type' => 'propel_task'
		);

		$tasks = get_children( $args );

		require_once( __DIR__ .'/../metaboxes/tasks.php' );
	}

	/**
	 *
	 */
	public static function edit_project_meta() {
		wp_nonce_field( plugin_basename( __FILE__ ), 'propel_nonce' );

		$start = get_post_meta( get_the_ID(), '_propel_start_date', true );
		if($start)
			$start = date( "M. jS, Y", $start );

		$end = get_post_meta( get_the_ID(), '_propel_end_date', true );
		if($end)
			$end = date( "M. jS, Y", $end );

		$priority = get_post_meta( get_the_ID(), '_propel_priority', true );
		if(!$priority)
			$priority = 1;

		$complete = get_post_meta( get_the_ID(), '_propel_complete', true );
		if(!$complete)
			$complete = 0;

		$owner = get_post_meta( get_the_ID(), '_propel_owner', true );
		if(!$owner)
			$owner = 0;

		$users = get_users();

		require_once( __DIR__ .'/../metaboxes/project-meta.php' );
	}

	/**
	 *
	 */
	public static function save_post($post_id) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['propel_nonce'] ) )
			return;

		if ( !wp_verify_nonce( $_POST['propel_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
		}
		
		update_post_meta( $post_id, '_propel_start_date', strtotime( $_POST['start_date'] ) );
		update_post_meta( $post_id, '_propel_end_date', strtotime( $_POST['end_date'] ) );
		update_post_meta( $post_id, '_propel_priority', (int)$_POST['priority'] );
		update_post_meta( $post_id, '_propel_complete', (int)$_POST['complete'] );
		update_post_meta( $post_id, '_propel_owner', (int)$_POST['owner'] );

	}

	public static function add_task() {
		require_once( __DIR__ . '/../metaboxes/add-task.php' );	
	}

	/**
	 *
	 */
	public static function admin_footer() { ?>
		<script type="text/javascript">
		jQuery(document).ready(function() { 
			jQuery("input[name=start_date]").datepicker();
			jQuery("input[name=end_date]").datepicker();
		});
		</script>
	<?php
	}
}

?>