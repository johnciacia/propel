<?php
/**
 * @todo add bulk action to archive tasks
 * @todo create completed category / meta information - log when the task is marked complete
 * @todo add a clear button for dates - http://bugs.jqueryui.com/ticket/3999
 * @todo implement filtering for project, priority, and contributor
 * @todo add ability to assign tasks to users
 * @todo dispatch email when project is assigned to user
 * @todo add ability to assign multiple contributors
 * @todo add action/bulk action to mark tasks as complete
 */

Post_Type_Task::init();

class Post_Type_Task {
	
	const POST_TYPE = 'propel_task';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'register_sortable_columns' ) );
		add_filter( 'parse_query', array( __CLASS__, 'parse_query' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_columns', array( __CLASS__, 'register_columns' ) );
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

		if( isset($_GET['post_status'] ) && $_GET['post_status'] == "archive" ) {
			$query->query_vars['post_type'] = "propel_time";
			$query->query_vars['post_status'] = "archive";
		}

	}


	public static function admin_footer() {
		if(isset($_GET['post'])) :
			$post = get_post($_GET['post']);
			if( $post->post_type == self::POST_TYPE) :
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('archive').text('Archive').appendTo("select[name='post_status']");
				});
			</script>
			<?php
			endif;
		endif;

		if(isset($_GET['post_type']) && $_GET['post_type'] != self::POST_TYPE) return;
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("<li>").html(" | <a href='edit.php?post_status=archive&post_type=propel_task'>Archived <span class='count'>(0)</span></a>").appendTo('.subsubsub')
			});
		</script>
		<?php
	}

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

	}

	public static function register_post_type()  {
 		$labels = array(
			'name' => _x('Tasks', 'post type general name'),
    		'singular_name' => _x('Tasks', 'post type singular name'),
    		'add_new' => _x('Add New', 'propel'),
    		'add_new_item' => __('Add New Task'),
    		'edit_item' => __('Edit Task'),
    		'new_item' => __('New Task'),
    		'all_items' => __('All Tasks'),
    		'view_item' => __('View Task'),
    		'search_items' => __('Search Tasks'),
    		'not_found' =>  __('No tasks found'),
    		'not_found_in_trash' => __('No tasks found in Trash'), 
    		'parent_item_colon' => '',
    		'menu_name' => 'Propel');

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => 'edit.php?post_type=propel_project', 
			'query_var' => true,
			'rewrite' => true,
			'taxonomies' => array('propel_category', 'post_tag'),
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','comments', 'author', 'custom-fields', 'revisions')); 
		
		register_post_type(self::POST_TYPE, $args);

		register_post_status( 'archive' );
	}

	/**
	 * @since 2.0
	 * @see http://shibashake.com/wordpress-theme/add-custom-post-type-columns
	 */
	public static function register_columns($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x( 'Project Name', 'column name' );
		$new_columns['project'] = __( 'Project', 'propel' );
		$new_columns['start'] = __( 'Start Date', 'propel' );
		$new_columns['end'] = __( 'End Date', 'propel' );
		$new_columns['priority'] = __( 'Priority', 'propel' );
		$new_columns['complete'] = __( '%' );
		$new_columns['categories'] = $columns['categories'];
		$new_columns['tags'] = $columns['tags'];
		$new_columns['comments'] = $columns['comments'];
		return $new_columns;
	}

	/**
	 * @since 2.0
	 * @see http://scribu.net/wordpress/custom-sortable-columns.html
	 */
	public static function register_sortable_columns( $x ) {
		$columns['start'] = 'start';
		$columns['end'] = 'end';
		$columns['project'] = 'project';
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

			case 'project':
				$id = get_post( $id );
				$project = get_post( $id->post_parent );
				echo "<a href='edit.php?post_type=propel_project&project=" . $project->ID . "'>" . $project->post_title . "</a>";
				break;

			case 'start':
				$date = get_post_meta( $id, '_propel_start_date', true );
				if($date) {
					echo date( "Y-m-d" , $date );
				}
				break;

			case 'end':
				$date = get_post_meta( $id, '_propel_end_date', true );
				if($date) {
					echo date( "Y-m-d" , $date );
				}
				break;

			case 'priority':
				echo get_post_meta( $id, '_propel_priority', true );
				break;

			case 'complete':
				echo get_post_meta( $id, '_propel_complete', true );
				break;

			default:
				break;
		}
	}

	public static function add_meta_boxes() {
		add_meta_box( 'propel_task_meta', __( 'Task', 'propel' ),
			array( __CLASS__, 'edit_task_meta'), 'propel_task', 'side' );
	}

	public function edit_task_meta() {
		wp_nonce_field( plugin_basename( __FILE__ ), 'propel_nonce' );

		$start = get_post_meta( get_the_ID(), '_propel_start_date', true );
		if($start)
			$start = date("Y-m-d", $start);

		$end = get_post_meta( get_the_ID(), '_propel_end_date', true );
		if($end)
			$end = date("Y-m-d", $end);

		$priority = get_post_meta( get_the_ID(), '_propel_priority', true );
		if(!$priority)
			$priority = 0;

		$complete = get_post_meta( get_the_ID(), '_propel_complete', true );
		if(!$complete)
			$complete = 0;

		$post = get_post( get_the_ID() );
		$parent = $post->post_parent;

		require_once( __DIR__ . '/../metaboxes/task-meta.php' );
	}
}

?>