<table width="100%" class="gen-table tasks-table">
	<thead>
		<tr>
			<th></th>
			<th class="sortable"><p><?php _e("Name", "propel"); ?></p></th>
			<?php if($_GET['page'] == "propel-edit-project") { ?>
				<th class="sortable"><p><?php _e("Owner", "propel"); ?></p></th>
			<?php } else { ?>
				<th class="sortable"><p><?php _e("Project", "propel"); ?></p></th>
			<?php } ?>
			<th class="sortable"><p><?php _e("Start Date", "propel") ?></p></th>
			<th class="sortable"><p><?php _e("End Date", "propel") ?></p></th>
			<th class="sortable"><p><?php _e("Priority", "propel"); ?></p></th>
			<th class="sortable"><p><?php _e("Progress", "propel"); ?></p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php
	foreach($tasks as $task) {
		//Get the post meta information
		$meta = get_post_meta($task->ID, "_propel_task_metadata", true);
		//@todo remove error supression operator	
		if($show_complete == false && @$meta['complete'] == 100)
			continue;
		//@todo remove error supression operator
		if($show_complete == true && @$meta['complete'] < 100) 
			continue;
			
		//Get the owner of this task. If the task is unassigned display a dash
		$owner = ($meta['assigned_to'] == 0) ? "-" : $this->tasksModel->getUserById($meta['assigned_to'])->user_nicename;
		//Get the project name this task is associated with
		$project = $this->projectsModel->getProjectById($task->post_parent);
		//Get the project status
		list($z, $color) = propel_get_status($meta);

		//If there is no start date or end date display a dash
		($meta['start'] == "0000-00-00") ? $start = "-" : $start = $meta['start'];
		($meta['end'] == "0000-00-00") ? $end = "-" : $end = $meta['end'];
		$x = ($meta['complete'] == 100) ? "" : "un"; 
		
		echo "<tr id='propel-task-{$task->ID}' data-value='{$task->ID}'>";
		echo "<td><div style='background-color: $color' class='propel-status'>$z</div></td>";
		echo "<td><p>{$task->post_title}</p></td>";
		
		if($_GET['page'] == "propel-edit-project") {
			echo "<td><p>{$owner}</p></td>";	
		} else {
			echo "<td><p>{$project->post_title}</p></td>";
		}
		
		echo "<td><p>{$start}</p></td>";
		echo "<td><p>{$end}</p></td>";
		echo "<td><p>{$meta['priority']}</p></td>";
		echo "<td><p>{$meta['complete']}%</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->ID}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-task&id={$task->ID}' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->ID}' title='Mark as complete'>Complete</a></td>";	
		echo "</tr>";

	}
	?>
</table>
<div style="clear:both;"></div>