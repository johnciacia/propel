<table width="100%">
	<tr>
		<td><p>Title</p></td>
		<td><input type="text" name="task_title" class="widefat" /></td>
	</tr>

	<tr>
		<td colspan="2"><p>Description</p></td>
	</tr>

	<tr>
		<td colspan="2"><textarea name="task_description" class="widefat"></textarea></td>
	</tr>

	<tr>
		<td><p>End Date</p> </td>
		<td><input type="text" name="task_end_date" class="widefat date" /></td>
	</tr>

	<tr>
		<td><p>Priority</p> </td>
		<td>
			<select name="task_priority">
			<?php for( $i = 0; $i <= 10; $i++ ) : ?>
			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
			<?php endfor; ?>
			</select>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="text-align: right;"><input type="button" id="add-task" class="button-primary" value="Add Task" /></td>
	</tr>
</table>