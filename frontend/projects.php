<div class="propel-projects">
	<ul>
		<?php 
			foreach ($projects as $project) {
				echo '<li><a href="#project-' . $project->id . '"><span>' 
					. $project->title . '</span></a></li>';
			}
		?>
	</ul>   

    <?php
    foreach ($projects as $project) {
    ?>
    	<div id="project-<?php echo $project->id ?>">
        	<table>
        	<?php 
			$tasks = $this->tasksModel->getTasksByProject($project->id);
            foreach($tasks as $task) {
            	$script .= '
            		jQuery("#pm-row-' . $task->id . '").mouseover(function() {
            			jQuery("#progress-'.$id.'-'.$task->id.', #description-'.$id.'-'.$task->id.'").toggle();
            		}).mouseout(function() {
	            			jQuery("#progress-'.$id.'-'.$task->id.', #description-'.$id.'-'.$task->id.'").toggle();
	            	});         		
            		jQuery("#progress-'.$id.'-'.$task->id.'").progressbar({value: '.$task->complete.'});';
            		
            ?>
            	<tr id="pm-row-<?php echo $task->id ?>">
            		<td><?php echo $task->title; ?></td>
            		<td id="pm-data-<?php echo $task->id ?>" width="300">
            			<div id="progress-<?php echo $id.'-'.$task->id; ?>" class="progressbar"></div>
            			<div class="propel-project-description" id="description-<?php echo $id.'-'.$task->id; ?>"><?php echo $task->description; ?></div>
            		</td>
            	
            		<td><?php echo $task->complete ?>%</td>
            	</tr>
            <?php 	
            }
            ?>
            </table>
        </div>
    <?php 
    }
    ?>
</div>
<br />

<script>
	jQuery(document).ready(function() {
		jQuery(".propel-project-description").hide();
		jQuery(".propel-projects").tabs();
		<?php echo $script; ?>
	});
</script>
