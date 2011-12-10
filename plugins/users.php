<?php
/**
 * This plugin will allow you to assign multiple authors to a project or task.
 * A large portion of this code has been borrowed from the Co-Authors Plus plugin
 * (http://wordpress.org/extend/plugins/co-authors-plus/)
 *
 * @todo: change the location of the Contributors column
 * @todo: add option to enable / disable pre_get_posts
 * @todo: when using pre_get_posts update the numbers at the top of the page
 * @todo: move list-authors.php into this file
 * @todo: adding a coauthor to a task makes them a coauthor of the project?
 */
WP_Post_Contributors::initialize();

class WP_Post_Contributors {

	const COAUTHOR_TAXONOMY = 'author';

	/**
	 *
	 */
	public static function initialize() {
		add_filter( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
		add_action( 'delete_user',  array( __CLASS__, 'delete_user_action' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_filter( 'manage_edit-propel_project_columns', array( __CLASS__, 'register_columns' ) );
		add_filter( 'manage_edit-propel_task_columns', array( __CLASS__, 'register_columns' ) );
		add_action( 'manage_propel_project_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
		add_action( 'manage_propel_task_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
	}

	public static function register_columns($columns) {
		$columns['contributor'] = __( 'Contributors', 'propel' );
		return $columns;
	}

	public static function get_coauthors( $post_id = 0, $args = array() ) {
		global $post, $post_ID, $coauthors_plus, $wpdb;

		$coauthors = array();
		$post_id = (int)$post_id;
		if(!$post_id && $post_ID) $post_id = $post_ID;
		if(!$post_id && $post) $post_id = $post->ID;

		$defaults = array('orderby'=>'term_order', 'order'=>'ASC');
		$args = wp_parse_args( $args, $defaults );

		if($post_id) {
			$coauthor_terms = wp_get_post_terms( $post_id, self::COAUTHOR_TAXONOMY, $args );

			if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
				foreach($coauthor_terms as $coauthor) {
					$post_author =  get_userdatabylogin($coauthor->name);
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
		if( $column_name == 'contributor')	{
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
		require_once( __DIR__ . '/../metaboxes/list-authors.php')	;
	}

	/**
	 *
	 */
	public static function pre_get_posts($query) {
		$types = array('propel_task', 'propel_project');
		if( !isset($query->query_vars['post_type']) ) return $query;
		if( !in_array($query->query_vars['post_type'], $types))  return $query;

		global $user_ID;
		$user = get_userdata($user_ID);
		$query->set('taxonomy', 'author');
		$query->set('term', $user->user_login);
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
			return self::add_coauthors( $post_id, $coauthors );
		}
	}

	public static function add_coauthors( $post_id, $coauthors, $append = false ) {
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

			if( !has_term( $name, self::COAUTHOR_TAXONOMY, $post->ID ) ){
				$notify[] = $name;
			}
		}

		if( !is_wp_error( $insert ) ) {
			$set = wp_set_post_terms( $post_id, $coauthors, self::COAUTHOR_TAXONOMY, $append );
		}
		self::notify_coauthors($notify);
	}


	public static function delete_user( $delete_id ) {
		global $wpdb;

		$reassign_id = absint( $_POST['reassign_user'] );

		if($reassign_id) {
			$reassign_user = get_profile_by_id( 'user_login', $reassign_id );
			if( $reassign_user ) {
				$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $delete_id ) );

				if ( $post_ids ) {
					foreach ( $post_ids as $post_id ) {
						self::add_coauthors( $post_id, array( $reassign_user ), true );
					}
				}
			}
		}

		$delete_user = get_profile_by_id( 'user_login', $delete_id );
		if( $delete_user ) {
			wp_delete_term( $delete_user, self::COAUTHOR_TAXONOMY );
		}
	}

	//email when
	//- assigned to a task
	//- unassigned a task
	//- task was updated (exclude users from the aforementioned two)
	//- comment made
	public static function notify_coauthors($to) {
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
}

?>