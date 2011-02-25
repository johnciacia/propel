<div id="tabs">
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
            foreach($tasks[$project->title] as $task) {
            	$script .= '
            		jQuery("#pm-row-' . $task->id . '").mouseover(function() {
            			jQuery("#progress-'.$id.'-'.$task->id.'").hide();
            			jQuery("#description-'.$id.'-'.$task->id.'").show();
            		});
            		
             		jQuery("#pm-row-' . $task->id . '").mouseout(function() {
            			jQuery("#progress-'.$id.'-'.$task->id.'").show();
            			jQuery("#description-'.$id.'-'.$task->id.'").hide();
            		});           		
            		
            		jQuery("#description-'.$id.'-'.$task->id.'").hide();
            		jQuery("#progress-'.$id.'-'.$task->id.'").progressbar({value: '.$task->complete.'});';
            		
            ?>
            	<tr id="pm-row-<?php echo $task->id ?>">
            		<td><?php echo $task->title; ?></td>
            		<td id="pm-data-<?php echo $task->id ?>" width="300">
            			<div id="progress-<?php echo $id.'-'.$task->id; ?>" class="progressbar"></div>
            			<div id="description-<?php echo $id.'-'.$task->id; ?>"><?php echo $task->description; ?></div>
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
		jQuery("#tabs").tabs();
		<?php echo $script; ?>
	});
</script>
            