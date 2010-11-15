<form method="POST" action="admin.php?page=propel-tasks&action=_update">

<table>
	<tr>
		<td>Name</td>
		<td><input type="text" name="title" value="<?php echo $task->title; ?>" /></td>
	</tr>

	<tr>
		<td>Project</td>
		<td>
			<select name="project">
                <?php
                foreach ($projects as $project) {
                	if($task->pid == $project->id) {
                		echo '<option value="'.$project->id.'" selected="selected">'.$project->title.'</option>';
                	} else {
                		echo '<option value="'.$project->id.'">'.$project->title.'</option>';
                	}
                    
                }
                ?>
            </select>
		</td>
	</tr>

	<tr>
		<td>User</td>
		<td>
			<select name="user">
				<option value="0">Anyone</option>
                <?php
                foreach($users as $user) {
                	if($user->id == $task->uid) {
                		echo '<option value="' . $user->id . '" selected="selected">' . $user->user_nicename . '</option>';                	
                	} else {
                		echo '<option value="' . $user->id . '">' . $user->user_nicename . '</option>';
                	}
                }
                ?>
            </select>
        </td>
    </tr>
    	
	<tr>
		<td>Priority</td>
		<td>
			<select name="priority">
				<option value="1">Low</option>
                <?php
                for ($i = 1; $i <= 10; $i ++) {
                	if($task->priority == $i) {
                		echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
                	} else {
            			echo '<option value="'.$i.'">'.$i.'</option>';	
                	}
                    
                
                }
                ?>
                <option value="10">High</option>
            </select>
        </td>
    </tr>

	<tr>
		<td>Start Date</td>
		<td>
			<select name="start_month">
				<?php 
					foreach($months as $value => $m) {
						if($start_month == $value) {
							echo '<option value="'.$value.'" selected="selected">'.$m.'</option>';
						} else {
							echo '<option value="'.$value.'">'.$m.'</option>';
						}
					}
				
				?>
				
			</select>
			<input type="text" name="start_day" class="day" value="<?php echo $start_day; ?>"/>
			<input type="text" name="start_year" class="year" value="<?php echo $start_year; ?>" />
		</td>
	</tr>

	<tr>
		<td>End Date</td>
		<td>
			<select name="end_month">
				<?php 
					foreach($months as $value => $m) {
						if($end_month == $value) {
							echo '<option value="'.$value.'" selected="selected">'.$m.'</option>';
						} else {
							echo '<option value="'.$value.'">'.$m.'</option>';
						}
					}
				
				?>
				
			</select>
			<input type="text" name="end_day" class="day" value="<?php echo $end_day; ?>"/>
			<input type="text" name="end_year" class="year" value="<?php echo $end_year; ?>" />
		</td>
	</tr>
	
	
	<tr>
		<td>Completed</td>
		<td>
            <select name="complete">
                <?php
                for ($i = 0; $i <= 100; $i ++) {
                    if($task->complete == $i) {
                		echo '<option value="'.$i.'" selected="selected">'.$i.'%</option>';
                	} else {
            			echo '<option value="'.$i.'">'.$i.'%</option>';	
                	}
                
                }
                ?> 
            </select>		
		</td>
	</tr>
	
	<tr>
		<td>Description</td>
		<td><textarea class="ion_description" name="description"><?php echo $task->description; ?></textarea></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" value="<?php echo $task->id; ?>" name="id" />	
            <input type="submit" class="button-secondary action" value="Save" />
            <a href="admin.php?page=propel-tasks" class="button">Cancel</a>	
		</td>
	</tr>
</table>

</form>