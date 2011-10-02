<table width="100%" class="gen-table tablesorter" id="propel-tasks">
	<thead>
		<tr>
			<th class="sortable"><p>Name</p></th>
			<th class="sortable"><p>Priority</p></th>
			<th class="sortable"><p>Owner</p></th>
			<th class="sortable"><p>Start</p></th>
			<th class="sortable"><p>Due</p></th>
			<th class="sortable"><p>%</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php

	foreach($tasks as $task) {
		$meta = get_post_meta($task->ID, "_propel_task_metadata", true);
		$owner = ($meta['assigned_to'] == 0) ? "-" : $this->tasksModel->getUserById($meta['assigned_to'])->user_nicename;
		$start = ($meta['start'] == "0000-00-00") ? "-" : $meta['start'];
		$end   = ($meta['end'] == "0000-00-00") ? "-" : $meta['end'];
		$x = ($meta['complete'] == 100) ? "" : "un"; 
		echo "<tbody onClick='gen_expand(this)' id='{$task->ID}'><tr><td data-value='{$task->post_title}'><p>{$task->post_title}</p></td>";
		echo "<td data-value='{$meta['priority']}'><p>{$meta['priority']}</p></td>";
		echo "<td data-value='{$owner}'><p>$owner</p></td>";
		echo "<td data-value='{$start}'><p>$start</p></td>";
		echo "<td data-value='{$end}'><p>$end</p></td>";
		echo "<td data-value='{$meta['complete']}'><p>{$meta['complete']}</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->ID}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='post.php?post={$task->ID}&action=edit' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->ID}' title='Mark as complete'>Complete</a></td>";
		echo "</tr><tr class='gen-hidden' id='gen-row-{$task->ID}'><td>&nbsp</td><td colspan='8'><p>{$task->post_content}</p></td></tr></tbody>";	
	}

	?>

</table>


<script type="text/JavaScript">

	function gen_expand(elem) {
		jQuery('#gen-row-' + elem.id).toggle();
	}
	
</script>