<?php 
if ( ! defined( 'PROPEL_MIGRATE_DB' ) )
	exit();

if( get_option('PROPEL_DBVERSION') == 1.4 ) :
?>


<?php

if(isset($_POST['update']) && $_POST['update'] == 'update') {
$sql = "SELECT * FROM {$wpdb->prefix}projects";
$projects = $wpdb->get_results($sql, OBJECT);


foreach($projects as $project) {
	$p = array(
		'post_title' => $project->title,
		'post_content' => $project->description,
		'post_status' => 'publish',
		'post_type' => "propel_project"
	);

	$id = wp_insert_post($p);
	
	$sql = "SELECT * FROM {$wpdb->prefix}tasks WHERE `pid` = {$project->id}";
	$tasks = $wpdb->get_results($sql, OBJECT);
	foreach($tasks as $task) {
		create_task($task, $project, $id);
	}
}

update_option("PROPEL_DBVERSION", 1.5);

}



function create_task($task, $project, $id) {

	$t = array(
		'post_title' => $task->title,
		'post_content' => $task->description,
		'post_status' => 'publish',
		'post_parent' => $id,
		'post_type' => "propel_task"
	);
	//create a custom post type
	$id = wp_insert_post($t);
	
	//create the posts metadata
	$meta = array(
		'start' => $task->start, 
		'end' => $task->end,
		'priority' => $task->priority,
		'complete' => $task->complete,
		'assigned_to' => $task->uid
	);

	add_post_meta($id, "_propel_task_metadata", $meta);
	add_post_meta($id, "_propel_task_user", $task->uid);
		
}

?>

	<div id="propel-general" class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Propel</h2>

	<div class='error fade'><p><strong>It is strongly recommended you backup your database before you continue.</strong></p></div>

	<p>
	Version 1.7 of Propel has changed its database structure in an effort to take full advantage of more advanced 
	WordPress features. To continue using 1.7 you must update your database structure by clicking the update button below. 
	It is highly recommended you take a FULL backup of your database prior to updating the Propel database structure. 
	If you do not wish to continue, you may download an older version of Propel and upload it to your plugins directory. 
	Using an OLDER version will not affect your database. However, older versions will no longer be officially supported. 
	If you have any questions regarding this migration, you can post a comment at 
	<a href="http://www.johnciacia.com/propel/">http://www.johnciacia.com/propel/</a> or use the official 
	<a href="http://wordpress.org/tags/propel">WordPress forum</a>.
	</p>

<?php if(get_option('PROPEL_DBVERSION') != 1.5) : ?>
	<form method="POST">
		<input type="hidden" value="update" name="update" />
		<input type="submit" class="button" value="Update" />
	</form>
<?php else: ?>
	<a href="admin.php?page=propel">Go to projects</a>
<?php endif; ?>
	</div>

<?php endif; ?>

<?php if( get_option('PROPEL_DBVERSION') == 1.5 ) : ?>
TODO: Implement migration tool
<?php endif; ?>
