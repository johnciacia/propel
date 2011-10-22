<table width="100%">

	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo esc_attr($start); ?>" /></td>
	</tr>

	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo esc_attr($end); ?>" /></td>
	</tr>
	
	<tr>
		<td><p>Priority</p></td>
		<td>
			<select name="priority">
				<option value="1">Low</option>
	                <?php
	                for ($i = 1; $i <= 10; $i++) {	
	                    echo "<option value='$i'".selected($priority, $i).">$i</option>";
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
					echo "<option value='$i'".selected($complete, $i).">$i</option>";
	            }
	            ?> 
            </select>		
		</td>
	</tr>
	
	<tr>
		<td><p>Client</p></td>
		<td>
			<select name="owner">
			<?php foreach($users as $user) : ?>
				<?php if($user->ID == $owner) : ?>
					<option value="<?php echo $user->ID; ?>" selected><?php echo $user->display_name; ?></option>
				<?php else : ?>
					<option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>
	
</table>