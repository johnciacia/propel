<?php
/**
 * This plugin will allow you to assign multiple authors to a project or task.
 * A large portion of this code has been borrowed from the Co-Authors Plus plugin
 * (http://wordpress.org/extend/plugins/co-authors-plus/)
 *
 * @todo: add option to enable / disable pre_get_posts. after the initial import every author should
 * be added as a coauthor. this will allow for this plugin to be enabled and disabled seamlessly.
 * @todo: move list-authors.php into this file
 * @todo: when a user is deleted projects are not reassigned appropratly 
 * @todo: make distinction between task owner and contributors more clear
 * @todo: add tool to bulk add / remove contributors
 * @todo: create an import tool. all authors should be added as contributors. 
 * should this happen each time the plugin is enabled?
 */
Propel_Authors::initialize();

class Propel_Authors {

	const COAUTHOR_TAXONOMY = 'author';

	/**
	 *
	 */
	public static function initialize() {
		add_filter( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
		add_action( 'delete_user',  array( __CLASS__, 'delete_user' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_filter( 'manage_edit-propel_project_columns', array( __CLASS__, 'register_columns' ) );
		add_filter( 'manage_edit-propel_task_columns', array( __CLASS__, 'register_columns' ) );
		add_action( 'manage_propel_project_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'manage_propel_task_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'comment_post', array( __CLASS__, 'comment_post' ) );
		add_filter( 'views_edit-propel_task', array( __CLASS__, 'views_edit_post' ) );
		add_filter( 'views_edit-propel_project', array( __CLASS__, 'views_edit_post' ) );
		add_action( 'post_wp_ajax_add_task', array( __CLASS__, 'post_wp_ajax_add_task' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}

	public static function admin_menu() {
		remove_meta_box( 'authordiv', 'propel_project', 'normal' );
		remove_meta_box( 'authordiv', 'propel_task', 'normal' );
	}

	public static function post_wp_ajax_add_task( $post_id ) {
		$post = get_post( $post_id );
		$user = get_userdata( $post->post_author );
		$coauthors = array( $user->user_login );

		$project_managers = self::get_coauthors( $post->post_parent );
		foreach( $project_managers as $project_manager ) {
			$coauthors[] = $project_manager->user_login;
		}
		$coauthors = array_unique( $coauthors );

		self::add_coauthors( $post_id, $coauthors );
	}

	public static function comment_post( $comment_ID ) {
		$comment = get_comment( $comment_ID );
		$post = get_post( $comment->comment_post_ID );
		$parent = get_post( $post->post_parent );
		if( $post->post_type == "propel_task" ) {
			$subject = "NEW COMMENT ($parent->post_title): $post->post_title";
			$message = "Hello,\n\n";
			$message .= "$comment->comment_author commented on the task '$post->post_title':\n";
			$message .= "$comment->comment_content\n";
			$coauthors = wp_get_post_terms( $post->ID, self::COAUTHOR_TAXONOMY );
			foreach($coauthors as $login) {
				$user = get_user_by( 'login', $login->slug );
				wp_mail($user->user_email, $subject, $message);
			}
		}
	}

	public static function register_columns( $columns ) {
		$columns = array_slice($columns, 0, 4, true) +
    		array('contributor' => __( 'Contributors', 'propel' )) +
    		array_slice($columns, 4, count($columns) - 1, true) ;
		return $columns;
	}

	public static function get_coauthors( $post_id = 0, $args = array() ) {
		global $post, $post_ID, $coauthors_plus, $wpdb;

		$coauthors = array();
		$post_id = (int)$post_id;
		if( !$post_id && $post_ID ) $post_id = $post_ID;
		if( !$post_id && $post ) $post_id = $post->ID;

		$defaults = array( 'orderby' => 'term_order', 'order' => 'ASC' );
		$args = wp_parse_args( $args, $defaults );

		if($post_id) {
			$coauthor_terms = wp_get_post_terms( $post_id, self::COAUTHOR_TAXONOMY, $args );

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

	public static function manage_columns($column_name, $id) {
		if( $column_name == 'contributor' )	{
			$authors = self::get_coauthors( $id );
			$count = 1;
			foreach( $authors as $author ) :
				?>
				<a href="edit.php?author=<?php echo $author->ID; ?>"><?php echo $author->display_name ?></a><?php echo ( $count < count( $authors ) ) ? ',' : ''; ?>
				<?php
				$count++;
			endforeach;
		}
	}

	public static function init() {
		register_taxonomy( self::COAUTHOR_TAXONOMY, null,
			array('hierarchical' => false,
				'update_count_callback' => '_update_post_term_count',
				'label' => false,
				'query_var' => false,
				'rewrite' => false,
				'sort' => true,
				'show_ui' => false) 
			);
	}

	public static function add_meta_boxes() {
		add_meta_box( 'propel_list_authors', __( 'Contributors' ),
			array( __CLASS__, 'list_authors'), 'propel_project', 'side' );
		add_meta_box( 'propel_list_authors', __( 'Contributors' ),
			array( __CLASS__, 'list_authors'), 'propel_task', 'side' );
	}

	public static function list_authors() {
		require_once( dirname(__FILE__) . '/../metaboxes/list-authors.php')	;
	}

	/**
	 *
	 */
	public static function pre_get_posts( $query ) {
		$types = array( 'propel_task', 'propel_project' );
		if( !isset( $query->query_vars['post_type']) ) return $query;
		if( !in_array( $query->query_vars['post_type'], $types ) )  return $query;

		global $user_ID;
		$user = get_userdata( $user_ID );
		$query->set( 'taxonomy', 'author' );
		$query->set( 'term', $user->user_login );
		return $query;
	 }


	public static function wp_insert_post_data( $data ) {
		// Bail on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && !DOING_AUTOSAVE )
			return $data;

		// Bail on revisions
		if( $data['post_type'] == 'revision' )
			return $data;

		if( isset( $_REQUEST['coauthors-nonce'] ) && is_array( $_POST['coauthors'] ) ) {
			$author = $_POST['coauthors'][0];
			if( $author ) {
				$author_data = get_user_by( 'login', $author );
				$data['post_author'] = $author_data->ID;
			}
		} else {
			// If for some reason we don't have the coauthors fields set
			if( ! isset( $data['post_author'] ) ) {
				$user = wp_get_current_user();
				$data['post_author'] = $user->ID;
			}
		}

		return $data;
	}

	public static function save_post($post_id, $post) {
		global $typenow;
		/**
		 * Sanity checks
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		$post_type = $post->post_type;
		if( isset( $_POST['coauthors-nonce'] ) && isset( $_POST['coauthors'] ) ) {
			check_admin_referer( 'coauthors-edit', 'coauthors-nonce' );
			$coauthors = (array) $_POST['coauthors'];
			$coauthors = array_map( 'esc_html', $coauthors );

			//if a contributor is added/removed from a project, add/remove to/from 
			//ALL THE TASKS associated with that project
			if( 'propel_project' == $typenow ) {
				$posts = get_posts( array( 'post_type' => 'propel_task', 'post_parent' => $post_id ) );
				foreach( $posts as $post ) {
					self::add_coauthors( $post->ID, $coauthors );
				}
			}

			//add project contributors to new tasks
			if( 'propel_task' == $typenow ) {
				$project_managers = self::get_coauthors( $post->post_parent );
				foreach( $project_managers as $project_manager ) {
					$coauthors[] = $project_manager->user_login;
				}
				$coauthors = array_unique( $coauthors );
			}

			return self::add_coauthors( $post_id, $coauthors );
		}
	}

	/**
	 * $post_id int 
	 * $coauthors array 
	 * $append bool
	 * $notify bool
	 */
	public static function add_coauthors( $post_id, $coauthors, $append = false, $notify = true ) {
		global $current_user, $post;

		$notify = array();
		$post_id = (int) $post_id;
		$insert = false;

		if ( !is_array( $coauthors ) || 0 == count( $coauthors ) || empty( $coauthors ) ) {
			$coauthors = array( $current_user->user_login );
		}

		$terms = wp_get_post_terms( $post_id, self::COAUTHOR_TAXONOMY );

		foreach( array_unique( $coauthors ) as $author ) {
			$name = $author;
			if( !term_exists( $name, self::COAUTHOR_TAXONOMY ) ) {
				$args = array( 'slug' => sanitize_title( $name ) );
				$insert = wp_insert_term( $name, self::COAUTHOR_TAXONOMY, $args );
			}

			if( !has_term( $name, self::COAUTHOR_TAXONOMY, $post_id ) ){
				$notify[] = $name;
			}
		}

		if( !is_wp_error( $insert ) ) {
			$set = wp_set_post_terms( $post_id, $coauthors, self::COAUTHOR_TAXONOMY, $append );
		}

		if( $notify ) {
			self::notify_coauthors($notify);
		}
	}

	/**
	 * When a user is deleted, remove the term information and reassign
	 * if requested.
	 */
	public static function delete_user( $delete_id ) {
		global $wpdb;

		$reassign_id = absint( $_POST['reassign_user'] );

		if($reassign_id) {
			$reassign_user = get_user_by( 'id', $reassign_id );
			if( $reassign_user ) {
				$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $delete_id ) );

				if ( $post_ids ) {
					foreach ( $post_ids as $post_id ) {
						self::add_coauthors( $post_id, array( $reassign_user->user_login ), true, false );
					}
				}
			}
		}

		$delete_user = get_user_by( 'id', $delete_id );
		if( $delete_user ) {
			wp_delete_term( $delete_user->user_login, self::COAUTHOR_TAXONOMY );
		}
	}

	//email when
	//- assigned to a task
	//- unassigned a task
	//- task was updated (exclude users from the aforementioned two)
	//- comment made
	public static function notify_coauthors( $to ) {
		global $post;

		$parent = get_post( $post->post_parent );
		$subject = "ASSIGNMENT ($parent->post_title): $post->post_title";
		foreach( $to as $login ) {
			$user = get_user_by( 'login', $login );
			$message = "Hello $user->user_nicename,\n\n";
			$message .= "The task '$post->post_title' is now assigned to you.\n";
			$message .= "$post->guid";
			wp_mail($user->user_email, $subject, $message);
		}
	}


	public static function views_edit_post( $views ) {
		global $wpdb, $avail_post_stati, $typenow;
		if( $typenow != 'propel_project' && $typenow != 'propel_task' ) return $views;

		$user = wp_get_current_user();

		$query = "SELECT P.post_status, COUNT(*) AS num_posts FROM {$wpdb->terms} AS T 
			LEFT JOIN {$wpdb->term_taxonomy} AS TT ON T.term_id = TT.term_id 
			LEFT JOIN {$wpdb->term_relationships} AS TR ON TT.term_taxonomy_id = TR.term_taxonomy_id
			LEFT JOIN {$wpdb->posts} AS P ON TR.object_id = P.id
			WHERE T.name = (SELECT U.user_login FROM {$wpdb->users} AS U WHERE U.ID = {$user->ID}) 
			AND TT.taxonomy = 'author'
			AND P.post_type = '{$typenow}'
			GROUP BY P.post_status";

		//@todo cache $count
		$count = $wpdb->get_results( $query, ARRAY_A );

		$stats = array();
		foreach ( get_post_stati() as $state )
			$stats[$state] = 0;

		foreach ( (array) $count as $row )
			$stats[$row['post_status']] = $row['num_posts'];


		$num_posts = (object)$stats;


		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();



		$total_posts = array_sum( (array) $num_posts );


		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$typenow{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$typenow'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}

		return $status_links;
	}
}

?>