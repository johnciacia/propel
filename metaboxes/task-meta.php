<table width="100%">

	<tr>
		<td>Project</td>
		<td>
		<?php

		$projects = get_posts('post_type=propel_project&post_status=publish');
		echo "<select name='parent_id' id='parent_id'>";
		foreach($projects as $project) :
			if($project->ID == $parent) {
				echo '<option value=' . $project->ID . ' selected>';
				echo $project->post_title;
				echo '</option>';
			} else {
				echo '<option value=' . $project->ID . '>';
				echo $project->post_title;
				echo '</option>';				
			}
		endforeach;

		?>
		</td>
	</tr>
	
	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo $start; ?>" /></td>
	</tr>

	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo $end; ?>" /></td>
	</tr>
	
	<tr>
		<td><p>Priority</p></td>
		<td>
			<select name="priority">
				<option value="1">Low</option>
	                <?php
	                for ($i = 1; $i <= 10; $i ++) {
						if($i == $priority) {
	                    	echo '<option value="' . $i . '" selected>' . $i . '</option>';
						} else {
						    echo '<option value="' . $i . '">' . $i . '</option>';
	                	}
					}
	                ?>
                <option value="10">High</option>
            </select>
        </td>
    </tr>

	<tr>
		<td><p>Completed</p></td>
		<td>
            <select name="complete">
	            <?php
	            for ($i = 0; $i <= 100; $i++) {
					if($i == $complete)
						echo '<option value="' . $i . '" selected>' . $i . '%</option>';
	                else 
	                	echo '<option value="' . $i . '">' . $i . '%</option>';
	            }
	            ?> 
            </select>		
		</td>
	</tr>
	
</table>