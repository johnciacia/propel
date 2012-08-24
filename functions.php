<?php
/*
 * aps2012
 */
function ajax_scripts(){?>
<script type="text/javascript">
function ajax_update(x){
		
	var http_req = new XMLHttpRequest();
	if (x == "admin"){ 
		var php_file = "<?php echo plugins_url(); ?>/propel/ajax-admin.php";
	} else if(x == "personal"){
		var php_file = "<?php echo plugins_url(); ?>/propel/ajax-personal.php";
    }
	http_req.open("POST", php_file, true);
	http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_req.onreadystatechange = function(){
	}
	http_req.send(); 	
}
</script>
<?php
}

add_action('admin_head','ajax_scripts');

function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	global $url_curr;
	global $wp;
	$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
	$wp_admin_bar->remove_menu('updates');
	$customerSupportURL = $current_url;
	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);
	$profile = get_post_meta( $static_id, '_propel_preference',true);
	if(empty($profile)){
	   $title = "Context - Personal";
	} elseif($profile == 'personal'){
	   $title = "Context - Personal";
	} elseif($profile == 'admin'){
	   $title = "Context - Admin";
	}
	
	$wp_admin_bar->add_menu( array(
		'parent' => false,
		'id' => 'customer_support',
		'title' => __($title)
	));
	
	$contactUsURL = $current_url;
	$wp_admin_bar->add_menu(array(
		'parent' => 'customer_support',
		'id' => 'adminpref',
		'title' => __('Admin'),
		'href' => $contactUsURL,
		'meta' => array( 'onclick' => 'ajax_update("admin")' )
	)); 
	
	$contactUsURL = $current_url;
	$wp_admin_bar->add_menu(array(
		'parent' => 'customer_support',
		'id' => 'personalpref',
		'title' => __('Personal'),
		'href' => $contactUsURL,
		'meta' => array( 'onclick' => 'ajax_update("personal")' )
	));
}

add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

/* TASKS FOR SIGNED IN USERS ONLY aps2012 */
function get_authored_posts($query) {
    global $user_ID;
	$u = get_userdata($user_ID);
	$queried_post_type = get_query_var('post_type');
	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);
	$profile = get_post_meta( $static_id, '_propel_preference',true);
	if(empty($profile)){
	   $profile = "personal";
	}
    if (('propel_task' ==  $queried_post_type ) && ($profile == 'personal' ))  {
	  	$taxquery = array(
		     array(   
				'taxonomy' => 'author',
				 'field' => 'name',
                 'terms' => $u->user_login				
		     )
		 );
      $query->set('tax_query', $taxquery);	
    }
    if (('propel_project' ==  $queried_post_type ) && ($profile == 'personal' )) {
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



/* DASHBOARD METABOX THAT DISPLAYS PAST DUE TASKS */
function Past_Due_Function() {

	global $user_ID;
	$u = get_userdata($user_ID);
	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);
	$profile = get_post_meta( $static_id, '_propel_preference',true);
	
	if(empty($profile)){
		$profile = "personal";
	}
	
	$args = array(
		'numberposts' => -1,
		'post_type' => 'propel_project',
		'post_status' => 'publish'
	);
	
	
	$projects = get_posts( $args );
	
	echo "<table width='100%'>";
	
	foreach( $projects as $project ) {
		$display = 0;
		if($profile == "personal"){
		
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
		
		} else {
			$argv = array(
				'numberposts' => -1,
				'post_type' => 'propel_task',
				'post_status' => 'publish',
				'post_parent' => $project->ID
			);
		}
		
		$tasks = get_posts( $argv );
				
		foreach( $tasks as $task ) {
		    
			$progress = get_post_meta( $task->ID, '_propel_complete', true );
			$date = get_post_meta( $task->ID, '_propel_end_date', true );
			
			if($date) {
			
				//echo date( get_option( 'date_format' ) , $date ); // Project's actual due date.
				
				$day   = date('d'); // Day of the countdown
				$month = date('m'); // Month of the countdown
				$year  = date('Y'); // Year of the countdown
				$hour  = date('H'); // Hour of the day (east coast time)
				
				$calculation = ( $date - time() ) / 3600;
				$hours = (int)$calculation + 24;
				$days  = (int)( $hours / 24 ) - 1;
				
				$hours_remaining = $hours-($days*24)-24;
				
				if ( $hours < 0 && $hours > -24 ) {
					if ($display == 0){
						echo "<td><span style='font-weight: bold;'>" .$project->post_title. "</span></td>";
					    $display++;
					}
					echo "<tr>";
					echo "<td><a href='".get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></td>";
					echo "<td><span style='color: red;'>" . str_replace( '-', '', $hours) 
					. " hours past due.</span></td>";
					echo "</tr>";
				}
			
				if ( $hours < -24 ) {
					if ($display == 0){
						echo "<td><span style='font-weight: bold;'>" .$project->post_title. "</span></td>";
					    $display++;
					}
					echo "<tr>";
					echo "<td><a href='".get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></td>";
					echo " <td><span style='color: red; font-weight: bold;'>" 
					. str_replace( '-', '', $days) . " days past due.</span></td>";
					echo "</tr>";
				}
			}
		}
	}
	echo "</table>";
} 

function Past_Due_Hook() {
	wp_add_dashboard_widget('pastdue_dashboard_widget', 'Past Due Tasks', 'Past_Due_Function');	
} 
add_action('wp_dashboard_setup', 'Past_Due_Hook' ); 

/*----------------------

------------------------*/
function Due_Today_Tomorrow_Function() {
  global $user_ID;
	$u = get_userdata($user_ID);
	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);
	$profile = get_post_meta( $static_id, '_propel_preference',true);
	if(empty($profile)){
	   $profile = "personal";
	}

	$args = array(
		'numberposts' => -1,
		'post_type' => 'propel_project',
		'post_status' => 'publish'
		 );
	
	$projects = get_posts( $args );
	
	echo "<table width='100%'>";
	
	foreach( $projects as $project ) {
        $display = 0;
		if($profile == "personal") {
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
		} else {
			$argv = array(
				'numberposts' => -1,
				'post_type' => 'propel_task',
				'post_status' => 'publish',
				'post_parent' => $project->ID
			);
		}
		
		$tasks = get_posts( $argv );
		
		if ( !isset($tasks) ) {
			echo '<tr rowspan="3"><td><strong>' . $project->post_title . '</strong></td></tr>';
		}
		
		foreach( $tasks as $task ) {
			
				$progress = get_post_meta( $task->ID, '_propel_complete', true );
				$date = get_post_meta( $task->ID, '_propel_end_date', true );
				if($date) {
				
					//echo date( get_option( 'date_format' ) , $date ); // Project's actual due date.
						
					$day   = date('d'); // Day of the countdown
					$month = date('m'); // Month of the countdown
					$year  = date('Y'); // Year of the countdown
					$hour  = date('H'); // Hour of the day (east coast time)
					
					$calculation = ( $date - time() ) / 3600;
					$hours = (int)$calculation + 24;
					$days  = (int)( $hours / 24 ) - 1;
					
					$hours_remaining = $hours-($days*24)-24;
					
					if ( $hours <= 48 && $hours >= 24 ) {
					    if ($display == 0){
							echo "<td><span style='font-weight: bold;'>" .$project->post_title. "</span></td>";
					    	$display++;
					    }
						  echo "<tr>";
			                echo "<td><a href='"
							 .get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></td>";
							echo " <td><span style='color: brown;'>Due tomorrow.</span></td>";
			              echo "</tr>";
					} 
					
					if ( $hours <= 24 && $hours >= 0 ) {
					    if ($display == 0){
							echo "<td><span style='font-weight: bold;'>" .$project->post_title. "</span></td>";
					    	$display++;
					    }
						  echo "<tr>";
			                echo "<td><a href='"
							.get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></td>";
							echo " <td><span style='color: brown;'>Due today.</span></td>";
			              echo "</tr>";
					}
					
				  }
		}
	}
	echo "</table>";
} 

function Due_Today_Tomorrow_Hook() {
  wp_add_dashboard_widget('duetodaytomorrow_dashboard_widget', 'Tasks Due Today And Tomorrow', 'Due_Today_Tomorrow_Function');	
} 
add_action('wp_dashboard_setup', 'Due_Today_Tomorrow_Hook' ); 

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