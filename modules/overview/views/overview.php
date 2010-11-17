This page is experimental and will change in a future release.
    
<div id="widgets-left"> 

	<?php
		foreach ($projects as $project) {
	?>

	<div id="available-widgets" class="widgets-holder-wrap"> 
		<div class="sidebar-name"> 
		<div class="sidebar-name-arrow"><br /></div> 
			<h3>
				[ID: <?php echo $project->id; ?>] 
				<?php echo $project->title; ?>
			</h3>
		</div> 
		<div class="widget-holder"> 
		<p class="description"><?php echo $project->description; ?></p> 

    	<div id="<?php echo $project->title ?>" class="pl-overview-tasklist">
        	<table class="pl-overview" width="100%">
        	
        	<thead>
        		<th>ID</th>
        		<th>Name</th>
        		<th>Progress</th>
        		<th>&nbsp;</th>
        		<th>Start</th>
        		<th>End</th>
        	</thead>
        	
        	<?php 
            foreach($tasks[$project->title] as $task) {
            	$script .= 'jQuery("#description-'.$id.'-'.$task->id.'").hide();
            		jQuery("#progress-'.$id.'-'.$task->id.'").progressbar({value: '.$task->complete.'});';
            		
            ?>
            	<tr>
            		<td><?php echo $task->id; ?></td>
            		<td><?php echo $task->title; ?></td>
            		
            		<td id="pm-data-<?php echo $task->id ?>" width="300">
            			<div id="progress-<?php echo $id.'-'.$task->id; ?>" class="progressbar"></div>
            		</td>
            		
            		<td><?php echo $task->complete ?>%</td>
            		
            		<td><?php echo $task->start; ?></td>
            		<td><?php echo $task->end; ?></td>
            	</tr>
            	
            	<tr class="pl-overview-description">
            		<td>&nbsp;</td>
            		<td>&nbsp;</td>
            		<td colspan="4">
            			<p class="description"><?php echo $task->description; ?></p>
            		</td>
            	</tr>
            <?php 	
            }
            ?>
            </table>
        </div>
        

		</div> 
	</div>
	<?php 
    }
	?>
</div> 

<script>
	<?php echo $script; ?>
</script>
            