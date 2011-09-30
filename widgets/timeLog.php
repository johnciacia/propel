<?php
if($_GET['start'] == 1) {
	$start_time = get_post_meta( $_GET['id'], '_propel_time_start', true );
	if(!$start_time)
		update_post_meta( $_GET['id'], "_propel_time_start", time());
}

if($_GET['stop'] == 1) {
	$end_time = get_post_meta( $_GET['id'], '_propel_time_end', true );
	if(!$end_time)
		update_post_meta( $_GET['id'], "_propel_time_end", time());
}
?>

<table width="100%">
<tr>
	<th></th>
	<th>Client</th>
	<th>Project</th>
	<th>Task</th>
	<th>Start</th>
	<th>End</th>
	<th>Duration</th>
	<th>User</th>
</tr>
<?php
	query_posts( 'post_type=propel_time' );
	while( have_posts() ) : the_post();

		$post = get_post( get_the_ID() );

		if( get_post_type( $post->post_parent ) == "propel_task") {
			$task = get_post( $post->post_parent );

			$task_name = $task->post_title;

			$project = get_post( $task->post_parent );
			$project_name = $project->post_title;
		} else {
			$task_name = " - ";

			$project = get_post( $post->post_parent );
			$project_name = $project->post_title;
		}
		

		$end_time = get_post_meta( get_the_ID(), '_propel_time_end', true );
		if(!$end_time) { 
			$end_time = " - ";
			$status = "stop";
		}

		$start_time = get_post_meta( get_the_ID(), '_propel_time_start', true );
		if(!$start_time) { 
			$start_time = " - ";
			$status = "start";
		}

		if( $end_time != " - " && $start_time != " - " ) {
			$status = "bill";
		}

		$client_id = get_post_meta( $project->ID, '_propel_project_owner', true );			
		$client = get_userdata( $client_id );

		?>
		<tr>
			<td><a href="admin.php?page=propel-time&<?php echo $status; ?>=1&id=<?php the_ID(); ?>"><?php echo ucfirst($status); ?></a></td>
			<td><?php echo $client->user_nicename; ?></td>
			<td><?php echo $project_name; ?></td>
			<td><?php echo $task_name; ?></td>
			<td><?php echo date( "G:i:s", $start_time ); ?></td>
			<td><?php echo date( "G:i:s", $end_time ); ?></td>
			<td><?php echo date( "G:i:s", $end_time-$start_time ); ?></td>
			<td><?php echo get_the_author(); ?></td>
		</tr>

		<?php
	endwhile;
	wp_reset_query();
?>
</table>