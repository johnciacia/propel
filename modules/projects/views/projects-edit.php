<?php 
$months = array("01" => "January", 
 			    "02" => "February", 
 			    "03" => "March", 
 			    "04" => "April", 
 			    "05" => "May", 
 			    "06" => "June", 
 			    "07" => "July", 
 			    "08" => "August", 
 			    "09" => "September", 
 			    "10" => "October", 
 			    "11" => "November", 
 			    "12" => "December");

list($start_year, $start_month, $start_day) = explode("-", $project->start);	
list($end_year, $end_month, $end_day) = explode("-", $project->end);

?>
<form method="POST" action="admin.php?page=propel-projects&action=_update">

<table>
	<tr>
		<td>Title</td>
		<td><input type="text" value="<?php echo $project->title ?>" name="title" /></td>
	</tr>

	<tr>
		<td>Start Date</td>
		<td>
			<select name="start_month">
				<?php 
					foreach($months as $value => $m) {
						if($start_month == $value) {
							echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
						} else {
							echo '<option value="'.$value.'">' . $m . '</option>';
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
							echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
						} else {
							echo '<option value="'.$value.'">' . $m . '</option>';
						}
					}
				
				?>
				
			</select>
			<input type="text" name="end_day" class="day" value="<?php echo $end_day; ?>"/>
			<input type="text" name="end_year" class="year" value="<?php echo $end_year; ?>" />
		</td>
	</tr>
		
	<tr>
		<td>Description</td>
		<td><textarea class="ion_description" name="description"><?php echo $project->description ?></textarea><br /></td>
	</tr>
		
	<tr>
		<td>&nbsp;</td>
		<td>
            <input type="hidden" value="<?php echo $project->id ?>" name="project_id" />
            <input type="submit" class="button-secondary action" value="Save" />
            <input type="button" value="<?php esc_attr_e('Cancel'); ?>" name="newproject" id="newproject" onclick="window.location.href='admin.php?page=propel-projects'" class="button-secondary action" />
		</td>
	</tr>
</table>
    
</form>