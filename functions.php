<?php

/* TASKS FOR SIGNED IN USERS ONLY aps2012 */
function get_authored_posts($query) {
    global $user_ID;
	$u = get_userdata($user_ID);
	$queried_post_type = get_query_var('post_type');
    if ('propel_task' ==  $queried_post_type ) {
	  	$taxquery = array(
		     array(   
				'taxonomy' => 'author',
				 'field' => 'name',
                 'terms' => $u->user_login				
		     )
		 );
      $query->set('tax_query', $taxquery);	
    }
    if ('propel_project' ==  $queried_post_type ) {
	  	$taxquery = array(
		     array(   
				'taxonomy' => 'author',
				 'field' => 'name',
                 'terms' => $u->user_login				
		     )
		 );
      $query->set('tax_query', $taxquery);	
    }
    return $query;
}
add_filter('pre_get_posts', 'get_authored_posts');


/* CURRENT PROJECTS */
function dashboard_client_current_projects_metabox() {

	function dashboard_client_current_projects_content() {
		echo "
			<ul>
				<li><a href='#'>Project #1</a></li>
				<li><a href='#'>Project #2</a></li>
			</ul>
		";
	}
	
	wp_add_dashboard_widget( 'dashboard_client_current_projects_content', __( 'Current Projects' ), 'dashboard_client_current_projects_content' );
	
}

add_action('wp_dashboard_setup', 'dashboard_client_current_projects_metabox');


/* COMPLETED PROJECTS */
function dashboard_client_completed_projects_metabox() {

	function dashboard_client_completed_projects_content() {
		echo "
			<ul>
				<li><a href='#'>Project #1</a></li>
				<li><a href='#'>Project #2</a></li>
			</ul>
		";
	}

	wp_add_dashboard_widget( 'dashboard_client_completed_projects_content', __( 'Completed Projects' ), 'dashboard_client_completed_projects_content' );
}

add_action('wp_dashboard_setup', 'dashboard_client_completed_projects_metabox');


/* ARCHIVED PROJECTS */
function dashboard_client_archived_projects_metabox() {

	function dashboard_client_archived_projects_content() {
		echo "
			<ul>
				<li><a href='#'>Project #1</a></li>
				<li><a href='#'>Project #2</a></li>
			</ul>
		";
	}

	wp_add_dashboard_widget( 'dashboard_client_archived_projects_content', __( 'Archived Projects' ), 'dashboard_client_archived_projects_content' );
}

add_action('wp_dashboard_setup', 'dashboard_client_archived_projects_metabox');


/* GANTT CHART */
function dashboard_client_gantt_chart_metabox() {

	function dashboard_client_gantt_chart_content() {
		echo "
			<img alt='Gannt Chart' src='/wp-content/themes/the-portland-company-version-three/images/gantt-chart.png' title='Gannt Chart' />
		";
	}

	wp_add_dashboard_widget( 'dashboard_client_gantt_chart_content', __( 'Timeline' ), 'dashboard_client_gantt_chart_content' );

}
add_action('wp_dashboard_setup', 'dashboard_client_gantt_chart_metabox');


/* SUPPORT REQUESTS */
function dashboard_client_support_requests_metabox() {

	function dashboard_client_support_requests_content() {
		echo "
			<p>If you have an inquiry or support request use the form below to have it added to the queue so it can be addressed promptly.</p>
		";
	}
	
	wp_add_dashboard_widget( 'dashboard_client_support_requests_content', __( 'Support Requests' ), 'dashboard_client_support_requests_content' );
	
}

add_action('wp_dashboard_setup', 'dashboard_client_support_requests_metabox');

function dashboard_widget_function() {
    global $user_ID;
	$u = get_userdata($user_ID);
	$args = array(
		'numberposts' => -1,
		'post_type' => 'propel_project',
		'post_status' => 'publish',
		'tax_query' => array(
		     array(   
				 'taxonomy' => 'author',
				 'field' => 'name',
                 'terms' => $u->user_login				
		     )
		 )
	);
	$projects = get_posts( $args );
	echo "<table width='100%'>";
	foreach( $projects as $project ) {
		echo '<tr rowspan="3"><td><strong>' . $project->post_title . '</strong></td></tr>';
		$argv = array(
			'numberposts' => -1,
			'post_type' => 'propel_task',
			'post_status' => 'publish',
			'post_parent' => $project->ID,
			'tax_query' => array(
		        array(   
				 'taxonomy' => 'author',
				 'field' => 'name',
                 'terms' => $u->user_login				
		        )
		    )
		);
		$tasks = get_posts( $argv );
		

		foreach( $tasks as $task ) {
			$progress = get_post_meta( $task->ID, '_propel_complete', true );
			echo "<tr>";
			echo "<td width='200'>" . $task->post_title . "</td>";
			echo "<td width='65%'><div class='propel-progress-bar' style='height:13px' title='$progress'></div></td>";
			echo "<td>" . $progress . "%</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('.propel-progress-bar').each(function() {
				value = { value: parseInt(this.title) }
				jQuery(this).progressbar( value )
			})
		})
	</script>
<?php
}

function add_dashboard_widgets() {
	wp_add_dashboard_widget('dashboard_widget', 'Projects Overview', 'dashboard_widget_function');
}

add_action('wp_dashboard_setup', 'add_dashboard_widgets' );


class Propel_Functions {
	
	var $args = array();
	var $post_type;
	var $post;
	var $action;
	var $status;
	var $cb;

	public static function register_post_status( $status, $args ) {
		register_post_status( $status );

		$functions = new Propel_Functions();
		$functions->status = $status;
		$functions->args = $args;
		add_filter( 'parse_query', array( $functions, 'parse_query' ) );
		add_action( 'admin_footer', array( $functions, 'admin_footer' ) );
	}

	/**
	 * $args['post_type']
	 * $args['action']
	 */
	public static function add_post_action( $args, $cb ) {
		if( isset($_GET['post_type']) && $_GET['post_type'] != $args['post_type']) return;

		$functions = new Propel_Functions();
		$functions->args = $args;
		$functions->args['cb'] = $cb;

		add_action( 'admin_footer', array( $functions, 'admin_footer_action' ) );
		add_filter( 'post_row_actions', array( $functions, 'post_row_actions' ) );
		add_action( 'admin_action_' . $args['action'], array( $functions, 'do_action' ) );
	}

	/**
	 * @todo verify that the current user can perform said action
	 */
	public function do_action() {

		if( is_array( $_REQUEST['post'] ) ) {
			foreach( $_REQUEST['post'] as $post => $post_id) {
				call_user_func($this->args['cb'], $post_id);	
			}
		} else {
			call_user_func($this->args['cb'], $_GET['post']);
		}

		wp_redirect( $_SERVER['HTTP_REFERER'] );
		die();
	}

	public function post_row_actions( $actions ) {
		if( !isset($_GET['post_type']) || $_GET['post_type'] != $this->args['post_type']) return $actions;
		$actions[$this->args['action']] = "<a href='post.php?post=" . get_the_ID() . "&action=" . $this->args['action'] . "'>" . $this->args['label'] . "</a>";
		return $actions;
	}

	public function admin_footer_action() {
		if( !isset($_GET['post_type']) || $_GET['post_type'] != $this->args['post_type']) return;
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('<option>').val("<?php echo $this->args['action']; ?>").text("<?php echo $this->args['label']; ?>").appendTo("select[name='action']");
				jQuery('<option>').val("<?php echo $this->args['action']; ?>").text("<?php echo $this->args['label']; ?>").appendTo("select[name='action2']");
			});
		</script>
		<?php
	}


	/**
	 * @since 2.0
	 */
	public function parse_query($query) {
		global $pagenow;
		if ( !isset( $_GET['post_type'] ) )
			return $query;

		if( $pagenow != "edit.php" && $_GET['post_type'] != $this->args['post_type'] )
			return $query;

		if( isset($_GET['post_status'] ) && $_GET['post_status'] == $this->status ) {
			$query->query_vars['post_type'] = $this->args['post_type'];
			$query->query_vars['post_status'] = $this->status;
		}
	}

	/**
	 * JavaScript hacks to add custom bulk action and custom post status 
	 * @since 2.0
	 */
	public function admin_footer() {
		global $wpdb;

		if(isset($_GET['post'])) :
			$post = get_post($_GET['post']);
			if( $post->post_type == $this->args['post_type']) :
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('<option>').val("<?php echo $this->status; ?>").text("<?php echo $this->args['label']; ?>").appendTo("#post_status");
					<?php if( get_post_status( get_the_ID() ) == $this->status) : ?>
					$("label[for='post_status']").html("Status: <strong><?php echo $this->args['label']; ?></strong>");
					$("#save-post").val("Save <?php echo $this->args['label']; ?>");
					$('#post_status').val("<?php echo $this->status; ?>")
					<?php endif; ?>
				});
			</script>
			<?php
			endif;
		endif;

		if(isset($_GET['post_type']) && $_GET['post_type'] != $this->args['post_type']) return;
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '".$this->args['post_type']."' && post_status = '$this->status';" ) );
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("<li>").html(" | <a href='edit.php?post_status=" 
						+ "<?php echo $this->status ?>"
						+ "&post_type="
						+ "<?php echo $this->args['post_type']; ?>'>" 
						+ "<?php echo $this->args['label'] ?>"
						+ "  <span class='count'>(" 
						+ "<?php echo $count; ?>"
						+ ")</span></a>").appendTo('.subsubsub')
			});
		</script>
		<?php
	}
}

?>