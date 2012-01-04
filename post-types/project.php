<?php

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
		add_action( 'wp_ajax_add_task', array( __CLASS__, 'wp_ajax_add_task' ) );
		add_action( 'load-post.php', array( __CLASS__, 'post' ) );
		add_filter( 'request', array( __CLASS__, 'request' ) );
	}

	/**
	 *
	 */
	 public static function request($vars) {
		if ( isset( $vars['orderby'] ) && 'priority' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_propel_priority',
				'orderby' => 'meta_value_num') );
		}

		if ( isset( $vars['orderby'] ) && 'complete' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_propel_complete',
				'orderby' => 'meta_value_num') );
		}

		if ( isset( $vars['orderby'] ) && 'start' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_propel_start_date',
				'orderby' => 'meta_value_num') );
		}

		if ( isset( $vars['orderby'] ) && 'end' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_propel_end_date',
				'orderby' => 'meta_value_num') );
		}

		if ( isset( $vars['orderby'] ) && 'client' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_propel_owner',
				'orderby' => 'meta_value_num') );
		}
		return $vars;
	 }

	/**
	 *
	 */
	 public static function post() {
		
		if( isset($_GET['_wpnonce'], $_GET['action'], $_GET['post'] ) && $_GET['action'] == "propel-delete" ) {
			if ( !wp_verify_nonce($_GET['_wpnonce'], 'propel-trash') ) die('Security check');

			wp_delete_post($_GET['post']);
			wp_redirect( $_SERVER['HTTP_REFERER'] );
			die();
		}
		
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
    		'menu_icon' => WP_PLUGIN_URL . '/propel/images/icon.png',
			'supports' => array( 'title','editor','comments', 'author', 'custom-fields', 'revisions' )
		);
		
		register_post_type( self::POST_TYPE, $args );

		$argz = array(
			'post_type' => 'propel_project',
			'action' => 'complete',
			'label' => 'Complete' );
		Propel_Functions::add_post_action( $argz, array( __CLASS__, 'action_complete' ) );

	}

	/**
	 * @since 2.0
	 */
	public static function action_complete( $post_id ) {
		$end = get_post_meta( $post_id, '_propel_end_date', true);
		if( !$end && empty( $_POST['end_date'] ) ) {
			update_post_meta( $post_id, '_propel_end_date', time() );	
		}
		update_post_meta( $post_id, '_propel_complete', 100 );

		$tasks = get_children( "post_parent=$post_id" );
		foreach( $tasks as $task ) {
			$end = get_post_meta( $task->ID, '_propel_end_date', true);
			if( !$end && empty( $_POST['end_date'] ) ) {
				update_post_meta( $task->ID, '_propel_end_date', time() );	
			}
			update_post_meta( $task->ID, '_propel_complete', 100 );
		}
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
	 */
	public static function register_columns($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x( 'Project Name', 'column name' );
		if( Propel_Options::option('show_client' ) )
			$new_columns['client'] = __( 'Client', 'propel' );
		$new_columns['author'] = __( 'Manager', 'propel' );
		if( Propel_Options::option('show_start_date' ) )
			$new_columns['start'] = __( 'Start Date', 'propel' );
		if( Propel_Options::option('show_end_date' ) )
			$new_columns['end'] = __( 'End Date', 'propel' );
		$new_columns['priority'] = __( 'Priority', 'propel' );
		if( Propel_Options::option('show_progress' ) )
			$new_columns['complete'] = __( 'Progress', 'propel' );
		$new_columns['comments'] = $columns['comments'];
		return $new_columns;
	}

	/**
	 * @since 2.0
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
				$priorities = propel_get_priorities();
				echo $priorities[get_post_meta( $id, '_propel_priority', true )];
				break;

			case 'complete':
				echo get_post_meta( $id, '_propel_complete', true ) . "%";
				break;

			default:
				break;
		}
	}

	/**
	 * @since 2.0
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'propel_project_meta', __('Project', 'propel' ),
			array( __CLASS__, 'edit_project_meta'), 'propel_project', 'side' );

		if( isset($_GET['action']) && $_GET['action'] == "edit" ) {
			add_meta_box('propel_project_tasks', __('Project Tasks', 'propel'),	array( __CLASS__, 'project_tasks'), 'propel_project', 'normal', 'high', 10, 2 );
			add_meta_box('propel_completed_tasks', __('Completed Tasks', 'propel'), array( __CLASS__, 'completed_tasks'), 'propel_project', 'normal', 'high', 10, 2 );
			add_meta_box('propel_add_task', __('Add Task', 'propel'), array( __CLASS__, 'add_task' ), 'propel_project', 'side');
		}
	}

	/**
	 *
	 */
	public static function completed_tasks( $post, $id ) {
		global $wpdb;
		$parent = get_the_ID(); 
		//@todo: profile query / use WP_Query
		$query = "SELECT `post_id`, `meta_value` AS `progress` 
		    	FROM `{$wpdb->postmeta}`
		        WHERE `meta_key` = '_propel_complete' 
		        AND `meta_value` = 100 AND `{$wpdb->postmeta}`.`post_id` 
		        	IN (SELECT `ID` FROM {$wpdb->posts}
		        	WHERE `post_parent`={$parent} AND `post_status` = 'publish')
		        ORDER BY `meta_value` DESC, `post_id` DESC;";

		$posts = $wpdb->get_results($query);
		require( dirname(__FILE__) . '/../metaboxes/tasks.php');
	}

	/**
	 * @since 2.0
	 */
	public static function project_tasks( $post, $id ) {
		global $wpdb;
		$parent = get_the_ID(); 
		//@todo: profile query / use WP_Query?
		$query = "SELECT `post_id`, `meta_value` AS `progress` 
		    	FROM `{$wpdb->postmeta}`
		        WHERE `meta_key` = '_propel_complete' 
		        AND `meta_value` < 100 AND `{$wpdb->postmeta}`.`post_id` 
		        	IN (SELECT `ID` FROM {$wpdb->posts}
		        	WHERE `post_parent`={$parent} AND `post_status` = 'publish')
		        ORDER BY `meta_value` DESC, `post_id` DESC;";

		$posts = $wpdb->get_results($query);
		require( dirname(__FILE__) . '/../metaboxes/tasks.php' );
	}

	/**
	 * @since 2.0
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

		require_once( dirname(__FILE__) . '/../metaboxes/project-meta.php' );
	}

	/**
	 * @since 2.0
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

		if( !isset( $_POST['complete'] ) )
			$_POST['complete'] = ''; //@todo: probably shouldn't set the value of a superglobal like this
		
		$start = !empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : time();
		update_post_meta( $post_id, '_propel_start_date', $start );

		$end = strtotime($_POST['end_date']);
		if( empty( $_POST['end_date'] ) && $_POST['complete'] == 100  ) {
			$end = time();
		}
		update_post_meta( $post_id, '_propel_end_date', $end );
		update_post_meta( $post_id, '_propel_priority', (int)$_POST['priority'] );
		update_post_meta( $post_id, '_propel_complete', (int)$_POST['complete'] );
		update_post_meta( $post_id, '_propel_owner', (int)$_POST['owner'] );

	}

	/**
	 * @since 2.0
	 */
	public static function add_task() {
		require_once( dirname(__FILE__) . '/../metaboxes/add-task.php' );	
	}

	/**
	 * @since 2.0
	 */
	public static function wp_ajax_add_task() {

		check_ajax_referer( 'add-task', 'security' );
		$post = array(
			'post_title' => $_POST['title'],
			'post_content' => $_POST['description'],
			'post_parent' => $_POST['parent'],
			'post_author' => $_POST['user'],
			'post_type' => 'propel_task',
			'post_status' => 'publish'
		);

		$id = wp_insert_post( $post );
		if( !$id ) die(0);

		update_post_meta( $id, '_propel_start_date', time() );
		update_post_meta( $id, '_propel_end_date', strtotime( $_POST['end_date'] ) );
		update_post_meta( $id, '_propel_complete', 0 );
		update_post_meta( $id, '_propel_priority', $_POST['priority'] );
		do_action( 'post_wp_ajax_add_task', $id );
		die($id);
	}

	/**
	 * @since 2.0
	 */
	public static function admin_footer() { ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) { 
			$(".date").datepicker();
			$("#add-task").click(function() {
				var data = {
						action: 'add_task',
						security: '<?php echo wp_create_nonce( "add-task" ); ?>',
						parent: '<?php echo get_the_ID(); ?>',
						title: $('input[name=task_title]').val(),
						description: $('textarea[name=task_description]').val(),
						end_date: $('input[name=task_end_date]').val(),
						priority: $('select[name=task_priority]').val(),
						user: $('#task_author option:selected').val()
				};

				jQuery.post(ajaxurl, data, function(response) {
					location.reload();
				});
			});
		});
		</script>
	<?php
	}
}

?>