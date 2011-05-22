<table width="100%" class="gen-table" id="propel-tasks">
	<thead>
		<tr>
			<th><p>Name</p></th>
			<th><p>Priority</p></th>
			<th><p>Owner</p></th>
			<th><p>Start</p></th>
			<th><p>Due</p></th>
			<th><p>%</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	

	<?php

	foreach($tasks as $task) {
		$owner = ($task->uid == 0) ? "-" : $this->tasksModel->getUserById($task->uid)->user_nicename;
		$start = ($task->start == "0000-00-00") ? "-" : $task->start;
		$end   = ($task->end == "0000-00-00") ? "-" : $task->end;
		$x = ($task->complete == 100) ? "" : "un"; 
		echo "<tbody onClick='gen_expand(this)' id='{$task->id}'><tr><td data-value='{$task->title}'><p>{$task->title}</p></td>";
		echo "<td data-value='{$task->priority}'><p>{$task->priority}</p></td>";
		echo "<td data-value='{$task->uid}'><p>$owner</p></td>";
		echo "<td data-value='{$task->start}'><p>$start</p></td>";
		echo "<td data-value='{$task->end}'><p>$end</p></td>";
		echo "<td data-value='{$task->complete}'><p>{$task->complete}</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->id}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-task&id={$task->id}' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->id}' title='Mark as complete'>Complete</a></td>";
		echo "</tr><tr class='gen-hidden' id='gen-row-{$task->id}'><td>&nbsp</td><td colspan='8'><p>{$task->description}</p></td></tr></tbody>";	
	}
	?>

</table>

<script type="text/JavaScript">

	function gen_expand(elem) {
		jQuery('#gen-row-' + elem.id).toggle();
	}
	
</script>