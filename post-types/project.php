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
		add_action( 'project_get_task', array( __CLASS__, 'project_get_task' ) );
		add_action( 'wp_ajax_add_task', array( __CLASS__, 'wp_ajax_add_task' ) );
		add_action( 'wp_ajax_get_update', array( __CLASS__, 'wp_ajax_get_update' ) );
		add_action( 'wp_ajax_check_update', array( __CLASS__, 'wp_ajax_check_update' ) );
		add_action( 'wp_ajax_update_task', array( __CLASS__, 'wp_ajax_update_task' ) );
		add_action( 'wp_ajax_get_task_detail', array( __CLASS__, 'wp_ajax_get_task_detail' ) );
		add_action( 'wp_ajax_restore_task', array( __CLASS__, 'wp_ajax_restore_task' ) ); // aps2012
		add_action( 'wp_ajax_delete_task', array( __CLASS__, 'wp_ajax_delete_task' ) );  // aps2012
		add_action( 'wp_ajax_trash_task', array( __CLASS__, 'wp_ajax_trash_task' ) );  // aps2012
		add_action( 'load-post.php', array( __CLASS__, 'post' ) );
		add_action( 'admin_head', array( __CLASS__, 'tooltip_css' ) );
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

		    // aps2012 : deleted because ajax submit is used instead
			//wp_delete_post($_GET['post']);
			//wp_redirect( $_SERVER['HTTP_REFERER'] );
			//die();
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
				
					echo date( get_option( 'date_format' ) , $date ); // Project's actual due date.
					echo "<br />" . date( get_option( 'date_format' ) . " G:i" ); // Todays date.
					
					$day   = date('d'); // Day of the countdown
					$month = date('m'); // Month of the countdown
					$year  = date('Y'); // Year of the countdown
					$hour  = date('H'); // Hour of the day (east coast time)
					
					$calculation = ( $date - time() ) / 3600;
					$hours = (int)$calculation + 24;
					$days  = (int)( $hours / 24 ) - 1;
					
					$hours_remaining = $hours-($days*24)-24;
					
					// Used for debugging.
					// date_default_timezone_set('America/Los_Angeles');
					// echo "<br />";
					// print_r(date_default_timezone_get());
					
					if ( $hours >= 48 ) {
						echo "<br /><span style='color: green;'>Due in " . $days . " days " . $hours_remaining . " hours.</span>";
					} elseif ( $hours <= 48 && $hours >= 24 ) {
						echo "<br /><span style='color: brown;'>Due tomorrow.</span>";
					} elseif ( $hours <= 24 && $hours >= 0 ) {
						echo "<br /><span style='color: orange;'>Due today.</span>";
					} elseif ( $hours < 0 && $hours > -24 ) {
						echo "<br /><span style='color: red;'>" . str_replace( '-', '', $hours) . " hours past due.</span>";
					} elseif ( $hours < -24 ) {
						echo "<br /><span style='color: red; font-weight: bold;'>" . str_replace( '-', '', $days) . " days past due.</span>";
					}
					
					else {
						echo "<br /><span>Recurring Project</span>";
					}
					
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
			add_meta_box('propel_add_task', __('Add Task', 'propel'), array( __CLASS__, 'add_task' ), 'propel_project', 'normal', 'high', 10, 2);
			add_meta_box('propel_project_tasks', __('Project Tasks', 'propel'),	array( __CLASS__, 'project_tasks'), 'propel_project', 'normal', 'high', 10, 2 );
			add_meta_box('propel_completed_tasks', __('Completed Tasks', 'propel'), array( __CLASS__, 'completed_tasks'), 'propel_project', 'normal', 'high', 10, 2 );
			add_meta_box('propel_deleted_tasks', __('Deleted Tasks', 'propel'), array( __CLASS__, 'deleted_tasks'), 'propel_project', 'normal', 'high', 10, 2 ); // aps2012 deleted meta box
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
		require( dirname(__FILE__) . '/../metaboxes/project-tasks.php');
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
		require( dirname(__FILE__) . '/../metaboxes/project-tasks.php' );
	}
/**
* aps2012 : select deleted task under a parent ID
*/
	public static function deleted_tasks( $post, $id ) {
		global $wpdb;
		$parent = get_the_ID(); 
		//@todo: profile query / use WP_Query
		$query = "SELECT * FROM {$wpdb->posts}
		        	WHERE `post_parent`={$parent} AND `post_status` = 'trash'
		        ORDER BY `ID` DESC;";

		$posts = $wpdb->get_results($query);
		require( dirname(__FILE__) . '/../metaboxes/project-deleted.php');
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
		//if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		//	return;

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
		
		$curr_user = get_current_user();
		
		$post = array(
			'post_title' => $_POST['title'],
			'post_content' => $_POST['description'],
			'post_parent' => $_POST['parent'],
			'post_author' => $curr_user->ID,
			'post_type' => 'propel_task',
			'post_status' => 'publish'
		);		
		
		$id = wp_insert_post( stripslashes_deep($post) );
		if( !$id ) die(0);
		$cnt = 0;
		foreach($_POST['user'] as $user){
			update_post_meta( $id, '_propel_user_'.$cnt, $user );
			$cnt++;
		}
		update_post_meta( $id, '_propel_user', $cnt );	
		update_post_meta( $id, '_propel_start_date', time() );
		update_post_meta( $id, '_propel_end_date', strtotime( $_POST['end_date'] ) );
		update_post_meta( $id, '_propel_complete', 0 );
		update_post_meta( $id, '_propel_priority', $_POST['priority'] );				

		do_action( 'post_wp_ajax_add_task', $id );
		self::auto_notify($id,'new-assign');
		do_action( 'project_get_task', $id);			

		die($id);
	}

	/**
	 * @since 2.0
	 */
	public static function admin_footer() { ?>

		<script type="text/javascript">
		
		var oTable;
		var cTable;
		var _nTr = new Array();
		var _set;
		
		jQuery(document).ready(function($) { 	

				jQuery('#propel_completed_tasks table').attr('id','propel-com-tasks');
				
				var cols = jQuery(".tasks-table:first th").size()
				var asdf = [];
				var _html='';
				for(i = 0; i < cols; i++) {
					asdf.push(null);
					_html = _html + '<td><p></p></td>';
				}

				var _tdcnt = jQuery('#propel_project_tasks #propel-tasks').find('td').size();
				
				if( _tdcnt == 1 ) {
					jQuery('#propel_project_tasks #propel-tasks tbody tr').remove();
					jQuery('#propel_project_tasks #propel-tasks tbody').prepend('<tr id="no-data" class="odd">'+ _html +'</tr>');
					jQuery('#propel_project_tasks #propel-tasks tbody').find('td:eq(3)').find('p').html('No data available');
				}								
				
				var __tdcnt = jQuery('#propel_completed_tasks #propel-com-tasks').find('td').size();
				if( __tdcnt == 1 ) {
					jQuery('#propel_completed_tasks #propel-com-tasks tbody tr').remove();
					jQuery('#propel_completed_tasks #propel-com-tasks tbody').prepend('<tr id="no-data" class="odd">'+ _html +'</tr>');
					jQuery('#propel_completed_tasks #propel-com-tasks tbody').find('td:eq(3)').find('p').html('No data available');
				}
				
				// aps2012 : enable datatable for deleted tasks
				//$('#propel-deleted tr').find('td:eq(2) a').css('display','none');
				var _tdcntd = jQuery('#propel_project_tasks #propel-deleted').find('td').size();
				if( _tdcntd == 1 ) {
					jQuery('#propel_deleted_tasks #propel-deleted tbody tr').remove();
					jQuery('#propel_deleted_tasks #propel-deleted tbody').prepend('<tr id="no-data" class="odd">'+ _html +'</tr>');
					jQuery('#propel_deleted_tasks #propel-deleted tbody').find('td:eq(3)').find('p').html('No data available');
				}			
				dTable = jQuery('#propel-deleted').dataTable();														
			
				oTable = jQuery('#propel-tasks').dataTable( 
					{
					"bStateSave": true,
					//"sPaginationType": "full_numbers",
					"bFilter": false,
					"bPaginate": false,
					"bRetreive":true,
					"bInfo": false,
					"aoColumnDefs": [
						{ "bSortable": false, "aTargets": [ 0 ] }
					],
					"aaSorting": [[1, 'asc']],
					"aoColumns" : asdf
				});
				
				cTable = jQuery('#propel-com-tasks').dataTable( 
					{
					"bStateSave": true,
					//"sPaginationType": "full_numbers",
					"bFilter": false,
					"bPaginate": false,
					"bRetreive":true,
					"bInfo": false,
					"aoColumnDefs": [
						{ "bSortable": false, "aTargets": [ 0 ] }
					],
					"aaSorting": [[1, 'asc']],
					"aoColumns" : asdf
				});						
								
					jQuery(".date").datetimepicker({
						ampm: true,
						addSliderAccess: true,
						sliderAccessArgs: { touchonly: false },					
					});
					
					jQuery('#propel_edit_task').css({ 'display':'none' });
					jQuery('#propel_add_task').css({ 'display':'block' });
					
					jQuery("#add-task").click(function(e) {									
						add_Data();
						return false;			
					});
		
	   	            //
					// aps 2012 : added for trashing task.
					//
				   jQuery(".propel_trashtask").live('click',function(){ 
					    var task_id = jQuery(this).attr('alt'); 
						 var $parent = jQuery(this).parent().parent();
						 var data = {
									action: 'trash_task',
									security: '<?php echo wp_create_nonce( "trash-task" ); ?>',
									postid: task_id
							};
							
						  jQuery.post(ajaxurl, data, function(response) {	
								
								$parent.find('td')
									 .wrapInner('<div style="display: block;" />')
									 .parent()
									 .find('td > div')
									 .slideUp(700, function(){											    				
											$parent.remove();											
								});
								
								$parentCom = jQuery('#propel_deleted_tasks #propel-deleted tbody tr');
										
								get_JSON(response,2);	
								
                                $('#propel-deleted tr').find('td:eq(0) a').attr('class','propel_deltask');
								$('#propel-deleted tr').find('td:eq(1)').attr('class','gen-icon db-updated gen-deleted-icon');
								$('#propel-deleted tr').find('td:eq(2) a').attr('class','propel_restore');
								$('#propel-deleted tr').find('td:eq(2) a').attr('href','javascript:;');
								$('#propel-deleted tr').find('td:eq(2) a').attr('alt',task_id);
								$('#propel-deleted tr').find('td:eq(2) a').attr('title','Restore');

							});	
						 						 
     					 return false;
					 });
		             //
					// aps 2012 : added for physical delete task
					//
					jQuery(".propel_deltask").live('click',function(){
							var task_id = jQuery(this).attr('alt'); 
							var $parent = jQuery(this).parent().parent();
							var data = {
									action: 'delete_task',
									security: '<?php echo wp_create_nonce( "delete-task" ); ?>',
									postid: task_id
							};
							
							$parent.find('td')
									 .wrapInner('<div style="display: block;" />')
									 .parent()
									 .find('td > div')
									 .slideUp(700, function(){											    				
											$parent.remove();											
							 });
							
							jQuery.post(ajaxurl, data, function(response) {});			
		
						 return false;
					 });
					
					//
					// aps 2012 : added for restoring task
					//
					 jQuery(".propel_restore").live('click',function(){
							var task_id = jQuery(this).attr('alt'); 
							var $parent = jQuery(this).parent().parent();
							 var prog_ress = $parent.find('td:eq(7)').attr('data-value');
							var data = {
									action: 'restore_task',
									security: '<?php echo wp_create_nonce( "restore-task" ); ?>',
									postid: task_id
							};
							
							jQuery.post(ajaxurl, data, function(response) {	
								
								$parent.find('td')
									 .wrapInner('<div style="display: block;" />')
									 .parent()
									 .find('td > div')
									 .slideUp(700, function(){											    				
											$parent.remove();											
								});
								
								if(prog_ress == 100){
									get_JSON(response, 1);
								} else {
									get_JSON(response);
								}

							});			
		
						 return false;
					 });
					
					//rob_eyouth : added this to remove the checked data from the current task table
					// and added to the completed task table
				  jQuery("#propel_project_tasks #propel-tasks tbody td.gen-unchecked-icon").live('click',function(){
							var task_id = jQuery(this).parent().attr('id'); 
							var $parent = jQuery(this).parent();
							
							var data = {
									action: 'update_task',
									security: '<?php echo wp_create_nonce( "update-task" ); ?>',
									postid: task_id,
									end_date: '<?php echo time(); ?>',
									priority: jQuery('#propel_edit_prior').val(),
									complete: 100,
									propel_post_author: '<?php $userid = wp_get_current_user(); echo $userid->ID;  ?>',
									//jQuery('#propel_post_author').val()
							};
							
							jQuery.post(ajaxurl, data, function(response) {	
								
								$parent.find('td')
									 .wrapInner('<div style="display: block;" />')
									 .parent()
									 .find('td > div')
									 .slideUp(700, function(){											    				
											$parent.remove();											
								});
								
								$parentCom = jQuery('#propel_completed_tasks #propel-tasks tbody tr');	
								get_JSON(response,1);	

							});			
		
						 return false;
					 });
					 
					jQuery('#propel-tasks p span.span_contr').each(function(){
											
						var _contributorid;
						var _get_new_author;
						jQuery(this).live('click',function(){
								_contributorid = jQuery(this).attr('id');
								var task_id = jQuery(this).closest('tr').attr('id');
								var this_id = jQuery(this).parent().attr('id');
								var _html = jQuery('#'+this_id).clone();
								var _userdrpdwn = jQuery('.metabox-add-task-contributor').clone().css({'display':'block'});
								jQuery('#'+this_id).empty().append(_userdrpdwn);	
								jQuery('#'+this_id +' select').focus();
								
								jQuery('#'+this_id +' select').live('click',function(){ 
									return false; 
								}).live('change',function(){
									
									var data = {
											action	: 'update_task',
											security: '<?php echo wp_create_nonce( "update-task" ); ?>',
											parent	: '<?php echo get_the_ID(); ?>',
											postid	: task_id,
											user	: _contributorid,
											userval : jQuery(this).val(),
									}
									
									$.post(ajaxurl, data, function(response) {
									
										jQuery('tr#'+task_id).fadeIn('slow',function(){

											var aPos = oTable.fnGetPosition( this );
											var _obj = jQuery.parseJSON(response);
											var nTr = oTable.fnSettings().aoData[ aPos ].nTr;										
											jQuery(_html).find('span#'+_contributorid).attr('id',_obj.task_authid).text(_obj.task_author).css({'padding-left':'3px'});
											//oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );
											oTable.fnUpdate( jQuery(_html).html(), aPos, 4 );									
											jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);					
										});																								
									});									
									
								}).live('focusout',function(){									
									jQuery('#propel_edit_title_'+ task_id).remove();	
									jQuery('#'+this_id).html(_html);											
								});		
								
								
								
							});			
						}); 
					
					jQuery("#propel_project_tasks #propel-tasks tbody tr td p").live('click',function(){
							
							var task_id = jQuery(this).closest('tr').attr('id');					
							var this_id = jQuery(this).attr('id');
							var $this = jQuery(this);
							var _this_id;
							this_id === undefined ? _this_id = 0 : _this_id = this_id.substr(0,10);
							var _tr_before_id = jQuery('#'+task_id).prev('tr').attr('id');							

							jQuery('tr#'+task_id).find('td:eq(1)').removeClass('db-updated');
							
							switch(_this_id){
								case 'edit_title':	
									var _val = jQuery('#'+this_id).text();					
									jQuery('#'+this_id).empty().append('<input type="text" id="propel_edit_title_'+ task_id +'" value="'+ _val +'" size="60">');
									jQuery('#propel_edit_title_'+ task_id).focus();	
									jQuery('#propel_edit_title_'+ task_id).live('keypress',function(event){
										if ( event.which == 13){
											var data = {
													action: 'update_task',
													security: '<?php echo wp_create_nonce( "update-task" ); ?>',
													parent: '<?php echo get_the_ID(); ?>',
													postid: task_id,
													title: jQuery('#propel_edit_title_'+ task_id).val(),
											};
											var _data = jQuery('#propel_edit_title_'+ task_id).val();
											jQuery.post(ajaxurl, data, function(response) {
												var _obj = jQuery.parseJSON(response);
												jQuery('tr#'+task_id).fadeIn('slow',function(){
													var aPos = oTable.fnGetPosition( this );
													var _html ="";
													var len = _obj.task_content.length;
													if (len > 75 ) {
														var _content = _obj.task_content.substr(0,75)+' ...';
														_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
													}else{
														var _content = _obj.task_content.substr(0,75);
														_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
													}			

													oTable.fnUpdate( '<p id="edit_title_'+ task_id +'">'+ _obj.task_title +'</p>'+_html, aPos, 3 )
														jQuery('.propeltooltip').each(function(){
															var _content = jQuery(this).attr('title');
															var _id = jQuery(this).attr('id');
															jQuery(this).propeltooltip({
																id		: _id,
																content : _content							
															});
														});
																			
												});

											});
											
											return false;
										}
									}).live('click',function(){										
										return false;
									}).live('focusout',function(){									
										jQuery('#propel_edit_title_'+ task_id).remove();
										jQuery('#'+this_id).empty().html(_val);
									});	
															
									break;
								case 'edit_owner':
										var data = {
												action: 'get_task_detail',
												security: '<?php echo wp_create_nonce( "get-task-detail" ); ?>',
												postid: task_id,
												retnum: 2
										};
						
										jQuery.post(ajaxurl, data, function(response) {
											var _html = jQuery('#'+this_id).html();
											jQuery('#'+this_id).empty().append(response);	
											jQuery('#'+this_id +' select').focus();
											jQuery('#'+this_id +' select').live('click',function(){ 
												return false; 
											}).live('change',function(){

													var data = {
															action: 'update_task',
															security: '<?php echo wp_create_nonce( "update-task" ); ?>',
															parent: '<?php echo get_the_ID(); ?>',
															postid: task_id,
															user: jQuery(this).val(),
													};
													
													jQuery.post(ajaxurl, data, function(response) {
														jQuery('tr#'+task_id).fadeIn('slow',function(){
															var aPos = oTable.fnGetPosition( this );
															var _obj = jQuery.parseJSON(response);
															var nTr = oTable.fnSettings().aoData[ aPos ].nTr;															
															oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );													
															jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);																									
														});																								
													});
											}).live('focusout',function(){									
												jQuery('#propel_edit_title_'+ task_id).remove();	
												jQuery('#'+this_id).html(_html);											
											});	
											
										});					
										
									break;	
								case 'edit_sdate':	
										var _val = jQuery('#'+this_id).text();
										jQuery('#'+this_id).empty().append('<input class="metabox-add-task-date widefat sdate" type="text" placeholder="Start Date" />');									 
										jQuery('#'+this_id+' input.sdate').datetimepicker({
											ampm: true,
											addSliderAccess: true,
											sliderAccessArgs: { touchonly: false },
											onClose: function(dates) { 
											  if ( dates !== "" ){
												var data = {
													action: 'update_task',
													security: '<?php echo wp_create_nonce( "update-task" ); ?>',
													postid: task_id,
													start_date : dates
												};								
												jQuery.post(ajaxurl, data, function(response) {
													var _obj = jQuery.parseJSON(response);
													jQuery('#'+this_id+' input.sdate').remove();
													jQuery('#'+this_id).html(_obj.task_start);
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-due-icon');
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-past-due-icon');
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-published-icon');		
													jQuery('#'+task_id).find('td:eq(1)').addClass('gen-'+ _obj.task_status +'-icon');													
												});
											  }else{
											  	jQuery('#'+this_id).html(_val);
											  }	
											} 
										});
										jQuery('#'+this_id+' input.sdate').live('focus',function(){
											return false;
										});	
										jQuery('#'+this_id+' input').focus();									
										jQuery('#'+this_id+' input').live('click',function(){ 

										}).live('focusout',function(){								
												jQuery('#end_date_'+ task_id).remove();	
												jQuery('#'+this_id).html(_val);											
										});																		
									break;							
								case 'edit_edate':
										var _val = jQuery('#'+this_id).text();
										jQuery('#'+this_id).empty().append('<input class="metabox-add-task-date widefat enddate" type="text" placeholder="End Date" />');									 
										jQuery('#'+this_id+' input.enddate').datetimepicker({
											ampm: true,
											addSliderAccess: true,
											sliderAccessArgs: { touchonly: false },
											onClose: function(dates) { 
											  if (dates !== "" ){
												var data = {
													action: 'update_task',
													security: '<?php echo wp_create_nonce( "update-task" ); ?>',
													postid: task_id,
													end_date : dates
												};								
												
												jQuery.post(ajaxurl, data, function(response) {
													var _obj = jQuery.parseJSON(response);
													jQuery('#'+this_id+' input.enddate').remove();
													jQuery('#'+this_id).html(_obj.task_end);
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-due-icon');
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-past-due-icon');
													jQuery('#'+task_id).find('td:eq(1)').removeClass('gen-published-icon');		
													jQuery('#'+task_id).find('td:eq(1)').addClass('gen-'+ _obj.task_status +'-icon');																																				
												});
											  }else{
											  	jQuery('#'+this_id).html(_val);
											  }
											} 
										});
										jQuery('#'+this_id+' input.enddate').live('focus',function(){
											return false;
										});	
										jQuery('#'+this_id+' input').focus();									
										jQuery('#'+this_id+' input').live('click',function(){ 
										}).live('focusout',function(){								
												jQuery('#end_date_'+ task_id).remove();	
												jQuery('#'+this_id).html(_val);											
										});	
									break;
								case 'edit_progr':
										var data = {
												action: 'get_task_detail',
												security: '<?php echo wp_create_nonce( "get-task-detail" ); ?>',
												postid: task_id,
												retnum: 3
										};
						
										jQuery.post(ajaxurl, data, function(response) {
											var _html = jQuery('#'+this_id).html();
											jQuery('#'+this_id).empty().append(response);	
											jQuery('#'+this_id +' select').focus();
											jQuery('#'+this_id +' select').live('click',function(){ 
												return false; 
											}).live('change',function(){
													var _completed = jQuery(this).val();
													var data = {
															action: 'update_task',
															security: '<?php echo wp_create_nonce( "update-task" ); ?>',
															parent: '<?php echo get_the_ID(); ?>',
															postid: task_id,
															complete: jQuery(this).val(),
													};
													var _data = jQuery(this).val();
													jQuery.post(ajaxurl, data, function(response) {
														jQuery('tr#'+task_id).fadeIn('slow',function(){
															var aPos = oTable.fnGetPosition( this );
															var _obj = jQuery.parseJSON(response);
															var nTr = oTable.fnSettings().aoData[ aPos ].nTr;
															if ( _obj.is_start === 1 && _obj.is_end === 0 ) { 
																oTable.fnUpdate( '<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'"></progress></p>', aPos, 6 );			
																jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);																					
															} else if ( _obj.is_start === 0 && _obj.is_end === 1 ) { 
																oTable.fnUpdate( '<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'"></progress></p>', aPos, 6 );
																jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);				
															} else if ( _obj.is_start === 1 && _obj.is_end === 1 ) { 
																oTable.fnUpdate( '<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'"></progress></p>', aPos, 7 );
																jQuery(nTr).find('td:eq(7)').attr('data-value',_obj.task_progress);				
															}else {
																oTable.fnUpdate( '<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'"></progress></p>', aPos, 5 );	
																jQuery(nTr).find('td:eq(5)').attr('data-value',_obj.task_progress);			
															}																																									
														});									
													});
											}).live('focusout',function(){									
												jQuery('#propel_edit_title_'+ task_id).remove();		
												jQuery('#'+this_id).html(_html);										
											});
											
										});
																
									break;	
								default:
									break;	
														
							}//end of switch
						return false;
					}).live('mouseenter',function(){
					   jQuery(this).css({'cursor':'pointer'}); 
					}).live('mouseleave',function(){
					   jQuery(this).css({'cursor':'normal'}); 
					});
														
					jQuery('#propel_add_task input, #propel_add_task textarea, #propel_add_task select').live('focus',function(){
						jQuery('#div_task').fadeIn('slow');	
					}).live('focusout',function(){
						jQuery('#div_task').fadeOut('slow');	
					});
					
					jQuery('form#post #_task_desc').live('keypress',function(event){
						if (event.which === 13){
							add_Data();	
							return false;								
						}
					});
					
					jQuery('form#post #propel_add_task').live('keypress',function(event){
						if (event.which === 13){
							return false;								
						}
					});
					
					jQuery('#propel_project_tasks .metaboxes-add-task').css({ 'border':'1px solid #DFDFDF', 'padding':'10px', 'margin':'10px 0' });
					jQuery('#propel_post_author').addClass('task-priority');
					
					jQuery('.propeltooltip').each(function(){
						var _content = jQuery(this).attr('title');
						var _id = jQuery(this).attr('id');
						jQuery(this).propeltooltip({
							id		: _id,
							content : _content							
						});
					});
					
					jQuery('#propel_completed_task tbody tr').find('td:eq(1)').css({'width':0});
					
					//check_Existence();	
					if (jQuery('#task_contributor').length > 0 ) {		
						var _taskcontributorcss = jQuery('#task_contributor').offset().left - 165;
						var _taskcontributorw = jQuery('#task_contributor').innerWidth();
					}
					var _newsearchstring;
					var _listid;
					var _listidfind = false;
					jQuery('#task_contributor_list').css({'left':_taskcontributorcss, 'width':_taskcontributorw});	
					jQuery('#task_contributor').keyup(function(e){	
//						var _stringsearch = String.fromCharCode(e.which);
//						var _listItem = jQuery('#task_contributor_list li#'+jQuery(this).val().toLowerCase());	
//						var _indexofli = jQuery('#task_contributor_list li').index(_listItem);						
						
						var _arr = jQuery('#task_contributor_list li:econtains("'+ jQuery(this).val().toLowerCase() +'")');						
						if( _arr.length > 0 && jQuery(this).val() !== '' ){
							jQuery('#task_contributor_list').find('li').removeClass('searchable');
							jQuery(_arr).each(function(i,el){ 
								jQuery('#task_contributor_list').fadeIn('slow'); 
								jQuery(el).fadeIn('slow'); 
								jQuery(el).addClass('searchable');								
								if (!_listidfind){
									jQuery(el).css({'color':'red'}).addClass('selected');
									_listid = jQuery(el).attr('id');								
									_listidfind = true;
								}
							});							
						}else{
							jQuery('#task_contributor_list, #task_contributor_list li.propel_not_added').fadeOut('slow');												
						}

					}).focusin(function(){										
							jQuery('#task_contributor_list, #task_contributor_list li').fadeOut('slow');						
					}).keydown(function(e){
							switch (e.keyCode){
							case 40:	
								jQuery('#task_contributor_list').find('li').css({'color':'black'}).removeClass('selected');	
								jQuery('#task_contributor_list').find('li.searchable').next('li.searchable').css({'color':'red'}).addClass('selected');		
								break;
							case 38:
								jQuery('#task_contributor_list').find('li').css({'color':'black'}).removeClass('selected');										
								jQuery('#task_contributor_list').find('li.searchable').prev('li.searchable').css({'color':'red'}).addClass('selected');		
								break;
							case 13:
								jQuery(this).val('');
								var _selList = jQuery('#task_contributor_list li.selected').find('div');
								jQuery(_selList).removeClass().addClass('del_contributor').parent().removeClass().addClass('propel_is_added').clone().appendTo(jQuery('#task_contributor_list'));					
								jQuery(_selList).parent().remove();	
								jQuery('#selected_task_contributor').find('li').remove();									
								jQuery('#task_contributor_list, #task_contributor_list li').fadeOut('slow')
								jQuery('#task_contributor_list li').each(function(i,el){
									if (jQuery(el).hasClass('propel_is_added')){
										var _txtselected;
										jQuery(this).length < 5 ? _txtselected = jQuery(this).attr('id').substr(0,5) : _txtselected = jQuery(this).attr('id').substr(0,5)+'...';
																					
										jQuery('#selected_task_contributor').append('<li id="'+jQuery(this).attr('id')+'">'+_txtselected+'<span class="contributor_x">x</span></li>');	
										jQuery('#task_contributor').val('');
									}
								});		

								
								break;
							}
					});	
					
					jQuery('#task_contributor_list').find('li.searchable').css({'color':'#F00'});
					
 				    jQuery('#task_contributor_list li').live('mouseenter',function(){
						jQuery('#task_contributor_list').find('li').removeClass('searchable').css({'color':'#000'});
						jQuery(this).animate({'color':'#F00'}).addClass('searchable');
					}).live('mouseleave',function(){
						jQuery('#task_contributor_list').find('li').removeClass('searchable');
						if (jQuery(this).hasClass('propel_is_added') && jQuery(this).hasClass('searchable')){
							jQuery(this).animate({'color':'#F00'});
						}else{
							jQuery(this).animate({'color':'#000'});	
						}
						
					})
										
					jQuery('.add_contributor').live('click',function(){
							jQuery(this).removeClass().addClass('del_contributor').parent().removeClass().addClass('propel_is_added').clone().appendTo(jQuery(this).offsetParent());						
							jQuery(this).parent().fadeOut(function(){
								jQuery(this).remove();
								jQuery('#selected_task_contributor').find('li').remove();	
								jQuery('#task_contributor_list li').each(function(){
									if (jQuery(this).hasClass('propel_is_added')){	
										var _txtselected;
										jQuery(this).length < 5 ? _txtselected = jQuery(this).attr('id').substr(0,5) : _txtselected = jQuery(this).attr('id').substr(0,5)+'...';
																					
										jQuery('#selected_task_contributor').append('<li id="'+jQuery(this).attr('id')+'">'+_txtselected+'<span class="contributor_x">x</span></li>');	
										jQuery('#task_contributor').val('');
									}
								});							
							});								
					});
					
					jQuery('.del_contributor').live('click',function(){
							console.log($(this).parent().attr('id'));
							jQuery(this).removeClass().addClass('add_contributor').parent().removeClass().addClass('propel_not_added').clone().prependTo(jQuery(this).offsetParent());	
							jQuery(this).parent().fadeOut(function(){
								jQuery(this).remove();
								jQuery('#selected_task_contributor').find('li').remove();
								jQuery('#task_contributor_list li').each(function(){
									if (jQuery(this).hasClass('propel_is_added')){											
										var _txtselected;
										jQuery(this).length < 5 ? _txtselected = jQuery(this).attr('id').substr(0,5) : _txtselected = jQuery(this).attr('id').substr(0,5)+'...';
																					
										jQuery('#selected_task_contributor').append('<li id="'+jQuery(this).attr('id')+'">'+_txtselected+'<span class="contributor_x">x</span></li>');
										jQuery('#task_contributor').val('');													
									}
								});	
							});
					});
					
					jQuery('#task_contributor_list').mouseleave(function(){
						//jQuery(this).fadeOut('slow');
					});
					
					jQuery('.contributor_x').live('click',function(){
						jQuery(this).parent().remove();
						jQuery('#task_contributor_list li#'+jQuery(this).parent().attr('id')).removeClass().addClass('propel_not_added');
						jQuery('#task_contributor_list li#'+jQuery(this).parent().attr('id')).find('div').removeClass().addClass('add_contributor');
					});
					
					jQuery('#_task_desc').focusin(function(){
						jQuery('#task_contributor_list').fadeOut('slow');
					});
					
	});//End of document.ready  
	
	jQuery.expr[':'].econtains = function(obj, index, meta, stack){
		return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;
	};
	
//	function check_Existence(){
//
//		clearTimeout(_set);
//		
//		_set = setInterval(function(){ 
//			var d=new Date();
//			var t=d.toLocaleTimeString();
//			var data = {
//					action: 'get_update',
//					parent: '<?php esc_attr_e(get_the_ID()); ?>',
//					security: '<?php echo wp_create_nonce( "get-update" ); ?>'
//				};
//				
//			jQuery.post(ajaxurl, data, function(response) {
//				
//				var _post = jQuery.parseJSON(response);
//					
//					jQuery.each(_post,function(i,el){
//						  
//					  if ( el.post_status === "publish" ) {	
//
//						var _isUpdate = false;
//						var _len = 1;																																						
//								
//							if ( el !== undefined ){	
//								
//									if ( el.post_date !== el.post_modified ){									
//										if ( !jQuery('#propel-tasks tbody tr#'+ el.ID).find('td:eq(1)').hasClass('db-updated') ){				
//											_len = 0;
//											_isUpdate = true;
//										}else{
//											var _tr = jQuery('#propel-tasks tbody tr#'+ el.ID);
//											if ( (el.post_title !== _tr.find('td:eq(3)').find('p').text()) || (el.post_content !== _tr.find('td:eq(3)').find('small').text())){
//												var aPos = _tr.index();
//												var _html ="";
//												var len = el.post_content.length;
//												if (len > 75 ) {
//													var _content = el.post_content.substr(0,75)+' ...';
//													_html = '<div id="desc_'+ el.ID +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ el.post_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
//												}else{
//													var _content = el.post_content.substr(0,75);
//													_html = '<div id="desc_'+ el.ID +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ el.post_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
//												}													
//												oTable.fnUpdate( '<p id="edit_title_'+ el.ID +'">'+ el.post_title +'</p>'+_html, aPos, 3 )
//												_len = 0;
//												_isUpdate = true;
//											}else if ( el.progress > _tr.find('p#edit_progr_'+el.ID).find('progress').val() ){
//												_tr.find('p#edit_progr_'+el.ID).find('progress').val(el.progress);
//												_len = 0;
//												_isUpdate = true;																								
//											}
//											
//										}//end of if !Query('#propel-tasks tbody tr#'+ el.ID).find('td:eq(1)').hasClass('db-updated')
//										
//									}else if ( el.post_date === el.post_modified ){										
//										if ( !jQuery('#propel-tasks tbody tr#'+ el.ID).find('td:eq(1)').hasClass('db-updated') ){										
//											_len = 0;
//											_isUpdate = false;
//										}										
//									}else{										
//										_len = jQuery('#propel-tasks tbody tr#'+el.ID).length;
//									}
//									
//									if ( _len == 0 ) {
//
//										var data = {
//											action: 'update_task',
//											security: '<?php echo wp_create_nonce( "update-task" ); ?>',
//											pID: el.ID,
//										};
//										
//										jQuery.post(ajaxurl, data, function(response) {
//											
//											var _msg;
//											var _obj = jQuery.parseJSON(response);
//											
//											if ( _obj.task_id !== null || _obj.task_id !== undefined ) {
//												
//												if (_isUpdate ==  true ){													 	
//													 _msg = "This task has just been updated." 
//												}else{
//													 get_JSON(response);
//													 _msg = "A new task has been assigned to you.";
//												}				
//																														
//												var _cuser = jQuery('#user-id').val();
//												
//												if ( parseInt(_cuser) == parseInt(_obj.task_authid) ){
//													get_JSON(response,1);
//													jQuery('#propel-tasks tbody tr#'+_obj.task_id+' td.gen-published-icon p').css({'background' : '#FFF'}).animate({'backgroundColor':'lime'},7000,'linear');
//													jQuery('#propel-tasks tbody tr#'+_obj.task_id).find('td:eq(1)').addClass('db-updated').append('<div class="propelnotify"><span class="narrow"></span>'+ _msg +'<small id="xclose">X<small></div>');
//													
//													
//													jQuery('.propelnotify').fadeIn(3000,'linear');									
//													
//													jQuery('#xclose').live('mouseenter',function(){
//														jQuery(this).animate({'color': "#FFF"},'slow','linear');
//													}).live('mouseleave',function(){
//														jQuery(this).animate({'color': "#CCC"},'slow','linear');
//													}).live('click',function(){
//														jQuery(this).offsetParent().fadeOut('slow',function(){
//															jQuery(this).remove();
//															//check_Existence()
//														});
//													});											
//												}
//										}
//									
//								});
//								
//							}//End of _len == 0
//							
//						}//end of response != undefiend											
//					
//					  }else{						 																																					
//								
//							if ( el !== undefined ){			
//								var _trlen = jQuery('#propel-deleted tbody tr#'+ el.ID).length;
//								if ( _trlen <= 0 ){
//									if ( !jQuery('#propel-deleted tbody tr#'+ el.ID).find('td:eq(1)').hasClass('db-updated') ){	
//																	
//										var data = {
//											action: 'update_task',
//											security: '<?php echo wp_create_nonce( "update-task" ); ?>',
//											pID: el.ID,
//										};
//										
//										jQuery.post(ajaxurl, data, function(response) {
//
//											var _msg;
//											var _obj = jQuery.parseJSON(response);
//											
//											if ( _obj.task_id !== null || _obj.task_id !== undefined ) {
//												get_JSON(response,2);		
//												jQuery('#propel-tasks tbody tr#'+_obj.task_id).find('td')
//													 .wrapInner('<div style="display: block;" />')
//													 .parent()
//													 .find('td > div')
//													 .slideUp(700, function(){											    				
//														jQuery('#propel-tasks tbody tr#'+_obj.task_id).remove();											
//												});																																											
//											}
//									
//										});
//									}
//								}
//							
//							}//end of response != undefiend
//						  
//					  }//End of el.post_status
//	
//				});//End of each				
//					
//			});						
//		},60000);
//	}  

	function get_JSON(response,whichTable){

		var _obj = jQuery.parseJSON(response);
		
		if ( _obj.is_start === 1 && _obj.is_end === 0 ) { 	
			//aps2012		
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ _obj.task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="post.php?action=complete&post='+ _obj.task_id +'" title="Mark as complete">Complete</a>',
				'<p id="edit_title_'+ _obj.task_id +'">'+ _obj.task_title +'</p>',
				'<p id="edit_owner_'+ _obj.task_authid +'">'+ _obj.task_author +'</p>',
				'<p id="edit_sdate_'+ _obj.task_id +'" style="font-size: 10px; color: #999;">'+ _obj.task_start +'</p>',			
				'<p id="edit_progr_'+ _obj.task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'" ></progress></p>'
			);
			
			if (whichTable === 1){
				jQuery('#propel_completed_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-com-tasks').dataTable().fnAddData( _json );															
				var nTr = cTable.fnSettings().aoData[ a[0] ].nTr;			
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-checked-icon');
			}
			//aps2012 added deleted table 
			else if (whichTable === 2){
				jQuery('#propel_deleted_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-deleted').dataTable().fnAddData( _json );															
				var nTr = dTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-deleted-icon');
			}
			else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');
			}
			var _html = "";
			var len = _obj.task_content.length;
			if (len > 75 ) {
				var _content = _obj.task_content.substr(0,75)+' ...';
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}else{
				var _content = _obj.task_content.substr(0,75);
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}
			
				
			jQuery(nTr).attr('id',_obj.task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_start);			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});							
			
			// aps2012
			if(whichTable === 2){
				jQuery(nTr).find('td:eq(1) p').attr('title', 'deleted');
				jQuery(nTr).find('td:eq(1)').addClass('gen-deleted-icon').removeClass('gen-published-icon');								
			} 
			
		} else if ( _obj.is_start === 0 && _obj.is_end === 1 ) { 	
		    // aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ _obj.task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="post.php?action=complete&post='+ _obj.task_id +'" title="Mark as complete">Complete</a>',
				'<p id="edit_title_'+ _obj.task_id +'">'+ _obj.task_title +'</p>',
				'<p id="edit_owner_'+ _obj.task_authid +'">'+ _obj.task_author +'</p>',
				'<p id="edit_edate_'+ _obj.task_id +'" style="font-size: 10px; color: #999;">'+ _obj.task_end +'</p>',				
				'<p id="edit_progr_'+ _obj.task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'"></progress></p>'
			);		
			
			if (whichTable === 1){
				jQuery('#propel_completed_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-com-tasks').dataTable().fnAddData( _json );															
				var nTr = cTable.fnSettings().aoData[ a[0] ].nTr;		
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-checked-icon');	
			}
			
			//aps2012 added deleted table
			else if (whichTable === 2){
				jQuery('#propel_deleted_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-deleted').dataTable().fnAddData( _json );															
				var nTr = dTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');				
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-deleted-icon');
			}
			
			else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');
			}
			var _html = "";
			var len = _obj.task_content.length;
			if (len > 75 ) {
				var _content = _obj.task_content.substr(0,75)+' ...';
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}else{
				var _content = _obj.task_content.substr(0,75);
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}
			
				
			jQuery(nTr).attr('id',_obj.task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_end);			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});		
			
			// aps2012
			if(whichTable === 2){
				jQuery(nTr).find('td:eq(1) p').attr('title', 'deleted');
				jQuery(nTr).find('td:eq(1)').addClass('gen-deleted-icon').removeClass('gen-published-icon');								
			} 									
			
		} else if ( _obj.is_start === 1 && _obj.is_end === 1 ) { 	
		
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ _obj.task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="post.php?action=complete&post='+ _obj.task_id +'" title="Mark as complete">Complete</a>',
				'<p id="edit_title_'+ _obj.task_id +'">'+ _obj.task_title +'</p>',
				'<p id="edit_owner_'+ _obj.task_authid +'">'+ _obj.task_author +'</p>',
				'<p id="edit_sdate_'+ _obj.task_id +'" style="font-size: 10px; color: #999;">'+ _obj.task_start +'</p>',
				'<p id="edit_edate_'+ _obj.task_id +'" style="font-size: 10px; color: #999;">'+ _obj.task_end +'</p>',				
				'<p id="edit_progr_'+ _obj.task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'" ></progress></p>'
			);
			
			if (whichTable === 1){
				jQuery('#propel_completed_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-com-tasks').dataTable().fnAddData( _json );															
				var nTr = cTable.fnSettings().aoData[ a[0] ].nTr;			
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-checked-icon');
			}
			
			//aps2012 added deleted table
			else if (whichTable === 2){
				jQuery('#propel_deleted_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-deleted').dataTable().fnAddData( _json );															
				var nTr = dTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');				
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-deleted-icon');
			}			
			
			else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');
			}
			
			var _html = "";
			var len = _obj.task_content.length;
			if (len > 75 ) {
				var _content = _obj.task_content.substr(0,75)+' ...';
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}else{
				var _content = _obj.task_content.substr(0,75);
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}
			
				
			jQuery(nTr).attr('id',_obj.task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_start);
			jQuery(nTr).find('td:eq(5)').attr('data-value',_obj.task_end);			
			jQuery(nTr).find('td:eq(6)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(7)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});		
			
			// aps2012
			if(whichTable === 2){
				jQuery(nTr).find('td:eq(1) p').attr('title', 'deleted');
				jQuery(nTr).find('td:eq(1)').addClass('gen-deleted-icon').removeClass('gen-published-icon');								
			} 
			
		}else {
			//aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ _obj.task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="post.php?action=complete&post='+ _obj.task_id +'" title="Mark as complete">Complete</a>',				
				'<p id="edit_title_'+ _obj.task_id +'">'+ _obj.task_title +'</p>',
				'<p id="edit_owner_'+ _obj.task_authid +'">'+ _obj.task_author +'</p>',
				'<p id="edit_progr_'+ _obj.task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="'+ _obj.task_progress +'" ></progress></p>'
				);			

			if (whichTable === 1){
				jQuery('#propel_completed_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-com-tasks').dataTable().fnAddData( _json );															
				var nTr = cTable.fnSettings().aoData[ a[0] ].nTr;			
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-checked-icon');
			}
			
			//aps2012 added deleted table
			else if (whichTable === 2){
				jQuery('#propel_deleted_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-deleted').dataTable().fnAddData( _json );															
				var nTr = dTable.fnSettings().aoData[ a[0] ].nTr;
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');				
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-deleted-icon');
			}
			
			else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );	
				console.log(oTable.fnSettings().aoData[ a[0] ]);														
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;	
				jQuery(nTr).find('td:eq(1)').addClass('db-updated');		
				jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');
			}
						
			var _html = "";
			var len = _obj.task_content.length;
			if (len > 75 ) {
				var _content = _obj.task_content.substr(0,75)+' ...';
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}else{
				var _content = _obj.task_content.substr(0,75);
				_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
			}
			
				
			jQuery(nTr).attr('id',_obj.task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(5)').attr('data-value',_obj.task_progress);
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});
			
			// aps2012
			if(whichTable === 2){
				jQuery(nTr).find('td:eq(1) p').attr('title', 'deleted');	
				jQuery(nTr).find('td:eq(1)').addClass('gen-deleted-icon').removeClass('gen-published-icon');							
			} 
				
		}//End of if....
				
		//check_Existence();
		
		jQuery('.propeltooltip').each(function(){
			var _content = jQuery(this).attr('title');
			var _id = jQuery(this).attr('id');
			jQuery(this).propeltooltip({
				id		: _id,
				content : _content							
			});
		});				
				
	}
	
	function add_Data(){
		var _arrdata = [];
		var _cntdata = 0;
		var _html_author;
		
		jQuery('#task_contributor_list li').each(function(){
			if (jQuery(this).hasClass('propel_is_added')){	
				_arrdata[_cntdata] = jQuery(this).attr('id');
				var _contr_id = jQuery(this).attr('data-value');
				if ( _html_author === undefined){
					_html_author = '<span id="'+_contr_id+'" class="span_contr" style="padding:3px;">'+jQuery(this).attr('id')+'</span>';
				}else{
					_html_author +='<span id="'+_contr_id+'" class="span_contr" style="padding:3px;">'+jQuery(this).attr('id')+'</span>';
				}
				_cntdata++;
			}
		});	
		
		var data = {
			action: 'add_task',
			security: '<?php echo wp_create_nonce( "add-task" ); ?>',
			parent: '<?php echo get_the_ID(); ?>',
			title: jQuery('input[name=task_title]').val(),
			description: jQuery('textarea[name=task_description]').val(),
			end_date: jQuery('input[name=task_end_date]').val(),
			priority: jQuery('select[name=task_priority]').val(),
			user: _arrdata,			
		};
		//jQuery('#propel_post_author').val()

		var task_id = "<?php echo get_the_ID(); ?>";
		var task_authid = jQuery('#propel_post_author').val();
		var task_author = jQuery('#propel_post_author option:selected').text();
		var task_title = jQuery('input[name=task_title]').val();
		var task_content = jQuery('textarea[name=task_description]').val();
		var today = "<?php echo date('m-d-y H:i', time()); ?>";
		var task_end = jQuery('input[name=task_end_date]').val();						
		
		var _img = "<?php echo get_admin_url(); ?>";
		var is_start = "<?php echo Propel_Options::option('show_start_date' ); ?>";
		var is_end = "<?php echo Propel_Options::option('show_end_date' ); ?>";			

		var _html = "";
		var len = task_content.length;
		if (len > 75 ) {
			var _content = task_content.substr(0,75)+' ...';
			_html = '<div id="desc_" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
		}else{
			var _content = task_content.substr(0,75);
			_html = '<div id="desc_" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
		}
		
		if ( is_start == 1 && is_end != 1 ){
			//aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="#" title="Mark as complete">Complete</a>',
				'<p id="edit_title_">'+ task_title +'</p>',
				'<p id="edit_contr_">'+ _html_author +'</p>',
				'<p id="edit_sdate_" style="font-size: 10px; color: #999;">'+ today +'</p>',			
				'<p id="edit_progr_" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
			);		
			jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
			var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
			var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;			
			jQuery(nTr).attr('id',task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon db-updated');	
			jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');								
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value', task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(3)').prepend('<div class="saving" style="height:40px;width:40px; background:url('+ _img +'images/wpspin_light.gif) no-repeat 0 50%;margin-left:-20px;position:absolute;"></div>').css({ 'padding-left' : '20px' });						
			jQuery(nTr).find('td:eq(4)').attr('data-value', today );			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value', task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value', 0 );	
			jQuery(nTr).animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).animate({'backgroundColor':'transparent'},7000);							
			});									
		}else if ( is_start != 1 && is_end == 1 ){
			//aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="#" title="Mark as complete">Complete</a>',
				'<p id="edit_title_">'+ task_title +'</p>',
				'<p id="edit_contr_">'+ _html_author +'</p>',
				'<p id="edit_edate_" style="font-size: 10px; color: #999;">'+ task_end +'</p>',			
				'<p id="edit_progr_" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
			);
			jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
			var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
			var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;													
			jQuery(nTr).attr('id',task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon db-updated');	
			jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');								
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value', task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(3)').prepend('<div class="saving" style="height:40px;width:40px; background:url('+ _img +'images/wpspin_light.gif) no-repeat 0 50%;margin-left:-20px;position:absolute;"></div>').css({ 'padding-left' : '20px' });						
			jQuery(nTr).find('td:eq(4)').attr('data-value', task_end);			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value', task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value', 0 );	
			jQuery(nTr).animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).animate({'backgroundColor':'transparent'},7000);							
			});								
		}else if( is_start == 1 && is_end == 1 ){
			//aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="#" title="Mark as complete">Complete</a>',
				'<p id="edit_title_">'+ task_title +'</p>',
				'<p id="edit_contr_">'+ _html_author +'</p>',
				'<p id="edit_sdate_" style="font-size: 10px; color: #999;">'+ today +'</p>',
				'<p id="edit_edate_" style="font-size: 10px; color: #999;">'+ task_end +'</p>',				
				'<p id="edit_progr_" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
			);	
			jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
			var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
			var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;										
			jQuery(nTr).attr('id',task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon db-updated');	
			jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');								
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value', task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(3)').prepend('<div class="saving" style="height:40px;width:40px; background:url('+ _img +'images/wpspin_light.gif) no-repeat 0 50%;margin-left:-20px;position:absolute;"></div>').css({ 'padding-left' : '20px' });						
			jQuery(nTr).find('td:eq(4)').attr('data-value', today );
			jQuery(nTr).find('td:eq(5)').attr('data-value', task_end);			
			jQuery(nTr).find('td:eq(6)').addClass('owner').attr('data-value', task_author);
			jQuery(nTr).find('td:eq(7)').attr('data-value', 0 );	
			jQuery(nTr).animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).animate({'backgroundColor':'transparent'},7000);							
			});										
		}else {
			//aps2012
			var _json = Array(
				'<a href="javascript:;" class="propel_trashtask"  alt="'+ task_id+'" title="Delete">Delete</a>',
				'<p class="propeltooltip" title="published"></p>',
				'<a href="#" title="Mark as complete">Complete</a>',
				'<p id="edit_title_">'+ task_title +'</p>',
				'<p id="edit_owner_">'+ _html_author +'</p>',			
				'<p id="edit_progr_" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
			);	
			jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
			var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
			var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;										
			jQuery(nTr).attr('id',task_id);			
			jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-published-icon db-updated');	
			jQuery(nTr).find('td:eq(2)').addClass('gen-icon gen-unchecked-icon');								
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value', task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(3)').prepend('<div class="saving" style="height:40px;width:40px; background:url('+ _img +'images/wpspin_light.gif) no-repeat 0 50%;margin-left:-20px;position:absolute;"></div>').css({ 'padding-left' : '20px' });									
			jQuery(nTr).find('td:eq(4)').addClass('owner').attr('data-value', task_author);
			jQuery(nTr).find('td:eq(5)').attr('data-value', 0 );	
			jQuery(nTr).animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).animate({'backgroundColor':'transparent'},7000);							
			});										

		}													
		

		jQuery.post(ajaxurl, data, function(response) {
			 //rob_eyouth:added this...
			var _obj = jQuery.parseJSON(response);  
			var nonce = "<?php echo wp_create_nonce('propel-trash'); ?>";
			jQuery('.saving').fadeOut('slow',function(){
				jQuery(this).remove();			
				jQuery(nTr).attr('id',_obj.task_id);	
			   //aps2012 change
				jQuery(nTr).find('td:eq(0)').find('a').attr('href','javascript:;');
				jQuery(nTr).find('td:eq(0)').find('a').attr('alt', _obj.task_id);	
				jQuery(nTr).find('td:eq(2)').find('a').attr('href','post.php?action=complete&post='+ _obj.task_id);						
				jQuery(nTr).find('td:eq(3)').animate({ 'padding-left' : 0 },'slow');
				jQuery(nTr).find('td:eq(3)').find('p').attr('id','edit_title_'+_obj.task_id);
				if ( is_start == 1 && is_end != 1 ){
					jQuery(nTr).find('td:eq(4)').find('p').attr('id','edit_owner_'+_obj.task_id);											
					jQuery(nTr).find('td:eq(5)').find('p').attr('id','edit_sdate_'+_obj.task_id).html(_obj.task_start);																
					jQuery(nTr).find('td:eq(6)').find('p').attr('id','edit_progr_'+_obj.task_id);								
				}else if( is_start != 1 && is_end == 1 ){
					jQuery(nTr).find('td:eq(4)').find('p').attr('id','edit_owner_'+_obj.task_id);																		
					jQuery(nTr).find('td:eq(5)').find('p').attr('id','edit_edate_'+_obj.task_id).html(_obj.task_end);								
					jQuery(nTr).find('td:eq(6)').find('p').attr('id','edit_progr_'+_obj.task_id);																	
				}else if( is_start == 1 && is_end == 1 ){
					jQuery(nTr).find('td:eq(4)').find('p').attr('id','edit_owner_'+_obj.task_id);											
					jQuery(nTr).find('td:eq(5)').find('p').attr('id','edit_sdate_'+_obj.task_id).html(_obj.task_start);								
					jQuery(nTr).find('td:eq(6)').find('p').attr('id','edit_edate_'+_obj.task_id).html(_obj.task_end);								
					jQuery(nTr).find('td:eq(7)').find('p').attr('id','edit_progr_'+_obj.task_id);																	
				}else{
					jQuery(nTr).find('td:eq(4)').find('p').attr('id','edit_owner_'+_obj.task_id);																			
					jQuery(nTr).find('td:eq(5)').find('p').attr('id','edit_progr_'+_obj.task_id);								
				}
				jQuery('#edit_edate_'+ task_id).html(_obj.task_end);														
			});			
			
			 jQuery('#_task_title').val('');
			 jQuery('#_task_desc').val('');			 
			 
			 jQuery('#selected_task_contributor').find('li').remove();
			 jQuery('#task_contributor_list li').each(function(){
				if (jQuery(this).hasClass('propel_is_added')){	
					jQuery(this).removeClass().addClass('propel_not_added');
					jQuery(this).find('div').removeClass().addClass('add_contributor');
				}
			});	
			 				
		});
		
		//check_Existence();
			
		jQuery('.propeltooltip').each(function(){
			var _content = jQuery(this).attr('title');
			var _id = jQuery(this).attr('id');
			jQuery(this).propeltooltip({
				id		: _id,
				content : _content							
			});
		});		

	}
	
	jQuery.fn.propeltooltip = function(settings){
	
		var xOffset = 30;
		var yOffset = 20;
		var _title="";
		var task_id;
		var _divtooltip;
		
		var opts = jQuery.extend({ id : '', content : '' },settings);
		
		//return this.each(function() {
			
			var $this = jQuery(this);
					
			$this.live('mouseenter',function(e){
				
				_title = opts.content;
				$this.attr('title',"");
					
				jQuery("body").append("<p id='tooltips'><span class='arrow'></span>"+ _title +"</p>");
			
				e.pageX > 750 ?  e.pageX = 600 : e.pageX;
			
				jQuery("#tooltips")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset + 10) + "px")
					.fadeIn("slow");
					
			});
			
			$this.live('mouseleave',function(){
					$this.attr('title',_title);
					jQuery("#tooltips").fadeOut('fast');
					jQuery("#tooltips").remove();
			});
			
			$this.live('mousemove',function(e){		
					jQuery("#tooltips")
					.css("top",(e.pageY - 30) + "px")
					.css("left",(e.pageX + (yOffset)) + "px");
					
			}).live('click',function(){
	
					_divtooltip = $this.attr('id');
					task_id = jQuery.trim(_divtooltip.substr(5,10));
	
					jQuery("#tooltips").fadeOut('slow',function(){
						jQuery("#tooltips").remove();
						jQuery("#"+_divtooltip).find('small').hide('fast','linear',function(){
							
							jQuery("#"+_divtooltip).removeClass('propeltooltip').append('<textarea id="desc_edit_'+ task_id +'" style="width:400px; height:50px; margin-top:5px;font-size:10px;padding:5px;">'+ _title +'</textarea>');
						
						jQuery('#desc_edit_'+task_id).focus();	
							
						jQuery('#desc_edit_'+task_id).live('mouseleave',function(e) {
							jQuery('#desc_edit_'+task_id).fadeOut('fast',function(){
								jQuery('#desc_edit_'+task_id).remove();							
							});
							jQuery("#"+_divtooltip).addClass('propeltooltip').find('small').css("display","block").fadeIn('slow');
							
						})
						
						jQuery('#desc_edit_'+task_id).live('focusin',function(e) {
							jQuery("#tooltips").fadeOut('slow');
						});
						
						jQuery('#desc_edit_'+task_id).live('keypress',function(event){
							if ( event.which === 13 ){
										var data = {
												action: 'update_task',
												security: '<?php echo wp_create_nonce( "update-task" ); ?>',
												parent: '<?php echo get_the_ID(); ?>',
												postid: task_id,
												content: jQuery('#desc_edit_'+task_id).val(),
										};
			
										jQuery.post(ajaxurl, data, function(response) {
											var _obj = jQuery.parseJSON(response);
											jQuery('tr#'+task_id).fadeIn('slow',function(){
												var aPos = oTable.fnGetPosition( this );
												var _html ="";
												var len = _obj.task_content.length;
												if (len > 75 ) {
													var _content = _obj.task_content.substr(0,75)+' ...';
													_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
												}else{
													var _content = _obj.task_content.substr(0,75);
													_html = '<div id="desc_'+ _obj.task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ _obj.task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
												}			
												oTable.fnUpdate( '<p id="edit_title_'+ task_id +'">'+ _obj.task_title +'</p>'+_html, aPos, 3 );
												jQuery('.propeltooltip').each(function(){
														var _content = jQuery(this).attr('title');
														var _id = jQuery(this).attr('id');
														jQuery(this).propeltooltip({
															id		: _id,
															content : _content							
														});
													});
												});
				
										});
								
								jQuery("#"+_divtooltip).addClass('propeltooltip').find('small').css("display","block").fadeIn('slow');
								
							}//end of event == 13
							
						});//end of desc_edit
							
						});												
					});										
			});										   						 	
		   
		  //});//end of each
		  
		}; 		

    </script>
    
	<?php
	
	}
	
	//rob propeltooltip css
	public static function tooltip_css(){ ?>
		 <style>
		 	*{margin:0;padding:0}
			#tooltips {
				text-align:start;
				text-shadow: 1px 1px #EEE;
				text-wrap:normal !important;
				background: #DFDFDF;
				color: #555;
				display: block;
				padding: 10px;
				border : 1px solid #DDD;
				border-radius : 7px;
				-moz-border-radius : 7px;
				-webkit-border-radius : 7px;
				position: absolute;
				z-index:9999999999;
				height:auto;
				min-height:10px;
				width:auto;
				max-width:300px;
				box-sizing:border-box;
				-moz-box-sizing:content-box;
				-webkit-box-sizing:border-box;
				box-shadow: 1px 1px #CCC;
				overflow:none;
			}
			.propelnotify {
				text-align:start;
				text-shadow: 1px 1px 2px #f15c78;
				text-wrap:normal !important;
				background: #f87c94;
				color: #EEE;
				font-weight:bold;
				display: none;
				padding: 10px 20px 10px 10px;
				border : 1px solid #f15c78;
				border-radius : 4px;
				-moz-border-radius : 4px;
				-webkit-border-radius : 4px;
				position: absolute;
				z-index:9999999999;
				height:auto;
				min-height:10px;
				width:auto;
				max-width:400px;
				box-sizing:border-box;
				box-sizing:border-box;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;				
				box-shadow: 1px 1px #CCC;
				overflow:none;
				margin: -25px 0 0 25px;
			}			
			.arrow{
				width: 0; 
				height: 0; 
				top:10px;
				left:-10px;
				position:absolute;
				border-top: 10px solid transparent;
				border-bottom: 10px solid transparent; 				
				border-right:10px solid #DFDFDF; 	
			}
			.narrow{
				width: 0; 
				height: 0; 
				top:5px;
				left:-10px;
				position:absolute;
				border-top: 10px solid transparent;
				border-bottom: 10px solid transparent; 				
				border-right:10px solid #f87c94; 	
			}	
			#xclose {
				position: absolute !important;
				top: -5px;
				right: -5px;
				font-weight: bold;
				color: #CCC;
				text-shadow: 1px 1px #677;
				background: #555;
				padding: 0 5px 0 4px;
				border-radius: 8px;
				box-sizing:border-box;
				box-shadow: 1px 1px 2px #CCC;
				cursor:pointer;
			}		
			
			/*TimePicker*/
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			
			.gen-past-due-icon p {
				width: 0; 
				height: 0; 
				margin-left:3px;
				margin-top:3px;
				border-left: 6px solid transparent;
				border-right: 6px solid transparent;				
				border-bottom: 10px solid red;
				background:none;
			}
			
			.gen-published-icon p {
				width: 10px; 
				height: 10px; 
				margin-left:3px;
				margin-top:3px;
				background:#0F0;
				border-radius:2px;
				-moz-border-radius:2px;
				-webkit-border-radius:2px;				
			}	
			
			.gen-due-icon p {
				width: 10px; 
				height: 10px; 
				margin-left:3px;
				margin-top:3px;
				background:#FFA800;
				border-radius:5px;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
			}		
			
			/* aps2012 for deleted task */
			.gen-deleted-icon p {
				width: 10px; 
				height: 10px; 
				margin-left:3px;
				margin-top:3px;
				background:red;
				border-radius:5px;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
			}	
			
			#task_contributor_list{
			   display:none;
			   padding:0;
			   background:whiteSmoke;
			   border:1px solid #DDD;
			   position:absolute;
			   z-index:5;
			   border-bottom:none;
			   margin-top:2px;	
			}
			
			#task_contributor_list li{
				display:none;
				padding:5px 2px 5px 10px;
				border-bottom:1px solid #DDD;
				margin:0;
			}
			
			#task_contributor_list li.propel_is_added{
				color:#F00;
				font-weight:bold;
			}
			
			#task_contributor_list li.propel_not_added{
				color:#000;
			}
			
				#task_contributor_list li div.add_contributor{
					width:20px;
					height:20px;
					background: url('<?php echo plugins_url();?>/propel/images/details_open.png') no-repeat;
					float:right;
					clear:both;
					cursor: pointer;					
				}
				#task_contributor_list li div.del_contributor{
					width:20px;
					height:20px;
					background: url('<?php echo plugins_url();?>/propel/images/details_close.png') no-repeat;
					float:right;
					clear:both;
					cursor: pointer;
				}
			
			input#task_contributor{
				border:none;
			}	
			
			#selected_task_contributor{
				float: left;
			}
			
			#selected_task_contributor li{
				display: inline-block;
				background: #F0F0F0;
				padding: 1px 3px;
				margin: 1px;
				color:#3060cf;
				border-radius:2px 0 2px 0;
			}
			
			span.contributor_x{
				color:red;
				font-weight:bold;
				padding-left:3px;
				cursor: pointer;
			}
			
			td.owner p span{
				padding:2px;				
			}
			
			.metabox-add-task-contributor{
				display:none;
			}
			
			#propel_add_media{
				float:left;
				margin-top: -30px;
				z-index: 999;
				position: relative;
				margin-left: 5px;
			}
							
		 </style>
	<?php             
     }    

	/**
	 * @since 2.0
	 * rob: added this function..
	 */
	public static function project_get_task( $id ) {
		$data = new stdClass;
		$task = get_post($id); 		
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$priority = get_post_meta( $task->ID, '_propel_priority', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		$post_created = $task->post_date;
		$post_modified = $task->post_modified;
		//if( $start )
		//$start = date( get_option( 'date_format' ), $start );

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		
		if( $end ){								
			$day   = date('d'); // Day of the countdown
			$month = date('m'); // Month of the countdown
			$year  = date('Y'); // Year of the countdown
			$hour  = date('H'); // Hour of the day (east coast time)
			
			$calculation = ( $end - time() ) / 3600;
			$hours = (int)$calculation + 24;
			$days  = (int)( $hours / 24 ) - 1;
			
			$hours_remaining = $hours-($days*24)-24;
			
			if ( $hours < 0 && $hours > -24 ) {
				$data->task_status = "due";
				self::auto_notify($post_id,'task-due');
			}else if ( $hours < -24 ) {
				$data->task_status = "past-due";
			}else{
				$data->task_status = "published";
			}			
		}

		if( $task->post_author ) {
			$userdata = get_userdata( $task->post_author );
			$authid = $userdata->ID; 
			$author = $userdata->display_name;
		} else {
			$authid = '-1';
			$author = "Unassigned";
		}	
			
		$x = ($progress == 100) ? "" : "un";
		$nonce = wp_create_nonce('propel-trash');
		if ($post_created !== $post_modified):
		  $data->is_updated = 0;
		else: 
		  $data->is_updated = 1;		
		endif; 
		$data->task_id=  $task->ID;
		$data->task_title = $task->post_title;
		$data->task_author = $author;
		$data->task_nonce = $nonce;
		$data->task_progress = $progress;
		$data->task_authid = $authid;
		$data->task_author = $author;
		$data->task_content = $task->post_content;
		
		if( Propel_Options::option('show_start_date' ) ) :
			$data->is_start = 1;
			$data->task_start = date("m-d-y h:i a", (int)$start);
		else:
			$data->is_start = 0;
			$data->task_start = 0;				
		endif;
		
		if( Propel_Options::option('show_end_date' ) ) : 
			$data->is_end = 1;
			$data->task_end = date("m-d-y h:i a", (int)$end);
		else:
			$data->is_end = 0;
			$data->task_end = 0;		
		endif; 		

		echo json_encode($data);
					
		remove_action( 'project_get_task', array( __CLASS__, 'project_get_task' ) );
		
		die();		
	}	
	/**
	 * aps2012 
	 */
	public static function wp_ajax_restore_task() {
		
		$postval = array();
		$postval['ID'] =  $_POST['postid'];
		$postval['post_status'] = 'publish';
		wp_update_post((int)$postval);
		do_action( 'project_get_task', $_POST['postid']);	
		
	}
		/**
	 * aps2012 
	 */
	public static function wp_ajax_trash_task() {
		
		if ( isset( $_POST['postid'] ) ){
			$post_id = $_POST['postid'];
		}
		$postval = array();
		$postval['ID'] =  (int)$_POST['postid'];
		$postval['post_status'] = 'trash';
		
		wp_update_post($postval);
		self::auto_notify($post_id,'trash');
		do_action( 'project_get_task', $_POST['postid']);			
		
	}
	/**
	 * aps2012 physical delete
	 */
	public static function wp_ajax_delete_task() {
		
		wp_delete_post((int)$_POST['postid']);
		
	}		
	/**
	 * @since 2.0
	 * added by rob : 
	 */
	public static function wp_ajax_update_task() {
		
		check_ajax_referer( 'update-task', 'security' );
		
		$post_id = $_POST['postid'];
		
		if ( isset($_POST['pID']) ){
			$pid = $_POST['pID'];
			do_action('project_get_task',$pid);
			die($pid);
		}

		if ( isset($_POST["title"]) && isset($_POST["content"]) ){
			$post = array(
					'ID' => (int)$post_id,
					'post_title' => $_POST['title'],
					'post_content' => $_POST['content'],
					'post_parent' => $_POST['parent'],
				);

			wp_update_post( stripslashes_deep($post) );
			
			self::auto_notify($post_id,'task-update');
						
		}else if ( isset($_POST["title"]) && !isset($_POST["content"]) ) {
			$post = array(
				'ID' => (int)$post_id,
				'post_title' => $_POST['title'],
				'post_parent' => $_POST['parent'],
			);
			
			wp_update_post( stripslashes_deep($post) );
			self::auto_notify($post_id,'task-update');
			
		}else if ( !isset($_POST["title"]) && isset($_POST["content"]) ) {
			$post = array(
				'ID' => (int)$post_id,
				'post_content' => $_POST['content'],
				'post_parent' => $_POST['parent'],
			);
			
			wp_update_post( stripslashes_deep($post) );
			self::auto_notify($post_id,'task-update');
						
		}
		
		if( isset( $_POST['user'] ) ){
			$post = array(
				'ID' => (int)$post_id,
				'post_author' => (int)$_POST['userval']
			);
			wp_update_post( $post );		
			$usercnt = get_post_meta((int)$post_id,'_propel_user',true);			
			$olduser = get_userdata((int)$_POST['user']);
			for ($i =0; $i < $usercnt; $i++){
				$user = get_post_meta((int)$post_id,'_propel_user_'.$i,true);			
				if ( $olduser->user_login == $user ){
					$newuser = get_userdata((int)$_POST['userval']);
					update_post_meta( (int)$post_id, '_propel_user_'.$i, $newuser->user_login );
				}
			}
			//aps
			if((int)$_POST['user'] = -1){
				self::auto_notify($post_id,'unassign');
		    } else {
				self::auto_notify($post_id,'assign');
		    }
		}	
			
		if ( isset($_POST['start_date']) ){	
			$start = !empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : time();		
			update_post_meta( (int)$post_id, '_propel_start_date', $start );
		}
		
		if ( isset($_POST['end_date']) ){	
			$end = strtotime($_POST['end_date']);
			if( empty( $_POST['end_date'] ) && $_POST['complete'] == 100  ) {
				$end = time();
			}
			
			update_post_meta( (int)$post_id, '_propel_end_date', $end );
			self::auto_notify( (int)$post_id,'task-due');
		}
		
		if ( isset( $_POST['priority'] ) ){
			update_post_meta( (int)$post_id, '_propel_priority', (int)$_POST['priority'] );
		}
		
		if ( isset( $_POST['complete'] ) ){
			update_post_meta( (int)$post_id, '_propel_complete', (int)$_POST['complete'] );
			// aps
		 	if ( isset( $_POST['complete'] ) && (int)$_POST['complete'] == 100 ){
				self::auto_notify( (int)$post_id,'complete');				
	 	  	}
		}	
		
		do_action('project_get_task', (int)$post_id);
		
		die($post_id);
	}

	/**
	* aps
	* notify users when complete
	*/
	public static function auto_notify($post_id, $type ) {
    global $post, $wpdb;
		if( Propel_Options::get_option('email_notifications') ) { 
			$post = get_post( $post_id );
			$parent = get_post( $post->post_parent );
			$domain =  preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); 
			$current_user = wp_get_current_user();
			$post_owner = get_userdata( $post->post_author );
			$end = get_post_meta( $post_id, '_propel_end_date', true );
			//$headers = "From: $current_user->display_name <donotreply@$domain_name>" . "\r\n";
			if($type == 'complete'){
			$subject = "Task Completed: ".$post->post_title;
		    $message .= "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$current_user->display_name has updated the project as 100% complete &#34;$parent->post_title&#34; project:</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'new-assign'){
			  $subject = "New Task: ".$post->post_title;
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>New Task Assigned to you {$post->post_title}.</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'task-update'){
			  $subject = "Task Modified: ".$post->post_title;
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3> The task {$post->post_title} has been modified by $current_user->display_name.</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'assign'){
			  $subject = "Task Reassigned to ".$post_owner->user_login." : ".$post->post_title;
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$current_user->display_name re-assigned the following task to $post_owner->user_login on the &#34;$post->post_title&#34; project:</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'unassign'){
			  $subject = "Reassignment Notification: ".$post->post_title; //"Task is UnAssigned ($parent->post_title): $post->post_title";
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>{$current_user->display_name} has reassigned the following task to {$post_owner->user_login}:</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'trash'){
			  $subject = "Task Deleted: ".$post->post_title;
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>{$current_user->display_name} has deleted the following on the {$post->post_title} project:</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'task-due'){
			  $subject = "Task Due Date Change to ".$end." for ".$post->post_title;
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>The following task due date was changed to {$end}</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			}			
			
			if($post_id) {
			
					$coauthors = array();
					$defaults = array( 'orderby' => 'term_order', 'order' => 'ASC' );
					$args = wp_parse_args( $args, $defaults );
					$coauthor_terms = wp_get_post_terms( $post_id, 'author', $args );
					
					if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
						foreach($coauthor_terms as $coauthor) {			
							$post_author =  get_user_by( 'login', $coauthor->name );
							if(!empty($post_author)) $coauthors[] = $post_author;			
						}
						//wp_mail('robertopanes@theportlandcompany.com', 'is_array($coauthor_terms)', $post_author->ID, '');
						$usercnt = get_post_meta( $post_id, '_propel_user',true );
						foreach( $coauthors as $login ) {
							//$user = get_userdata( $login );						 
							//add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
							//wp_mail($login->user_email, $subject, $message, $headers); 							
							for ($i =0; $i < $usercnt; $i++ ){
								$userlogin = get_post_meta( $post_id, '_propel_user_'.$i, true );
								//$author =  get_user_by( 'login', $userlogin );
								if ( $userlogin == $login->user_login ){
									add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));					
									wp_mail($login->user_email, $subject, $message, $headers); 				
								}								
							}
							
						}	
					} else {

						$usercnt = get_post_meta( $post_id, '_propel_user',true );	
						for ($i =0; $i < $usercnt; $i++ ){
							$userlogin = get_post_meta( $post_id, '_propel_user_'.$i, true );
							$author =  get_user_by( 'login', $userlogin );
							add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));							
							wp_mail($author->user_email, $subject, $message, $headers); 			
						}	
						
					}
												
			}			
		 
		}  //End of email option	
	}
	
	
	/**
	 * @since 2.0
	 * rob: added this function..
	*/
	public static function wp_ajax_get_task_detail() {
 		
		check_ajax_referer( 'get-task-detail', 'security' );
		
		if (isset($_POST["postid"])){
			
			$id = $_POST["postid"];
	
				switch($_POST['retnum']){	
				
					case 1:
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
							?>
								<tr id='details-<?php esc_attr_e($id); ?>'>
                                <td colspan="3"></td>
                                <td colspan="3"><select>
                            <?php
								foreach($posts as $post) {
									$task = get_post($id);
									echo $task->ID;
								}
							?>
								</select></td></tr>;		
							<?php
                            die();
							break;
					case 2:	
							$task = get_post($id);
							if( $task->post_author ) {
								$userdata = get_userdata( $task->post_author );
								$authid = $userdata->ID; 
								$author = $userdata->display_name;
							} else {
								$authid = '-1';
								$author = "Unassigned";
							}
							$args = array(
							'class' => 'metabox-add-task-user',
							'name' => 'propel_post_author',
							'show_option_none' => 'Unassigned',
							'orderby' => 'display_name',
							'selected' => $userdata->ID
							);
							wp_dropdown_users( $args );
							die(0);
							break; 
					case 3:
				?>
						<select name="complete" id="propel_edit_progress">
							<?php
							for ($i = 0; $i <= 100; $i = $i+5) :
								echo "<option value='$i'>$i</option>";
							endfor;
							?> 
						</select>            
				<?php		
						die();
						break;
						
					default:
						die();
						break;
			}
			
		}
					
	}
	
	/*
	* added by rob: ajax call for updates to user
	*
	*/
	public static function wp_ajax_get_update() {		
		check_ajax_referer( 'get-update', 'security' );
		global $wpdb;
		$parent = $_POST['parent']; 
		$curDate = date("Y-m-d h:i:s");		
		//@todo: profile query / use WP_Query?
		$query = "SELECT `ID`, `post_date`, `post_modified`, `post_status`, `post_title`, `post_content`, `post_author`, `meta_value` AS `progress`,  CURDATE() as today
		    	FROM `{$wpdb->postmeta}` JOIN `{$wpdb->posts}` ON `post_id` = `ID`
		        WHERE `meta_key` = '_propel_complete' 
		        AND `meta_value` < 100 AND `{$wpdb->postmeta}`.`post_id` 
		        	IN (SELECT `ID` FROM {$wpdb->posts}
		        	WHERE `post_parent`={$parent} AND (`post_status` = 'publish' OR `post_status` = 'trash'))
		        ORDER BY `meta_value` DESC, `post_id` DESC";

		$posts = $wpdb->get_results($query);		
		echo json_encode($posts);
		die();
	}
	
	public static function wp_ajax_check_update() {	
		check_ajax_referer( 'check-update', 'security' );
		global $wpdb;
		$postid = $_POST['pID']; 
		$curDate = date("Y-m-d h:i:s");
		//@todo: profile query / use WP_Query?
		$query = "SELECT `ID`, `post_date`, `post_modified`, `post_status` FROM {$wpdb->posts}
		        	WHERE `ID`={$postid} AND `post_modified` >= {$curDate} ";
		$posts = $wpdb->get_results($query);		
		echo json_encode($posts);
		die();	
	}
		
} //End of class

?>