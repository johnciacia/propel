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
		
		var oTable;
		
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
					
					jQuery(".date").datepicker({ dateFormat: 'MM dd, yy' });
					
					jQuery('#propel_edit_task').css({ 'display':'none' });
					jQuery('#propel_add_task').css({ 'display':'block' });
					
					jQuery("#add-task").click(function(e) {	
								
						var data = {
							action: 'add_task',
							security: '<?php echo wp_create_nonce( "add-task" ); ?>',
							parent: '<?php echo get_the_ID(); ?>',
							title: jQuery('input[name=task_title]').val(),
							description: jQuery('textarea[name=task_description]').val(),
							end_date: jQuery('input[name=task_end_date]').val(),
							priority: jQuery('select[name=task_priority]').val(),
							user: jQuery('#propel_post_author').val()
						};

						var task_id = "<?php echo get_the_ID(); ?>";
						var task_authid = jQuery('#propel_post_author').val();
						var task_author = jQuery('#propel_post_author option:selected').text();
						var task_title = jQuery('input[name=task_title]').val();
						var task_content = jQuery('textarea[name=task_description]').val();
						var today = "<?php echo date("F j, Y"); ?>";
						var task_end = jQuery('input[name=task_end_date]').val();						
						
						var _img = "<?php echo get_admin_url(); ?>";
						
						var _json = Array(
							'<a href="#" title="Delete">Delete</a>',
							'<a href="#" title="Edit">Edit</a>',
							'<a href="#" title="Mark as complete">Complete</a>',
							'<p id="edit_title_'+ task_id +'">'+ task_title +'</p>',
							'<p id="edit_owner_'+ task_id +'">'+ task_author +'</p>',
							'<p id="edit_sdate_'+ task_id +'" style="font-size: 10px; color: #999;">'+ today +'</p>',
							'<p id="edit_edate_'+ task_id +'" style="font-size: 10px; color: #999;">'+ task_end +'</p>',				
							'<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
						);						
										
						jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
						var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
						var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;			

						
						var _html = "";
						var len = task_content.length;
						if (len > 75 ) {
							var _content = task_content.substr(0,75)+' ...';
							_html = '<div id="desc_'+ task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
						}else{
							var _content = task_content.substr(0,75);
							_html = '<div id="desc_'+ task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
						}
							
						jQuery(nTr).attr('id',task_id);			
						jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
						jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');	
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
						
		
						jQuery.post(ajaxurl, data, function(response) {
							 //rob_eyouth:added this...
							var _obj = jQuery.parseJSON(response);  
							var nonce = "<?php echo wp_create_nonce('propel-trash'); ?>";
							jQuery('.saving').fadeOut('slow',function(){
								jQuery(this).remove();			
								jQuery(nTr).find('td:eq(0)').find('a').attr('href','post.php?action=propel-delete&post='+ _obj.task_id +'&_wpnonce='+nonce);	
								jQuery(nTr).find('td:eq(2)').find('a').attr('href','post.php?action=complete&post='+ _obj.task_id);						
								jQuery(nTr).find('td:eq(3)').animate({ 'padding-left' : 0 },'slow');																	
							});			
							 jQuery('#_task_title').val('');
							 jQuery('#_task_desc').val('');			 				
						});
							
						return false;			
					});
		
					//rob_eyouth : added this to remove the deleted data from the current task table
					 jQuery("#propel-tasks tbody td.gen-delete-icon").live('click',function(){
						 var $parent = jQuery(this).parent();
						 var aPos = oTable.fnGetPosition( this );
						 var _href = jQuery(this).find('a').attr('href');								 
						 $parent.find('td')
								 .wrapInner('<div style="display: block;" />')
								 .parent()
								 .find('td > div')
								 .slideUp(700, function(){	
								  $parent.remove();						 
						 });	
						 jQuery.post(_href,function() {	
							 oTable.fnDeleteRow(aPos[0]);
						 });
						 return false;
					 });
					 
					 jQuery("#propel-com-tasks tbody td.gen-delete-icon").live('click',function(){
						 var $parent = jQuery(this).parent();
						 var _href = jQuery(this).find('a').attr('href');								 
						 							
						 $parent.find('td')
								 .wrapInner('<div style="display: block;" />')
								 .parent()
								 .find('td > div')
								 .slideUp(700, function(){
								  $parent.remove();	 
						 });	
						 jQuery.post(_href,function() {	
							 //$parent.remove();
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
									propel_post_author: jQuery('#propel_post_author').val()
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
					
					jQuery("#propel_project_tasks #propel-tasks tbody tr td p").live('click',function(){
							 
							var task_id = jQuery(this).closest('tr').attr('id');					
							var this_id = jQuery(this).attr('id');
							var $this = $(this);
							var _this_id;
							this_id === undefined ? _this_id = 0 : _this_id = this_id.substr(0,10);
							var _tr_before_id = jQuery('#'+task_id).prev('tr').attr('id');							
							
							
							
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
															if ( _obj.is_start === 1 && _obj.is_end === 0 ) { 																
																oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );													
																jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);											
															} else if ( _obj.is_start === 0 && _obj.is_end === 1 ) { 
																oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );
																jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);
															} else if ( _obj.is_start === 1 && _obj.is_end === 1 ) { 
																oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );															
																jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);
															}else {
																oTable.fnUpdate( '<p id="edit_owner_'+ task_id +'">'+ _obj.task_author +'</p>', aPos, 4 );
																jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_author);
															}
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
					
					jQuery("#propel_project_tasks .gen-edit-icon").each(function(index, element) {
                        
						jQuery(this).live('click',function(event){
							//console.log('test')	  
//							var task_id = jQuery(this).attr('id');					
//						
//							if(jQuery('#details-' + task_id).length > 0) {
//								
//								jQuery('#details-' + task_id).remove();
//								
//							} else {
//		
//								var data = {
//										action: 'get_task_detail',
//										security: '',
//										postid: task_id,
//										retnum: 1
//								};
//				
//								jQuery.post(ajaxurl, data, function(response) {
//									 jQuery("#" + task_id).fadeIn('slow',function(){
//										 jQuery(this).closest( "tr" ).after(response);																			
//									 });
//								});
//
//							}
							 
							return false;
						});					
					});	
					
					jQuery('form#post #propel_add_task').live('keypress',function(event){
						if (event.which === 13){

						var data = {
							action: 'add_task',
							security: '<?php echo wp_create_nonce( "add-task" ); ?>',
							parent: '<?php echo get_the_ID(); ?>',
							title: jQuery('input[name=task_title]').val(),
							description: jQuery('textarea[name=task_description]').val(),
							end_date: jQuery('input[name=task_end_date]').val(),
							priority: jQuery('select[name=task_priority]').val(),
							user: jQuery('#propel_post_author').val()
						};

						var task_id = "<?php echo get_the_ID(); ?>";
						var task_authid = jQuery('#propel_post_author').val();
						var task_author = jQuery('#propel_post_author option:selected').text();
						var task_title = jQuery('input[name=task_title]').val();
						var task_content = jQuery('textarea[name=task_description]').val();
						var today = "<?php echo date("F j, Y"); ?>";
						var task_end = jQuery('input[name=task_end_date]').val();						
						
						var _img = "<?php echo get_admin_url(); ?>";
						
						var _json = Array(
							'<a href="#" title="Delete">Delete</a>',
							'<a href="#" title="Edit">Edit</a>',
							'<a href="#" title="Mark as complete">Complete</a>',
							'<p id="edit_title_'+ task_id +'">'+ task_title +'</p>',
							'<p id="edit_owner_'+ task_id +'">'+ task_author +'</p>',
							'<p id="edit_sdate_'+ task_id +'" style="font-size: 10px; color: #999;">'+ today +'</p>',
							'<p id="edit_edate_'+ task_id +'" style="font-size: 10px; color: #999;">'+ task_end +'</p>',				
							'<p id="edit_progr_'+ task_id +'" style="font-size:10px;color:#999;"><progress max="100" value="" ></progress></p>'
						);						
										
						jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
						var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
						var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;			

						
						var _html = "";
						var len = task_content.length;
						if (len > 75 ) {
							var _content = task_content.substr(0,75)+' ...';
							_html = '<div id="desc_'+ task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
						}else{
							var _content = task_content.substr(0,75);
							_html = '<div id="desc_'+ task_id +'" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="'+ task_content +'"><small style="color:#999;text-shadow:1px 1px white">'+ _content +'</small></div>';
						}
							
						jQuery(nTr).attr('id',task_id);			
						jQuery(nTr).find('td:eq(0)').addClass('gen-icon gen-delete-icon');
						jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');	
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
						
		
						jQuery.post(ajaxurl, data, function(response) {
							 //rob_eyouth:added this...
							var _obj = jQuery.parseJSON(response);  
							var nonce = "<?php echo wp_create_nonce('propel-trash'); ?>";
							jQuery('.saving').fadeOut('slow',function(){
								jQuery(this).remove();			
								jQuery(nTr).find('td:eq(0)').find('a').attr('href','post.php?action=propel-delete&post='+ _obj.task_id +'&_wpnonce='+nonce);	
								jQuery(nTr).find('td:eq(2)').find('a').attr('href','post.php?action=complete&post='+ _obj.task_id);						
								jQuery(nTr).find('td:eq(3)').animate({ 'padding-left' : 0 },'slow');																	
							});			
							 jQuery('#_task_title').val('');
							 jQuery('#_task_desc').val('');			 				
						});
							
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
										
	});//End of document.ready    

	function get_JSON(response,whichTable){

		var _obj = jQuery.parseJSON(response);
		
		if ( _obj.is_start === 1 && _obj.is_end === 0 ) { 	
					
			var _json = Array(
				'<a href="post.php?action=propel-delete&post='+_obj.task_id+'&_wpnonce='+_obj.task_nonce+'" title="Delete">Delete</a>',
				'<a href="#" title="Edit">Edit</a>',
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
			}else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
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
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_start);			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});							
			
		} else if ( _obj.is_start === 0 && _obj.is_end === 1 ) { 	
		
			var _json = Array(
				'<a href="post.php?action=propel-delete&post='+_obj.task_id+'&_wpnonce='+_obj.task_nonce+'" title="Delete">Delete</a>',
				'<a href="#" title="Edit">Edit</a>',
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
			}else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
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
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_end);			
			jQuery(nTr).find('td:eq(5)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(6)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});							
			
		} else if ( _obj.is_start === 1 && _obj.is_end === 1 ) { 	
		
			var _json = Array(
				'<a href="post.php?action=propel-delete&post='+_obj.task_id+'&_wpnonce='+_obj.task_nonce+'" title="Delete">Delete</a>',
				'<a href="#" title="Edit">Edit</a>',
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
			}else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );															
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;
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
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);				
			jQuery(nTr).find('td:eq(4)').attr('data-value',_obj.task_start);
			jQuery(nTr).find('td:eq(5)').attr('data-value',_obj.task_end);			
			jQuery(nTr).find('td:eq(6)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(7)').attr('data-value',_obj.task_progress);	
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});		
			
		}else {
			
			var _json = Array(
				'<a href="post.php?action=propel-delete&post='+_obj.task_id+'&_wpnonce='+_obj.task_nonce+'" title="Delete">Delete</a>',
				'<a href="#" title="Edit">Edit</a>',
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
			}else{
				jQuery('#propel_project_tasks tbody #no-data').css('border','none').hide();	
				var a = jQuery('#propel-tasks').dataTable().fnAddData( _json );	
				console.log(oTable.fnSettings().aoData[ a[0] ]);														
				var nTr = oTable.fnSettings().aoData[ a[0] ].nTr;			
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
			jQuery(nTr).find('td:eq(1)').addClass('gen-icon gen-edit-icon');			
			jQuery(nTr).find('td:eq(3)').addClass('title').attr('data-value',_obj.task_title).css({"width":"400px"}).find('p').after(_html);					
			jQuery(nTr).find('td:eq(4)').addClass('owner').attr('data-value',_obj.task_author);
			jQuery(nTr).find('td:eq(5)').attr('data-value',_obj.task_progress);
			jQuery(nTr).stop().animate({'backgroundColor':'#0F3'},'slow',function(){ 
				jQuery(nTr).stop().animate({'backgroundColor':'transparent'},7000);
			});
				
		}//End of if....
		
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
				min-height:50px;
				width:auto;
				min-width:100px;
				max-width:300px;
				box-sizing:border-box;
				box-shadow: 1px 1px #CCC;
				overflow:none;
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
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
			.ui-timepicker-div dl { text-align: left; }
			.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
			.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
			.ui-timepicker-div td { font-size: 90%; }
			.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
			
		 </style>
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
		//if( $start )
			//$start = date( get_option( 'date_format' ), $start );

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		//if( $end )
			//$end = date( get_option( 'date_format' ), $end);

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
			$data->task_start = date("m-d-y H:i", (int)$start);
		else:
			$data->is_start = 0;
			$data->task_start = 0;				
		endif;
		
		if( Propel_Options::option('show_end_date' ) ) : 
			$data->is_end = 1;
			$data->task_end = date("m-d-y H:m", (int)$end);
		else:
			$data->is_end = 0;
			$data->task_end = 0;		
		endif; 		

		echo json_encode($data);
					
		remove_action( 'project_get_task', array( __CLASS__, 'project_get_task' ) );
		
		die();		
	}	
	
	/**
	 * @since 2.0
	 * added by rob : 
	 */
	public static function wp_ajax_update_task() {
		
		check_ajax_referer( 'update-task', 'security' );
		
		$post_id = $_POST['postid'];

		if ( isset($_POST["title"]) && isset($_POST["content"]) ){
			$post = array(
					'ID' => (int)$post_id,
					'post_title' => $_POST['title'],
					'post_content' => $_POST['content'],
					'post_parent' => $_POST['parent'],
				);

			wp_update_post( $post );
			
		}else if ( isset($_POST["title"]) && !isset($_POST["content"]) ) {
			$post = array(
				'ID' => (int)$post_id,
				'post_title' => $_POST['title'],
				'post_parent' => $_POST['parent'],
			);
			wp_update_post( $post );
			
		}else if ( !isset($_POST["title"]) && isset($_POST["content"]) ) {
			$post = array(
				'ID' => (int)$post_id,
				'post_content' => $_POST['content'],
				'post_parent' => $_POST['parent'],
			);
			wp_update_post( $post );
			
		}
		
		if( isset( $_POST['user'] ) ){
			$post = array(
				'ID' => (int)$post_id,
				'post_author' => (int)$_POST['user']
			);
			wp_update_post( $post );		
			//aps
			self::auto_notify($post_id,'assign');
		}	
			
		if ( isset($_POST['start_date']) ){	
			$start = !empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : time();		
			update_post_meta( $post_id, '_propel_start_date', $start );
		}
		
		if ( isset($_POST['end_date']) ){	
			$end = strtotime($_POST['end_date']);
			if( empty( $_POST['end_date'] ) && $_POST['complete'] == 100  ) {
				$end = time();
			}
			
			update_post_meta( $post_id, '_propel_end_date', $end );
		}
		
		if ( isset( $_POST['priority'] ) ){
			update_post_meta( $post_id, '_propel_priority', (int)$_POST['priority'] );
		}
		if ( isset( $_POST['complete'] ) ){
			update_post_meta( $post_id, '_propel_complete', (int)$_POST['complete'] );
			// aps
		 	if (isset( $_POST['complete'] ) && ($_POST['complete'] == 100)){
				self::auto_notify($post_id,'complete');
	 	  	}
		}
		
		do_action('project_get_task',$post_id);
		
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
			$headers = "From: $current_user->display_name <donotreply@$domain_name>" . "\r\n";
			if($type == 'complete'){
			$subject = "Task is Completed ($parent->post_title): $post->post_title";
		    $message .= "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$current_user->display_name has updated the project as 100% complete &#34;$parent->post_title&#34; project:</h3>
					<p><b>&#34;<a href='$post->guid' style='color: #1E8CBE;'>$post->post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$post->post_content&#34;</p>
				</div>
			";
			} elseif($type == 'assign'){
			  $subject = " - Task is Re-Assigned ($parent->post_title): $post->post_title";
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$current_user->display_name re-assigned the following to $post_owner->user_login on the &#34;$post->post_title&#34; project:</h3>
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
					// In case the user has been deleted while plugin was deactivated
					if(!empty($post_author)) $coauthors[] = $post_author->ID;
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
			
   			foreach(array_unique($coauthors) as $login ) {
						$user = get_userdata( $login );
						add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
						wp_mail($user->user_email, $subject, $message, $headers);
					}
		}
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
	
		
} //End of class

?>