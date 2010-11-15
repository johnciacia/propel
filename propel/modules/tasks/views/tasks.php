<form method="GET">

	<ul class="subsubsub"> 
		<li class='all'><a href='admin.php?page=propel-tasks&amp;' class="current">All</a> |</li> 
		<li class='moderated'>
			<a href='admin.php?page=propel-tasks&amp;filter=8'>Pending 
				<span class="count">(<span class="pending-count"><?php echo $pending; ?></span>)</span>
			</a>
		</li> 
	</ul> 

	<input type="hidden" value="propel-tasks" name="page" />
	<div class="tablenav">
	
		<div class="alignleft actions">
            <select name="action">
            	<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
            	<option value="delete"><?php _e('Delete'); ?></option>
            	<option value="complete"><?php _e('Complete'); ?></option>
            	<option value="approve"><?php _e('Approve'); ?></option>
            	<option value="unapprove"><?php _e('Unapprove'); ?></option>
            </select> 
            
			<input type="submit" value="<?php esc_attr_e('Apply'); ?>" 
				name="doaction" onClick="return confirm('You are about to perform this action on the selected tasks.\n \'Cancel\' to stop, \'OK\' to continue.')" 
				class="button-secondary action" />
				
     		<input type="button" value="<?php esc_attr_e('Add Task'); ?>"
    			name="newtask" id="newtask"
    			onclick="window.location.href='admin.php?page=propel-tasks&action=create'"
    			class="button-secondary action" />	
    			
    			               	
    	        <select name="filter">
    	        	<option value="0">Filter Tasks</option>
                    <option value="1">Complete</option>
                    <option value="2">Incomplete</option>
                    <option value="3">Priority</option>
                    <option value="4">Start</option>
                    <option value="5">End</option>             		               		
                    <option value="0">Projects</option>
                    <?php foreach($projects as $project) {?>
                    <option value="6-<?php echo $project->id; ?>" class="propel-indent"><?php echo $project->title; ?></option>
                    <?php }?>
                    <option value="8">Pending</option>
                </select>
                
			<input type="submit" value="<?php esc_attr_e('Filter'); ?>" 
				name="action" id="filter" class="button-secondary action" />
		</div>

            <div class="view-switch">
        	<a href="<?php echo $list_mode ?>">
        		<img <?php if ('list' == $mode) echo 'class="current"'; ?> 
        			id="view-switch-list" src="../wp-includes/images/blank.gif" 
        			width="20" height="20" title="<?php _e('List View')?>" 
        			alt="<?php _e('List View')?>" />
        	</a> 
        	<a href="<?php echo $excerpt_mode ?>">
        		<img <?php if ('excerpt' == $mode) echo 'class="current"'; ?> 
        			id="view-switch-excerpt" src="../wp-includes/images/blank.gif" 
        			width="20" height="20" title="<?php _e('Excerpt View')?>" 
        			alt="<?php _e('Excerpt View')?>" />
        	</a>
        		

    		
        </div>

        <div class="clear"></div>


		<table class="widefat" cellspacing="0">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th class="manage-column" width="175" scope="col">Name</th>
					<th class="manage-column" scope="col">User</th>
					<th class="manage-column" scope="col">Start</th>
					<th class="manage-column" scope="col">End</th>
					<th class="manage-column" scope="col">Project</th>
					<th class="manage-column" scope="col">Priority</th>
					<th class="manage-column" scope="col">Complete</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th class="manage-column" scope="col">Name</th>
					<th class="manage-column" scope="col">User</th>
					<th class="manage-column" scope="col">Start</th>
					<th class="manage-column" scope="col">End</th>
					<th class="manage-column" scope="col">Project</th>
					<th class="manage-column" scope="col">Priority</th>
					<th class="manage-column" scope="col">Complete</th>
				</tr>
			</tfoot>

			<tbody>

    		<?php
			foreach ($tasks as $task) {
				$edit_task = admin_url('admin.php?page=propel-tasks&amp;action=edit&amp;id=') . $task->id;
				$delete_task = admin_url('admin.php?page=propel-tasks&amp;action=delete&amp;id=') . $task->id;
				$approve_task = admin_url('admin.php?page=propel-tasks&amp;action=approve&amp;id=') . $task->id;
				$unapprove_task = admin_url('admin.php?page=propel-tasks&amp;action=unapprove&amp;id=') . $task->id;
				$project = $this->model->getProject($task->pid);
				$edit_project = admin_url('admin.php?page=propel-projects&amp;action=edit&amp;id=') . $project->id;
				if($task->uid == 0) {
					$user = "Anyone";
				} else {
					$user = $this->model->getUser($task->uid)->user_nicename;
				}
			?>
			    <tr id="task-<?php echo $task->id; ?>" valign="middle" <?php echo $task->approved ? 'class="approved"' : 'class="unapproved"'; ?>>
			    	<th scope="row" class="check-column"><input type="checkbox" name="check[]" value="<?php echo esc_attr($task->id); ?>" /></th>
			        <td>
			        	<strong>
			        		<a class="row-title" href="<?php echo $edit_task; ?>"><?php echo $task->title; ?></a>
		        		</strong>
		        		<br />
			        	<?php if ($mode == "excerpt") echo $task->description; ?>
			            <div class="row-actions">
			            	<?php if($task->approved == 0) { ?>
			            		<a href="<?php echo $approve_task; ?>">Approve</a> |
			            	<?php } else { ?>
			            		<a href="<?php echo $unapprove_task; ?>">Unapprove</a> |
			            	<?php } ?>
			            	<a href="<?php echo $edit_task; ?>">Edit</a> |	
			            	<a class="submitdelete" href="<?php echo $delete_task; ?>" 
			            	onclick="if (confirm("Hello World")) { return true;}return false;">Delete</a>
			            </div>
			        </td>
			        <td><?php echo $user; ?></td>
			        <td><?php echo date("Y/m/d", strtotime($task->start)); ?></td>
			        <td><?php echo date("Y/m/d", strtotime($task->end)); ?></td>
			        <td><a href="<?php echo $edit_project; ?>"><?php echo $project->title; ?></a></td>
			        <td style="text-align:center;"><?php echo $task->priority; ?></td>    
			        <td style="text-align:center;"><?php echo $task->complete; ?>%</td>         
				</tr>
			    <?php 
			    }
			    ?>
			</tbody>
		</table>
	</div>
</form>