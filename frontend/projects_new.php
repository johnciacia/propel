<div class="propel-projects">
	<ul>
		<?php 
			$script = "";
			foreach ($projects as $project) {
				echo '<li><a href="#project-' . $project->ID . '"><span>' 
					. $project->post_title . '</span></a></li>';
			}
		?>
	</ul>   

    <?php
    foreach ($projects as $project) :

    ?>


<div id="project-<?php echo $project->ID ?>">
<table width="100%">
	
	<?php 
	foreach($tasks[$project->post_name] as $task) : 
		$meta = (object)get_post_meta($task->ID, "_propel_task_metadata", true);
		$script .= 'jQuery("#progress-'.$task->ID.'").progressbar({value: '.$meta->complete.'});';
	?>
	<tbody>
	<tr>
		<td width="20%"><p><?php echo $task->post_title; ?></p></td>
		<td width="80%">
			<div id="progress-<?php echo $task->ID; ?>" class="progressbar"></div>
		</td>
		<td width="10%"><p><?php echo $meta->complete; ?>%</p></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><p><?php echo $task->post_content; ?></p></td>
	</tr>
	
	<tr>
		<td>&nbsp</td>
		<td colspan="2"><p><small>Priority: <?php echo $meta->priority; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Due: <?php echo $meta->end; ?></p></small></td>
	</tr>
	</tbody>
	<?php endforeach; ?>

</table>
</div>
<?php endforeach; ?>

</div>

<script>
	jQuery(document).ready(function() {
		jQuery(".propel-projects").tabs().wrap('<div class="propel">');
		<?php echo $script; ?>
	});
</script>
