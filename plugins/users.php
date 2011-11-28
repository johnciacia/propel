<?php

// function asdf34_parse_query($query) {
// 	$query->query_vars['coauthor'] = 'admin';
// 	$query->query['coauthor'] = 'admin';
// 	echo "<pre>" . print_r($query, true) . "</pre>";
// }

// add_filter('parse_query', 'asdf34_parse_query');
// WP_Post_Contributors::initialize();

class WP_Post_Contributors {
	
	/**
	 *
	 */
	public static function initialize() {
		//add_filter( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
		//add_filter( 'parse_query', array( __CLASS__, 'parse_query') );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );

	}

	public static function add_meta_boxes() {	
		add_meta_box( 'propel_list_authors', __( 'Contributors' ),
			array( __CLASS__, 'list_authors'), 'post', 'side' );

	}

	public static function list_authors() {
		require_once( __DIR__ . '/../metaboxes/list-authors.php')	;
	}

	/**
	 *
	 */
	 public static function parse_query($query) {
	 	global $user_ID;
	 	$posts = unserialize(get_user_meta($user_ID, 'contributors', true));
	 	$query->query_vars['post__in'] = $posts;
	 	return $query;
	 }



	public static function save_post($post_id, $post) {
		/**
		 * Sanity checks
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		if( !isset( $_POST['post_contributors'] ) ) return;

		/**
		 * Remove posts from users meta
		 */
		$post_meta = get_post_meta( $post->post_parent, 'contributors', true );

		$remove = array_diff($post_meta, $_POST['post_contributors']);
		foreach($remove as $user_id) {
			$data = unserialize(get_user_meta($user_id, 'contributors', true));
			unset($data[array_search($post->post_parent, $data)]);
			$data = serialize($data);
			update_user_meta($user_id, 'contributors', $data);
		}

		/**
		 * Add posts to users meta
		 */
		foreach($_POST['post_contributors'] as $user_id) {

			$data = unserialize(get_user_meta($user_id, 'contributors', true));
			if( count($data) != 0 ) {
				if ( !in_array( $post->post_parent, $data ) ) {
					$data[] = $post->post_parent;
				}
				$data = array_unique($data);
				sort( $data );
				$data = serialize($data);
				update_user_meta($user_id, 'contributors', $data);
			} else {
				$data = array();
				$data[0] = $post->post_parent;
				$data = serialize($data);
				update_post_meta($user_id, 'contributors', $data);  
			}

		}

		update_post_meta( $post_id, 'contributors', $_POST['post_contributors'] );

	}
}

?>