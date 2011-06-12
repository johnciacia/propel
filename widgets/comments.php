<form method="POST" action="admin.php">
<?php
echo "<select name='propel_post_id'>";
foreach($projects as $project) {
	$tasks = $this->tasksModel->getTasksByProject($project->ID);
	//echo "<optgroup label='{$project->post_title}'>";
	echo "<option value='{$project->ID}'>{$project->post_title}</option>";
	foreach($tasks as $task) {
		echo "<option value='{$task->ID}'>&nbsp;&nbsp;{$task->post_title}</option>";
	}
	//echo "</optgroup>";
}
echo "</select>";

?>
<br />
<input type="hidden" name="action" value="propel-insert-comment" />
<textarea name="propel_content"></textarea><br />
<input type="submit" class="button-primary" />
</form>