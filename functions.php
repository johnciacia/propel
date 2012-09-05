<?php

/*
 * aps2012
 */
function set_page_title($admin_title, $title){
	global $post;
	if(get_post_type($post->ID) == 'propel_project' ){
		return 'Propel > ' . $post->post_title;
	}
	if(get_post_type($post->ID) == 'propel_task' ){
		$parent = get_post($post->post_parent);
		return 'Propel > ' . $parent->post_title . ' > ' . $post->post_title;
	}
}

add_filter('admin_title','set_page_title',10,2);

function remove_box(){
       remove_meta_box('commentstatusdiv', 'propel_project','normal');
       remove_meta_box('commentstatusdiv', 'propel_task','normal');
}

add_filter('add_meta_boxes','remove_box');

function set_context(){
   	$part1 = "889999999999";
	$current_user = wp_get_current_user();
	$part2 = $current_user->ID;
	$id = $part1 . $part2;
	$static_id = (int)($id);

	if ($_REQUEST['context'] == 'admin'){
		update_post_meta( $static_id, '_propel_preference',"admin");
	} elseif ($_REQUEST['context'] == 'personal'){
		update_post_meta( $static_id, '_propel_preference',"personal");
	}
	
}
add_action( 'admin_init', 'set_context' ); 

function mytheme_admin_bar_render() {
	global $wp_admin_bar;
	global $url_curr;
	global $wp;
	$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
	$current_url = str_replace("&context=personal", "", $current_url);
	$current_url = str_replace("&context=admin", "", $current_url);
	$current_url = str_replace("?context=personal", "", $current_url);
	$current_url = str_replace("?context=admin", "", $current_url);
	$and_char = strrpos($current_url, '&');
	$quest_char = strrpos($current_url, '?');
	if (($and_char === false) && ($quest_char === false)) { 
         $con = '?';
    } elseif ((($and_char === false) && ($quest_char !== false)) ||
	 (($and_char !== false) && ($quest_char !== false))) {
	     $con = '&';
    }
	$wp_admin_bar->remove_menu('updates');
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
		'href' => $current_url . $con ."context=admin"
	)); 
	
	$contactUsURL = $current_url;
	$wp_admin_bar->add_menu(array(
		'parent' => 'customer_support',
		'id' => 'personalpref',
		'title' => __('Personal'),
		'href' =>  $current_url .$con ."context=personal"
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
	
	echo "
	<table width='100%'>
	<div>
	<style>
	 .pdue_title{ width:100%; height:20px; position:relative; margin:3px 0px;}
	 .pdue_title a{text-decoration:none; border:0px; font-weight:bold; color: #000;}
	 .task_name{ float:left; width:480px; }
	 .task_remark{ float:left; width:100px; }
	 </style>
	 <script type='text/javascript'>
	  var togl = jQuery.noConflict();
	  togl(document).ready(function(){
	      togl('.pdue_title a').click(function(){
			  togl(this).parent().parent().nextAll('.pdue_tasks').eq(0).toggle();
			  });
		  togl('.pdue_title a').hover(function(){
			  togl(this).css({'border':'1px solid #CCC', padding:'3px'});
		  });	  
		  togl('.pdue_title a').mouseout(function(){
			  togl(this).css({'border':'none', padding:'0px'});
		  });
	  });
	 </script> 
	";
	
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
			// check if complete
			if ($progress != 100){
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
								echo "
								<div class='pdue_title'>
									<span style='font-weight: bold;'><a class='pdue_tgle' href='javascript:;'>"
									 .$project->post_title. "</a></span>
								</div>
								<div class='pdue_tasks'>
								";
								$display++;
							}
							echo "<div class='task_name'>
							<a href='".get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></div>";
							echo "<div class='task_remark'>
							<span style='color: red;'>" . str_replace( '-', '', $hours) 
							. " hours past due.</span></div>";
						}
					
						if ( $hours < -24 ) {
							if ($display == 0){
								echo "
								<div class='pdue_title'>
									<span style='font-weight: bold;'><a class='pdue_tgle' href='javascript:;'>"
									 .$project->post_title. "</a></span>
								</div>
								<div class='pdue_tasks'>
								";
								$display++;
							}
								echo "<div class='task_name'>
								<a href='".get_edit_post_link(  $task->ID,'&amp;')."'>" . $task->post_title . "</a></div>";
							echo "<div class='task_remark'><span style='color: red; font-weight: bold;'>" 
							. str_replace( '-', '', $days) . " days past due.</span></div>";
						}
					}// if date
			}// if complete
		} // foreach
		echo '</div><div style="clear:both"></div>';
	}
	echo "</div></table>";
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