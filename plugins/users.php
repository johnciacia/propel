	<?php

	WP_Post_Contributors::initialize();

	class WP_Post_Contributors {
		
		/**
		 *
		 */
		public static function initialize() {
			//add_filter( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
			add_filter( 'parse_query', array( __CLASS__, 'parse_query') );
			add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );

		}

		/**
		 *
		 */
		public static function add_meta_boxes() {	
			add_meta_box( 'propel_list_authors', __( 'Contributors' ),
				array( __CLASS__, 'list_authors'), 'post', 'side' );
		}

		public static function list_authors() {
			$users = get_users();
			$contributors = get_post_meta( get_the_ID(), 'contributors');
		?>


	<div id="propel_list_users" class="categorydiv">
		<ul id="propel_list_users-tabs" class="category-tabs">
			<li class="tabs">
				<a href="#propel_user-all">All Users</a>
			</li>
		</ul>

		<div id="propel_user-all" class="tabs-panel" style="display: block; ">
			<ul id="propel_userschecklist" class="list:propel_category categorychecklist form-no-clear">

			<?php foreach($users as $user) : ?>
			<li id="propel_user-<?php echo $user->ID; ?>" class="popular-category">
				<label class="selectit">
					<?php if( isset($contributors[0]) && is_array($contributors[0]) && in_array( $user->ID, $contributors[0] ) ) : ?>
					<input value="<?php echo $user->ID; ?>" type="checkbox" name="post_contributors[]" id="in-propel_user-<?php echo $user->ID; ?>" checked> <?php echo $user->display_name; ?>
					<?php else : ?>
					<input value="<?php echo $user->ID; ?>" type="checkbox" name="post_contributors[]" id="in-propel_user-<?php echo $user->ID; ?>"> <?php echo $user->display_name; ?>
					<?php endif; ?>
				</label>
			</li>
			<?php endforeach; ?>				
			</ul>
		</div>
	</div>

		<?php
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