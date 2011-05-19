<div class="propel-projects">

<table width="100%">

	<?php foreach($tasks as $task) : 
		$script .= 'jQuery("#progress-'.$id.'-'.$task->id.'").progressbar({value: '.$task->complete.'});';
	?>
	<tbody>
	<tr>
		<td width="20%"><p><?php echo $task->title; ?></p></td>
		<td width="80%">
			<div id="progress-<?php echo $id.'-'.$task->id; ?>" class="progressbar"></di
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

<script>
	jQuery(document).ready(function() {
		<?php echo $script; ?>
	});
</script>