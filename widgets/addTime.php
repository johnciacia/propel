<form action="admin-post.php" method="POST">
<select name="id">
<?php
	foreach($projects as $project) {
		echo "<option value='{$project->ID}'>{$project->post_title}</option>";
		$tasks = $this->tasksModel->getTasksByProject($project->ID);
		foreach($tasks as $task) {
			echo "<option value='{$task->ID}'>{$project->post_title} - {$task->post_title}</option>";			
		}
	}
?>
</select>
<input class="button-primary" type="submit" value="Submit">
<input type="hidden" name="action" value="propel_add_time" />
</form>