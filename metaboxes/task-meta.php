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

	<?php if( Propel_Options::option('show_start_date' ) ) : ?>
	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo $start; ?>" /></td>
	</tr>
	<?php endif; ?>

	<?php if( Propel_Options::option('show_end_date' ) ) : ?>
	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo $end; ?>" /></td>
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

	<tr>
		<td><p>Type</p></td>
		<td>
			<select name="tax_input[propel_type]">
			<?php 
			$terms = get_terms( 'propel_type', array( 'hide_empty' => 0 ) );
			foreach( $terms as $term) :
				echo "<option value='$term->name'".selected($type, $term->term_id).">$term->name</option>";
			endforeach;
			?>
			</select>
		</td>
	</tr>

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
	
</table>