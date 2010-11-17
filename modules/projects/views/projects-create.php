<form method="POST" action="admin.php?page=propel-projects&action=_create">

<table>
	<tr>
		<td>Title</td>
		<td><input type="text" name="title" /></td>
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
			<input type="text" name="start_day" class="day" value="<?php echo $day; ?>"/>
			<input type="text" name="start_year" class="year" value="<?php echo $year; ?>" />
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
			<input type="text" name="end_day" class="day" value="<?php echo $day; ?>"/>
			<input type="text" name="end_year" class="year" value="<?php echo $year; ?>" />
		</td>
	</tr>
		
	<tr>
		<td>Description</td>
		<td><textarea class="ion_description" name="description"></textarea></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td>
            <input type="submit" class="button-secondary action" value="Save" />
			<input type="button" value="<?php esc_attr_e('Cancel'); ?>" name="newproject" id="newproject" onclick="window.location.href='admin.php?page=propel-projects'" class="button-secondary action" />
		</td>
	</tr>
</table>
    
</form>