<table class="metaboxes-add-task">
	<tr>
		<td>
			<input type="text" name="task_title" placeholder="Title" class="widefat" />
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<textarea name="task_description" placeholder="Description" class="widefat"></textarea>
			</td>
	</tr>

	<?php if( Propel_Options::option('show_end_date' ) ) : ?>
	<tr>
		<td>
			<input type="text" name="task_end_date" placeholder="End Date" class="widefat date" />
			</td>
	</tr>
	<?php endif; ?>

	<tr>
		<td>
			<label>Priority</label>
			<select name="task_priority" class="task-priority">
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
		<td>
			<label>Owner</label>
			<?php  
			$current_user = wp_get_current_user();
			wp_dropdown_users( array(
				'class' => 'task-owner',
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