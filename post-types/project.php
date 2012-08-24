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
		add_action( 'wp_ajax_update_task', array( __CLASS__, 'wp_ajax_update_task' ) );
		add_action( 'wp_ajax_get_task_detail', array( __CLASS__, 'wp_ajax_get_task_detail' ) );
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
		update_post_meta( $post_id, '_propel_owner', (int)$_POST['propel_post_author'] );

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
		do_action( 'project_get_task', $id);		

		die($id);
	}

	/**
	 * @since 2.0
	 */
	public static function admin_footer() { ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) { 			
			
			$(".date").datepicker();
			
			$('#propel_edit_task').css({ 'display':'none' });
			$('#propel_add_task').css({ 'display':'block' });
			
			$("#add-task").click(function(e) {				
				var data = {
					action: 'add_task',
					security: '<?php echo wp_create_nonce( "add-task" ); ?>',
					parent: '<?php echo get_the_ID(); ?>',
					title: $('input[name=task_title]').val(),
					description: $('textarea[name=task_description]').val(),
					end_date: $('input[name=task_end_date]').val(),
					priority: $('select[name=task_priority]').val(),
					user: $('#propel_post_author').val()
				};

				jQuery.post(ajaxurl, data, function(response) {
					 //rob_eyouth:added this...
					$('#propel_project_tasks #post_parent_').after(response,function(){
						$('#propel_project_tasks #propel-tasks tbody').fadeIn('slow');
					});						 				
				});
					
				return false;			
			});

			//rob_eyouth : added this to remove the deleted data from the current task table
			 $("#propel-tasks tbody td.gen-delete-icon").live('click',function(){
				 var $parent = $(this).parent();
				 $parent.fadeOut('slow',function(){
					var _href = $(this).find('a').attr('href');
					jQuery.post(_href,function() {
						$parent.remove();	
					});
					 
				 });
				 return false;
			 });
			
			//rob_eyouth : added this to remove the checked data from the current task table
			// and added to the completed task table
		  $("#propel-tasks tbody td.gen-unchecked-icon").live('click',function(){
					var task_id = $(this).parent().attr('id'); 
					var $parent = $(this).parent();
					var data = {
							action: 'update_task',
							security: '<?php echo wp_create_nonce( "update-task" ); ?>',
							postid: task_id,
							end_date: '<?php echo time(); ?>',
							priority: $('#propel_edit_prior').val(),
							complete: 100,
							propel_post_author: $('#propel_post_author').val()
					};
	
					jQuery.post(ajaxurl, data, function(response) {						 
						$parent.fadeOut('slow',function(){
							$parent.remove();
							$('#propel_completed_tasks #post_parent_').after(response,function(){
								$('#propel_project_tasks #propel-tasks tbody').fadeIn('slow');
							});						 																						
						})																								 				
					});

				 return false;
			 });
			
			$("#propel_project_tasks #propel-tasks tbody tr.toggle").live('click',function(event){
					  
 				    var task_id = $(this).attr('id');					
				
					if(jQuery('#details-' + task_id).length > 0) {
						
						jQuery('#details-' + task_id).remove();
						
					} else {

						var data = {
								action: 'get_task_detail',
								security: '<?php echo wp_create_nonce( "get-task-detail" ); ?>',
								postid: task_id,
						};
		
						jQuery.post(ajaxurl, data, function(response) {
							 $("#" + task_id).fadeIn('slow',function(){
								 $(this).closest( "tr" ).after( response );																			
							 });
						});
					}
					 
				return false;
			}).live('mouseenter',function(){
               $(this).css({'cursor':'pointer'}); 
            }).live('mouseleave',function(){
               $(this).css({'cursor':'default'}); 
            });
			
			$('#propel_task_update').live('click',function(){
				
					var _task_id = $.trim($(this).closest('tr').attr('id').substr(8,5));
					var _tr_id = $(this).closest('tr').attr('id');
					var _tr_before_id = $('#'+_task_id).prev('tr').attr('id');

					var data = {
							action: 'update_task',
							security: '<?php echo wp_create_nonce( "update-task" ); ?>',
							parent: '<?php echo get_the_ID(); ?>',
							postid: _task_id,
							end_date: '<?php echo time(); ?>',
							title: $('#propel_edit_task').val(),
							priority: $('#propel_edit_prior').val(),
							complete: $('#propel_edit_progress').val(),
							user: $('#propel_edit_author').val()
					};
	
					jQuery.post(ajaxurl, data, function(response) {
						$('#'+_task_id).fadeOut('slow',function(){
							$(this).remove();
							$('#'+_tr_id).fadeOut('slow',function(){
								$(this).remove();
							});
							if ( parseInt($('#propel_edit_progress').val()) < 100 ) {
								if ( _tr_before_id === undefined )
									$("#propel_project_tasks #propel-tasks tbody").prepend(response).fadeIn('slow');
								else
									$("#" + _tr_before_id).closest( "tr" ).after( response ).fadeIn('slow');
							} else {
								$('#propel_completed_tasks #propel-tasks tbody').prepend(response).fadeIn('slow');										
							}
						})																								 				
					});

				 return false;
			});			
			
			jQuery('#propel_project_tasks .metaboxes-add-task').css({ 'border':'1px solid #DFDFDF', 'padding':'10px', 'margin':'10px 0' });
			jQuery('#propel_post_author').addClass('task-priority');
		})    
    </script>
	<?php
		
	}

	/**
	 * @since 2.0
	 * rob: added this function..
	 */
	public static function project_get_task( $id ) {
		$task = get_post($id); 		
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$priority = get_post_meta( $task->ID, '_propel_priority', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		if( $start )
			$start = date( get_option( 'date_format' ), $start );

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		if( $end )
			$end = date( get_option( 'date_format' ), $end);

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

		?>        
		<tr class="toggle" id="<?php esc_attr_e( $task->ID ); ?>">
		
			<td class="gen-icon gen-delete-icon">
				<a href="post.php?action=propel-delete&post=<?php esc_attr_e( $task->ID ); ?>&_wpnonce=<?php echo $nonce; ?>" title="Delete">Delete</a></td>

			<td class="gen-icon gen-edit-icon">
				<a href="post.php?post=<?php esc_attr_e( $task->ID ); ?>&action=edit" title="Edit">Edit</a></td>

			<td class="gen-icon gen-<?php echo $x; ?>checked-icon">
				<a href="post.php?action=complete&post=<?php esc_attr_e( $task->ID ); ?>" title="Mark as complete">Complete</a></td>
				
			<td class="title" class="toggle" data-value="<?php esc_attr_e($task->post_title); ?>" style="width: 400px;">
				<p><?php esc_html_e($task->post_title); ?></p></td>

			<td class="owner" data-value="<?php esc_attr_e( $author ); ?>">
				<p><?php esc_html_e($author); ?></p>
			</td>

			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $start ); ?>">
				<p style="font-size: 10px; color: #999;"><?php esc_html_e($start); ?></p>
			</td>
			<?php endif; ?>

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $end ); ?>">
				<p style="font-size: 10px; color: #999;"><?php esc_html_e($end); ?></p></td>
			<?php endif; ?>

			<td data-value="<?php esc_attr_e( $progress ); ?>">
				<p><?php esc_html_e($progress); ?>%</p></td>
		</tr>
        
		<?php		
		remove_action( 'project_get_task', array( __CLASS__, 'project_get_task' ) );
	}	
	
	/**
	 * @since 2.0
	 * added by rob : 
	 */
	public static function wp_ajax_update_task() {
		
		check_ajax_referer( 'update-task', 'security' );
		
		$post_id = $_POST['postid'];

		if ( isset($_POST["title"]) ){
			$post = array(
				'ID' => (int)$post_id,
				'post_title' => $_POST['title'],
				'post_parent' => $_POST['parent'],
				'post_type' => 'propel_task',
				'post_status' => 'publish'
			);
			wp_update_post( $post );		
		}
			
		$start = !empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : time();
		
		update_post_meta( $post_id, '_propel_start_date', $start );

		$end = strtotime($_POST['end_date']);
		if( empty( $_POST['end_date'] ) && $_POST['complete'] == 100  ) {
			$end = time();
		}
		
		update_post_meta( $post_id, '_propel_end_date', $end );
		
		if ( isset( $_POST['priority'] ) )
			update_post_meta( $post_id, '_propel_priority', (int)$_POST['priority'] );
		
		if ( isset( $_POST['complete'] ) )
			update_post_meta( $post_id, '_propel_complete', (int)$_POST['complete'] );
		
		if ( isset( $_POST['user'] ) )
			update_post_meta( $post_id, '_propel_owner', (int)$_POST['user'] );
		
		do_action('project_get_task',$post_id);
		
		die($post_id);
	}
	
	/**
	 * @since 2.0
	 * rob: added this function..
	*/
	public static function wp_ajax_get_task_detail() {
		$id = $_POST["postid"];
		$task = get_post($id); 		
		$nonce = wp_create_nonce('propel-trash');
		if( $task->post_author ) {
			$userdata = get_userdata( $task->post_author );
			$authid = $userdata->ID; 
			$author = $userdata->display_name;
		} else {
			$authid = '-1';
			$author = "Unassigned";
		}
		?>        
		<tr id="details-<?php esc_attr_e( $task->ID ); ?>">
			        
            <td class="gen-icon gen-edit-icon" colspan="3"> <input type="button" class="button-primary" value="Update" id="propel_task_update"> </td>
			<td data-value="<?php esc_attr_e($task->post_title); ?>" class="propel_editable">
				<p><input type="text" value="<?php esc_html_e($task->post_title); ?>" id="propel_edit_task"/></p>
            </td>		

			<td data-value="<?php esc_attr_e( $authid ); ?>" class="propel_editable">
				<p>
					 <?php
						$args = array(
							'name' => 'propel_edit_author',
							'show_option_none' => 'Unassigned',
							'orderby' => 'display_name',
							'name' => 'propel_edit_author', 
							'selected' => $project->post_author
						);
						wp_dropdown_users( $args );
                    ?>                        
                </p>
            </td>
			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
                <td></td>
            <?php endif; ?>
            
            <?php if( Propel_Options::option('show_end_date' ) ) : ?>
                <td></td>
            <?php endif; ?>
			<td data-value="<?php esc_attr_e( $progress ); ?>" class="propel_editable">
				<p>
				    <select name="complete" id="propel_edit_progress">
                        <?php
                        for ($i = 0; $i <= 100; $i = $i+5) :
                            echo "<option value='$i'".selected($complete, $i).">$i</option>";
                        endfor;
                        ?> 
                    </select>            
                </p>
            </td>			           
			
        </tr>	
        
		<?php		
	}
	
		
} //End of class

?>