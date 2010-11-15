<form method="GET">
	<input type="hidden" value="propel-projects" name="page" />
	<div class="tablenav">
		<div class="alignleft actions">

			<select name="action">
				<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
				<option value="delete"><?php _e('Delete'); ?></option>
				<option value="complete"><?php _e('Complete'); ?></option>
			</select> 
			
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" onClick="return confirm('You are about to perform this action on the selected projects and all associated tasks.\n \'Cancel\' to stop, \'OK\' to continue.')" class="button-secondary action" />

    		<input type="button" value="<?php esc_attr_e('Add Project'); ?>"
    			name="newproject" id="newproject"
    			onclick="window.location.href='admin.php?page=propel-projects&action=create'"
    			class="button-secondary action" />	

		</div>

		<div class="view-switch">
			<a href="<?php echo esc_url(add_query_arg('mode', 'list', remove_query_arg('action', $_SERVER['REQUEST_URI'])))?>">
				<img <?php if ('list' == $mode) echo 'class="current"'; ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('List View')?>" alt="<?php _e('List View')?>" />
			</a> 
			<a href="<?php echo esc_url(add_query_arg('mode', 'excerpt', remove_query_arg('action', $_SERVER['REQUEST_URI'])))?>">
				<img <?php if ('excerpt' == $mode) echo 'class="current"'; ?> id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('Excerpt View')?>" alt="<?php _e('Excerpt View')?>" />
			</a>
		</div>

		<div class="clear"></div>

		<table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" class="check-column">
						<input type="checkbox" name="check_all" id="check_all" class="checkbox" />
					</th>
					<th scope="col"><?php _e('ID'); ?></th>
					<th scope="col"><?php _e('Name'); ?></th>
					<th scope="col"><?php _e('Start'); ?></th>
					<th scope="col"><?php _e('End'); ?></th>
					<th scope="col"><?php _e('Tasks'); ?></th>
				</tr>
			</thead>
		
			<tfoot>
				<tr>
					<th scope="col" class="check-column">
						<input type="checkbox" name="check_all" id="check_all" class="checkbox" />
					</th>
					<th scope="col"><?php _e('ID'); ?></th>
					<th scope="col"><?php _e('Name'); ?></th>
					<th scope="col"><?php _e('Start'); ?></th>
					<th scope="col"><?php _e('End'); ?></th>
					<th scope="col"><?php _e('Tasks'); ?></th>			
				</tr>
			</tfoot>
		
			<tbody>
	
			<?php
			foreach ($projects as $project) {
			
				$edit_link = admin_url('admin.php?page=propel-projects&amp;action=edit&amp;id=') . $project->id;
				$delete_link = admin_url('admin.php?page=propel-projects&amp;action=delete&amp;id=') . $project->id;
				$complete_link = admin_url('admin.php?page=propel-projects&amp;action=complete&amp;id=') . $project->id;
				
				$total = count($this->model->getTasks(6, $project->id));
				$complete = count($this->model->getTasks(7, $project->id));   
				$incomplete = $total - $complete; 
			            	
				$actions = array();
				$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
				$actions['complete'] = "<a class='submitcomplete' href='" . $complete_link . "'>" . __('Complete') . "</a>";
				$actions['delete'] = "<a class='submitdelete' href='" . $delete_link . "' onclick=\"if ( confirm('" . esc_js(sprintf(__("You are about to delete the project '%s' and all tasks associated with it.\n  'Cancel' to stop, 'OK' to delete."), $project->title)) . "') ) { return true;}return false;\">" . __('Delete') . "</a>"; 
			    ?>                 
			    <tr id="link-' <?php echo $project->id; ?>" valign="middle">
			    
					<th scope="row" class="check-column"><input type="checkbox" name="check[]" value="<?php echo esc_attr($project->id); ?>" /></th>
					<td><?php echo $project->id; ?></td>
					<td width="65%">
						<strong>
							<a class="row-title" href="<?php echo $edit_link; ?>"><?php echo $project->title ?></a>
						</strong>
						<br />
						
						<?php if ($mode == "excerpt") echo $project->description; ?>	
						<div class="row-actions">
							<span class="edit"><?php echo $actions['edit'] ?> | </span>
							<span class="complete"><?php echo $actions['complete'] ?> | </span>
							<span class="delete"><?php echo $actions['delete'] ?></span>
			       		</div>
			       	</td>
			        
			        
					<td><?php echo date("Y/m/d", strtotime($project->start)); ?></td>
					<td><?php echo date("Y/m/d", strtotime($project->end)); ?></td>
					<td>
					<?php 
						if ($mode == "excerpt") {
							echo "Total: " . $total . "<br />";
							echo "Complete: " . $complete . "<br />";
							echo "Incomplete: " . $incomplete;
						} else {
							echo  $total . "<br />";
						}
					?>
					</td>
				</tr>	
			<?php 		
				}
			?>
			</tbody>
		</table>

	</div>
</form>