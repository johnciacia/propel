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

	<?php if( Propel_Options::option('show_end_date' ) ) : ?>
	<tr>
		<td><p>End Date</p> </td>
		<td><input type="text" name="task_end_date" class="widefat date" /></td>
	</tr>
	<?php endif; ?>

	<tr>
		<td><p>Priority</p> </td>
		<td>
			<select name="task_priority">
			<?php
			$priorities = propel_get_priorities();
			for($i = 0; $i < count($priorities); $i++) :
				echo "<option value='$i'>$priorities[$i]</option>";
			endfor;
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><p>Owner</p></td>
		<td>
			<?php  
			$current_user = wp_get_current_user();
			wp_dropdown_users( array( 
				'show_option_none' => 'Unassigned',
				'name' => 'task_author', 
				'selected' => $current_user->ID) ); 
			?>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="text-align: right;"><input type="button" id="add-task" class="button-primary" value="Add Task" /></td>
	</tr>
</table>