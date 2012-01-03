<table width="100%">

	<?php if( Propel_Options::option('show_start_date' ) ) : ?>
	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo esc_attr($start); ?>" /></td>
	</tr>
	<?php endif; ?>

	<?php if( Propel_Options::option('show_end_date' ) ) : ?>
	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo esc_attr($end); ?>" /></td>
	</tr>
	<?php endif; ?>

	<tr>
		<td><p>Priority</p></td>
		<td>
			<select name="priority">
				<?php
				$priorities = propel_get_priorities();
				for($i = 0; $i < count($priorities); $i++) :
					echo "<option value='$i'".selected($priority, $i).">$priorities[$i]</option>";
				endfor;
				?>
            </select>
        </td>
    </tr>
    <?php if( Propel_Options::option('show_client' ) ) : ?>
	<tr>
		<td><p>Progress</p></td>
		<td>
			<select name="complete">
				<?php
				for ($i = 0; $i <= 100; $i = $i+5) :
					echo "<option value='$i'".selected($complete, $i).">$i</option>";
				endfor;
				?> 
			</select>	
		</td>
	</tr>
	<?php endif; ?>

	<?php if( Propel_Options::option('show_client' ) ) : ?>
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
	<?php endif; ?>
	
</table>