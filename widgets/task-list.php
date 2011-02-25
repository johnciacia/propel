<?php
	if($tasks == NULL) {
		echo "<p>There are no tasks at this time.</p>";
	}
	else {
?>
<table width="100%" id="pm-milestones">
<?php 
	$script = "";

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
?>

	<tr id="pm-task-<?php echo $task->id ?>">
		<td>
		
			<div class="ar as" id="pl-status-<?php echo $task->id;?>">
				<div class="at" style="background-color: <?php echo $color; ?>; border-color: <?php echo $color; ?>;">
					<div class="au" style="border-color:<?php echo $color; ?>">
						<div class="av"><?php echo $z; ?></div>
					</div>
				</div>
			</div>
			
			
			
		</td>
		<td width="100%">
			<p><?php echo $task->title ?></p>
			<div id="progress-<?php echo $task->id; ?>" class="progressbar"></div>
			<div id="description-<?php echo $task->id; ?>">
				<p><?php echo $task->description; ?></p>
			</div>
			<?php 
				$script .= '
            		jQuery("#pm-task-' . $task->id . '").mouseover(function() {
            			jQuery("#progress-'.$task->id.'").show();
            			jQuery("#description-'.$task->id.'").show();
            		});
            		
             		jQuery("#pm-task-' . $task->id . '").mouseout(function() {
            			jQuery("#progress-'.$task->id.'").hide();
            			jQuery("#description-'.$task->id.'").hide();
            		});
            		
            		jQuery("#description-'.$task->id.'").hide();
            		jQuery("#progress-'.$task->id.'").hide();
					jQuery("#progress-'.$task->id.'").progressbar({value: '.$task->complete.'});';	
			?>
		</td>
		<td>
			<div class="row-actions">
				<div class="pm-row-action">
					<a onclick="pm_complete(<?php echo $task->id ?>, <?php echo $completed ?>)">Complete</a>
				</div>
				<div class="pm-row-action">
					<a onclick="pm_delete(<?php echo $task->id ?>)">Delete</a>
				</div>
			</div>
		</td>
	</tr>
	
<?php 	
    }
?>
</table>

<script>
	<?php echo $script; ?>
</script>
<?php } ?>