<?php $meta = get_post_meta($project->ID, "_propel_project_metadata", true); ?>

<form action="admin-post.php" method="POST">
<table width="100%">
	<tr>
		<td width="20%"><p>Name: </p></td>
		<td><input style="width:100%" type="text" name="title" value="<?php echo $project->post_title; ?>" /></td>
	</tr>

	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo $meta['start']; ?>" /></td>
	</tr>

	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo $meta['end']; ?>" /></td>
	</tr>
	
	<tr>
		<td><p>Priority</p></td>
		<td>
			<select name="priority">
				<option value="<?php $meta['priority']; ?>"><?php $meta['priority']; ?></option>
				<option value="1">Low</option>
	                <?php
	                for ($i = 1; $i <= 10; $i ++) {
						if($i == $meta['priority']) {
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
					if($i == $meta['complete'])
						echo '<option value="' . $i . '" selected>' . $i . '%</option>';
	                else 
	                	echo '<option value="' . $i . '">' . $i . '%</option>';
	            }
	            ?> 
            </select>		
		</td>
	</tr>
	
	<tr>
		<td><p>Description</p></td>
		<td><textarea style="width:100%" name="description"><?php echo $project->post_content; ?></textarea></td>
	</tr>
	
	<tr>
		<td colspan="2"><input class="button-primary" type="submit" value="Submit"></td>
	</tr>
	
</table>

<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="action" value="propel_update_project" />
</form>