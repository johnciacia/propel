<div class="propel-projects">
	<ul>
		<?php 
			$script = "";
			foreach ($projects as $project) {
				echo '<li><a href="#project-' . $project->id . '"><span>' 
					. $project->title . '</span></a></li>';
			}
		?>
	</ul>   

    <?php
    foreach ($projects as $project) :
    ?>


<div id="project-<?php echo $project->id ?>">
<table width="100%">
	
	<?php 
	foreach($tasks[$project->title] as $task) : 
		$script .= 'jQuery("#progress-'.$task->id.'").progressbar({value: '.$task->complete.'});';
	?>
	<tbody>
	<tr>
		<td width="20%"><p><?php echo $task->title; ?></p></td>
		<td width="80%">
			<div id="progress-<?php echo $task->id; ?>" class="progressbar"></div>
		</td>
		<td width="10%"><p><?php echo $task->complete; ?>%</p></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><p><?php echo $task->description; ?></p></td>
	</tr>
	
	<tr>
		<td>&nbsp</td>
		<td colspan="2"><p><small>Priority: <?php echo $task->priority; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Due: <?php echo $task->end; ?></p></small></td>
	</tr>
	</tbody>
	<?php endforeach; ?>

</table>
</div>
<?php endforeach; ?>

</div>

<script>
	jQuery(document).ready(function() {
		jQuery(".propel-projects").tabs();
		<?php echo $script; ?>
	});
</script>