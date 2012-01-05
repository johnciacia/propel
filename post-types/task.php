<?php
/**
 * @todo create completed category / meta information - log when the task is marked complete
 * @todo add a clear button for dates - http://bugs.jqueryui.com/ticket/3999
 * @todo implement filtering for project, priority, and contributor
 */


Post_Type_Task::init();

class Post_Type_Task {
	
	const POST_TYPE = 'propel_task';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( __CLASS__, 'register_sortable_columns' ) );
		add_filter( 'parse_query', array( __CLASS__, 'parse_query' ) );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_columns', array( __CLASS__, 'register_columns' ) );
		add_action( 'wp_ajax_get_task_description', array( __CLASS__, 'wp_ajax_get_task_description' ) );
		add_filter( 'default_hidden_meta_boxes', array( __CLASS__, 'default_hidden_meta_boxes' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( __CLASS__, 'quick_edit_custom_box' ), 10, 2 );
		add_filter( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 10, 2 );
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer' ) );
	}
 
	public static function admin_footer() {
		global $current_screen;

		?>
		<script type="text/javascript">
		<!--
		function propel_set_inline_values(complete, priority, nonce) {
			inlineEditPost.revert();
			var widgetInput = document.getElementById('post_complete');
			var nonceInput = document.getElementById('propel_nonce');
			nonceInput.value = nonce;
			for (i = 0; i < widgetInput.options.length; i++) {
				if (widgetInput.options[i].value == complete) { 
					widgetInput.options[i].setAttribute("selected", "selected"); 
				} else { widgetInput.options[i].removeAttribute("selected"); }
			}
			widgetInput = document.getElementById('post_priority');
			for (i = 0; i < widgetInput.options.length; i++) {
				if (widgetInput.options[i].value == priority) { 
					widgetInput.options[i].setAttribute("selected", "selected"); 
				} else { widgetInput.options[i].removeAttribute("selected"); }
			}
		}
		//-->
		</script>
		<?php
	}
 
	public static function post_row_actions($actions, $post) {
		global $current_screen;
	 
		$nonce = wp_create_nonce( plugin_basename( __FILE__ ) );
		$complete = get_post_meta( $post->ID, '_propel_complete', true );
		$priority = get_post_meta( $post->ID, '_propel_priority', true );
		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
		$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
		$actions['inline hide-if-no-js'] .= " onclick=\"propel_set_inline_values('{$complete}', '{$priority}', '{$nonce}')\">"; 
		$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
		$actions['inline hide-if-no-js'] .= '</a>';

		return $actions;	
	}

	public static function quick_edit_custom_box($column_name, $post_type) {
		if ($column_name != 'complete') return;
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<span class="title">Progress</span>
				<select name='complete' id='post_complete'>
				<?php 
				for ($i = 0; $i <= 100; $i = $i+5) {
					echo "<option class='complete' value='$i'>$i%</option>";
				}
				?>
				</select>
			</div>
			<div class="inline-edit-col">
				<span class="title">Priority</span>
				<select name='priority' id='post_priority'>
					<option value="0">Low</option>
					<option value="1">Medium</option>
					<option value="2">High</option>
				</select>
			</div>
			<input type="hidden" name="propel_nonce" id="propel_nonce" value="" />
    	</fieldset>
	<?php
	}


	/**
	 *
	 */
	public static function wp_ajax_get_task_description() {
		$post = get_post($_POST['id']);
	 	echo $post->post_content;
	 	die();
	}

	/**
	 * @since 2.0
	 */
	public static function admin_menu() {
		remove_meta_box( 'postcustom', 'propel_task', 'core' );
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

		if( isset( $_GET['project'] ) ) {
			$query->query_vars['post_parent'] = (int)$_GET['project'];
			return $query;
		}

		return $query;
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
		
		$start = !empty( $_POST['start_date'] ) ? strtotime( $_POST['start_date'] ) : time();
		update_post_meta( $post_id, '_propel_start_date', $start );

		$end = strtotime($_POST['end_date']);
		if( empty( $_POST['end_date'] ) && $_POST['complete'] == 100  ) {
			$end = time();
		}
		update_post_meta( $post_id, '_propel_end_date', $end );

		update_post_meta( $post_id, '_propel_priority', (int)$_POST['priority'] );
		update_post_meta( $post_id, '_propel_complete', (int)$_POST['complete'] );
	}

	public static function register_taxonomy() {

		$labels = array(
			'name' => _x( 'States', 'taxonomy general name' ),
			'singular_name' => _x( 'States', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search states' ),
			'all_items' => __( 'All States' ),
			'parent_item' => __( 'Parent State' ),
			'parent_item_colon' => __( 'Parent State:' ),
			'edit_item' => __( 'Edit State' ), 
			'update_item' => __( 'Update State' ),
			'add_new_item' => __( 'Add New State' ),
			'new_item_name' => __( 'New State Name' ),
			'menu_name' => __( 'States' )); 	

		register_taxonomy( 'propel_status', 'propel_project', array(
			'public' => false,
			'labels' => $labels,
			// 'show_ui' => true,
			// 'query_var' => true,
		) );

		$state = array( 'Not Yet Started', 'Started', 'Finished', 'Delievered', 'Accepted', 'Rejected' );

		foreach($state as $status) {
			if( !term_exists($status, 'propel_status')) {
				wp_insert_term($status, 'propel_status');
			}
		}

		$labels = array(
			'name' => _x( 'Type', 'taxonomy general name' ),
			'singular_name' => _x( 'Types', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search types' ),
			'all_items' => __( 'All Types' ),
			'parent_item' => __( 'Parent Type' ),
			'parent_item_colon' => __( 'Parent Type:' ),
			'edit_item' => __( 'Edit Type' ), 
			'update_item' => __( 'Update Type' ),
			'add_new_item' => __( 'Add New Type' ),
			'new_item_name' => __( 'New Type Name' ),
			'menu_name' => __( 'Types' )); 	

		register_taxonomy( 'propel_type', 'propel_task', array(
			'public' => false,
			'labels' => $labels,
			// 'show_ui' => true,
			// 'query_var' => true,
			// 'show_in_nav_menus' => true,

		) );

		$type = array( 'Feature', 'Bug' );

		foreach($type as $t) {
			if( !term_exists($t, 'propel_type')) {
				wp_insert_term($t, 'propel_type');
			}
		}
	}

	/**
	 * @since 2.0
	 */
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
			'show_ui' => (wp_count_posts('propel_project')->publish > 0) ? true : false, 
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

		$argz = array(
			'post_type' => 'propel_task',
			'action' => 'complete',
			'label' => 'Complete' );
		Propel_Functions::add_post_action( $argz, array( __CLASS__, 'action_complete' ) );
	}

	public static function default_hidden_meta_boxes( $hidden, $screen ) {
		if($screen->id == 'propel_project') {
			$hidden[] = 'postcustom';
		}

		if( $screen->id == 'propel_task') {
			$hidden[] = 'custom-fields';
		}

		return $hidden;
	}

	/**
	 * @since 2.0
	 */
	public static function register_columns($columns) {
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x( 'Task Name', 'column name' );
		$new_columns['project'] = __( 'Project', 'propel' );
		$new_columns['author'] = __( 'Owner', 'propel' );
		if( Propel_Options::option('show_start_date' ) )
			$new_columns['start'] = __( 'Start Date', 'propel' );
		if( Propel_Options::option('show_end_date' ) ) 
			$new_columns['end'] = __( 'End Date', 'propel' );
		$new_columns['priority'] = __( 'Priority', 'propel' );
		$new_columns['type'] = __( 'Type', 'propel' );
		$new_columns['complete'] = __( 'Progress', 'propel' );
		$new_columns['propel_categories'] = __( 'Categories', 'propel' );
		$new_columns['tags'] = $columns['tags'];
		$new_columns['comments'] = $columns['comments'];
		return $new_columns;
	}

	/**
	 * @since 2.0
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
				echo "<a href='edit.php?post_type=propel_task&project=" . $project->ID . "'>" . $project->post_title . "</a>";
				break;

			case 'type':
				$terms = wp_get_post_terms( $id, 'propel_type' );
				if( is_array( $terms ) )
					echo $terms[0]->name;
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
			
			case 'propel_categories':
				$categories = get_the_terms(0, "propel_category");
				$categories_html = array();
				if(is_array($categories)) {
					foreach ($categories as $category) {
						array_push($categories_html, '<a href="' . get_term_link($category->slug, 'propel_category') . '">' . $category->name . '</a>');
					}
					echo implode($categories_html, ", ");
        		}
        		break;

			default:
				break;
		}
	}

	/**
	 * @since 2.0
	 */
	public static function add_meta_boxes() {
		add_meta_box('custom-fields', __('Custom Fields'),
			'post_custom_meta_box', self::POST_TYPE, 'normal', 'low');

		add_meta_box( 'propel_task_meta', __( 'Task', 'propel' ),
			array( __CLASS__, 'edit_task_meta'), self::POST_TYPE, 'side' );
	}

	/**
	 * @since 2.0
	 */
	public static function edit_task_meta() {
		wp_nonce_field( plugin_basename( __FILE__ ), 'propel_nonce' );

		$start = get_post_meta( get_the_ID(), '_propel_start_date', true );
		if($start)
			$start = date("M. jS, Y", $start);

		$end = get_post_meta( get_the_ID(), '_propel_end_date', true );
		if($end)
			$end = date("M. jS, Y", $end);

		$priority = get_post_meta( get_the_ID(), '_propel_priority', true );
		if(!$priority)
			$priority = 0;

		$complete = get_post_meta( get_the_ID(), '_propel_complete', true );
		if(!$complete)
			$complete = 0;

		$post = get_post( get_the_ID() );
		$parent = $post->post_parent;

		$types = wp_get_post_terms( get_the_ID(), 'propel_type' );
		if( is_array( $types ) && isset( $types[0] ) ) {
			$type = $types[0]->term_id;
		} else {
			$type = 0;
		}

		require_once( dirname(__FILE__) . '/../metaboxes/task-meta.php' );
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
	}

}