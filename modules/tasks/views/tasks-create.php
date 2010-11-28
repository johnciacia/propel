<form method="POST" action="admin.php?page=propel-tasks&action=_create">

<table class="propel">
	<tr>
		<td>Name</td>
		<td><input type="text" name="title" /></td>
	</tr>

	<tr>
		<td>Project</td>
		<td>
			<select name="project">
                <?php
                foreach ($projects as $project) {
                    echo '<option value="' . $project->id . '">' . $project->title . '</option>';
                }
                ?>
            </select>
		</td>
	</tr>

	<tr>
		<td>Start Date</td>
		<td>
			<select name="start_month">
				<?php 
					foreach($months as $value => $m) {
						if($m == $month) {
							echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
						} else {
							echo '<option value="'.$value.'">' . $m . '</option>';
						}
					}
				
				?>
				
			</select>
			<input type="text" name="start_day" class="propel-day" value="<?php echo $day; ?>"/>
			<input type="text" name="start_year" class="propel-year" value="<?php echo $year; ?>" />
		</td>
	</tr>

	<tr>
		<td>End Date</td>
		<td>
			<select name="end_month">
				<?php 
					foreach($months as $value => $m) {
						if($m == $month) {
							echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
						} else {
							echo '<option value="'.$value.'">' . $m . '</option>';
						}
					}
				
				?>
				
			</select>
			<input type="text" name="end_day" class="propel-day" value="<?php echo $day; ?>"/>
			<input type="text" name="end_year" class="propel-year" value="<?php echo $year; ?>" />
		</td>
	</tr>

	<tr>
		<td>User</td>
		<td>
			<select name="user">
				<option value="0">Anyone</option>
                <?php
                foreach($users as $user) {
                    echo '<option value="' . $user->id . '">' . $user->user_nicename . '</option>';
                
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
                    echo '<option value="' . $i . '">' . $i . '</option>';
                
                }
                ?>
                <option value="10">High</option>
            </select>
        </td>
    </tr>
    		
	<tr>
		<td>Completed</td>
		<td>
            <select name="complete">
                <?php
                for ($i = 0; $i <= 100; $i ++) {
                    echo '<option value="' . $i . '">' . $i . '%</option>';
                
                }
                ?> 
            </select>		
		</td>
	</tr>
	
	<tr>
		<td>Description</td>
		<td><textarea class="propel-description" name="description"></textarea></td>
	</tr>

	<!-- -	
	<tr>
		<td>Send E-Mail</td>
		<td><input type="checkbox" name="email" />
	</tr>
	 -->
	 
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" value="true" name="create_task" />		
            <input type="submit" class="button-secondary action" value="Save" />
            <a href="admin.php?page=propel-tasks" class="button">Cancel</a>		
		</td>
	</tr>
</table>
</form>