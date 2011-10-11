<?php
/**
 * @todo: add filtering for client
 * @todo: add filtering for project
 * @todo: add filtering for task
 * @todo: add filtering for post_status = billed
 * @todo: when start button is pressed, time does not show up until another refresh
 * @todo: javascript duration counter?
 */

Post_Type_Time::init();

class Post_Type_Time {
	
	const POST_TYPE = 'propel_time';

	/**
	 * @since 2.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		//add_action( 'admin_action_bill', array( __CLASS__, 'bill' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'load-edit.php', array( __CLASS__, 'onload' ) );
		add_filter( 'bulk_actions-' . self::POST_TYPE , array( __CLASS__, 'bulk_actions' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'register_sortable_columns' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_columns', array( __CLASS__, 'register_columns' ) );
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
	public static function onload() {
		if( !isset( $_GET['post_type'], $_REQUEST['action'] ) ) return;

		if( $_GET['post_type'] == 'propel_time' && $_GET['start'] == 1 ) {
			if( !get_post_meta( $_GET['id'], '_propel_time_start', true ) )
				update_post_meta( $_GET['id'], '_propel_time_start', time() );
		}

		if( $_GET['post_type'] == 'propel_time' && $_GET['end'] == 1 ) {
			if( !get_post_meta( $_GET['id'], '_propel_time_end', true ) )
				update_post_meta( $_GET['id'], '_propel_time_end', time() );
		}
	}

	/**
	 * Add a custom bulk action
	 * @todo Use the bulk_actions* filter when it gets implemented
	 * @see http://core.trac.wordpress.org/ticket/16031
	 * @since 2.0
	 * @param $actions
	 * @return $actions
	 */
	public static function bulk_actions($actions){
        $actions['create_invoice'] = __('Bill');
        return $actions;
    }

    /**
     * Register propel_time post type
	 * @since 2.0
     */
	public static function register_post_type()  {

 		$labels = array(
			'name' => _x('Time', 'post type general name'),
    		'singular_name' => _x('Time', 'post type singular name'),
    		'add_new' => _x('Add New', 'time'),
    		'add_new_item' => __('Add New Time'),
    		'edit_item' => __('Edit Time'),
    		'new_item' => __('New Time'),
    		'all_items' => __('All Time'),
    		'view_item' => __('View Time'),
    		'search_items' => __('Search Time'),
    		'not_found' =>  __('No time found'),
    		'not_found_in_trash' => __('No time found in Trash'), 
    		'parent_item_colon' => 'Propel',
    		'menu_name' => 'Time');

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => 'edit.php?post_type=propel_project', 
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('')); 
		
		register_post_type(self::POST_TYPE, $args );

		$argv = array(
			'label' => "Billed",
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'post_type' => 'propel_time' );

		Propel_Functions::register_post_status( 'billed', $argv );

		$argz = array(
			'post_type' => 'propel_time',
			'action' => 'bill',
			'label' => 'Bill' );
		Propel_Functions::add_post_action( $argz, array( __CLASS__, 'bill' ) );
	}


	/**
	 * Add metaboxes
	 * @since 2.0
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'edit_task_meta', __( 'Time', 'propel' ),
			array( __CLASS__, 'edit_task_meta'), 'propel_time', 'normal', 'high' );
	}

	/**
	 * @since 2.0
	 * @param $post
	 */
	public static function edit_task_meta( $post ) {
		wp_nonce_field( plugin_basename( __FILE__ ), 'propel_nonce' );

		$projects = query_posts( 'post_type=propel_project&post_status=publish' );
		echo "<select name='parent_id' id='parent_id'>";
		foreach($projects as $project) {
			echo "<option value='".$project->ID."'>" . $project->post_title . "</option>";
			/** 
			 * @todo: optimize query
			 */ 
			$tasks = query_posts( 'post_type=propel_task&post_status=publish&post_parent=' . $project->ID );
			foreach($tasks as $task) {
				echo "<option value='".$task->ID."'>&nbsp&nbsp- " . $task->post_title . "</option>";
			}
		}
		echo "</select>";
		echo "<input type='hidden' name='post_title' value='foo' />";

		wp_reset_query();
	}

	/**
	 * Add custom columns in the table
	 * @since 2.0
	 * @param $columns
	 * @return $new_columns
	 */
	public static function register_columns( $columns ) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = __('Title', 'propel');
		$new_columns['action'] = __('Action', 'propel');
		$new_columns['client'] = __('Client', 'propel');
		$new_columns['project'] = __('Project', 'propel');
		$new_columns['task'] = __('Task', 'propel');
		$new_columns['start'] = __('Start', 'propel');
		$new_columns['end'] = __('End', 'propel');
		$new_columns['duration'] = __('Duration', 'propel');
		$new_columns['author'] = __('User', 'propel');
		return $new_columns;
	}

	/**
	 * Make columns sortable
	 * @since 2.0
	 * @param $columns
	 * @return $new_columns
	 */
	public static function register_sortable_columns( $columns ) {
		$new_columns['client'] = 'client';
		$new_columns['project'] = 'project';
		$new_columns['task'] = 'task';
		$new_columns['start'] = 'start';
		$new_columns['end'] = 'end';
		$new_columns['duration'] = 'duration';
		$new_columns['user'] = 'user';
		return $new_columns;
	}

	
	/**
	 * Output table data
	 * @since 2.0
	 * @param $column_name
	 * @param $id
	 */		 
	public static function manage_columns( $column_name, $id ) {
		global $wpdb;

		$post = get_post( $id );

		$parent = get_post( $post->post_parent );

		if( $parent->post_type == "propel_project" ) {
			$project = $parent;
		} else {
			$task = $parent;
			$project = get_post( $task->post_parent );
		}

		$client = get_post_meta( $project->ID, '_propel_project_owner', true );

		switch ( $column_name ) {
			case 'id':
				echo $id;
				break;

			case 'action':
				if( !get_post_meta( $id, '_propel_time_start', true) )
					echo "<a href='?post_type=propel_time&id=$id&start=1'>Start</a>";
				else if(! get_post_meta( $id, '_propel_time_end', true) )
					echo "<a href='?post_type=propel_time&id=$id&end=1'>End</a>";
				else
					echo "";

				break;

			case 'client':
				$user = get_userdata( $client );
				if($user)
					echo "<a href='?post_type=propel_time&client=#'>" . $user->user_nicename . "</a>";
				break;

			case 'project':
				echo $project->post_title;
				break;

			case 'task':
				if( isset( $task->post_title ) ) 
					echo $task->post_title;
				break;

			case 'start':
				$start = get_post_meta( $id, '_propel_time_end', true );
				if($start)
					echo date('G:i:s', $start );
				break;

			case 'end':
				$end = get_post_meta( $id, '_propel_time_end', true );
				if($end)
					echo date('G:i:s', $end );
				break;

			case 'duration':
				$start = get_post_meta( $id, '_propel_time_start', true );
				$end =  get_post_meta( $id, '_propel_time_end', true );
				echo date('G:i:s', $end-$start );
				break;

			default:
				break;
		}
	}

	/**
	 * Create a WP-Invoice
	 * @since 2.0
	 */
	public static function bill() {
		die("Sorry, not implemented yet...");
	}
}

?>