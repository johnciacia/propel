<table width="100%" id="propel-my-tasks" class="gen-table">
	<thead>
		<tr>
			<th width='80'></th>
			<th><p>Name</p></th>
			<th><p>Project</p></th>
			<th><p>Priority</p></th>
			<th><p>%</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php


	foreach($tasks as $task) {
		if($task->complete == 100) {
			$z = "Complete";
			$color = "#0000cc";
		} 
		
		else {
			if(date("Y-m-d") == $task->end) {
				$z = "Today";
				$color = "#ffa500";
			} else if(date("Y-m-d") > $task->end) {
				$z = "Overdue";
				$color = "#ff0000";
			} else {
				$z = "Later";
				$color = "#008000";
			}
		}
		
		$x = ($task->complete == 100) ? "" : "un"; 
		echo "<tbody onClick='gen_expand(this)' id='{$task->id}'><tr><td>";
		?>
			<div style="background-color: <?php echo $color; ?>;" class="propel-status"><?php echo $z; ?></div>
				<?php
		echo "</td>";
		echo "<td data-value='{$task->title}'><p>{$task->title}</p></td>";
		echo "<td><p>{$task->pid}</p></td>";
		echo "<td data-value='{$task->priority}'><p>{$task->priority}</p></td>";
		echo "<td data-value='{$task->complete}'><p>{$task->complete}</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->id}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='#' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->id}' title='Mark as complete'>Complete</a></td>";
		echo "</tr><tr class='gen-hidden' id='gen-row-{$task->id}'><td>&nbsp</td><td colspan='8'><p>{$task->description}</p></td></tr></tbody>";	
	}
	?>
	</tbody>
</table>

<script type="text/JavaScript">

	function gen_expand(elem) {
		jQuery('#gen-row-' + elem.id).toggle();
	}
	
</script>