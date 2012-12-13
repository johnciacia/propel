<!-- Spencer tried adding this but adding tasks simply doesn't work. It also appears in the "completed" list.
<table>
	<tr>
		<td>
			<input class="metabox-add-task-title" type="text" name="task_title" id="_task_title" placeholder="Title" class="widefat" />

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<input class="metabox-add-task-title" type="text" name="task_end_date" placeholder="End Date" class="widefat date" />
			<?php endif; ?>

			<label>Manager:</label>
			<?php 
			$current_user = wp_get_current_user();
			$args = array(
			'class' => 'metabox-add-task-user',
			'name' => 'propel_post_author',
			'show_option_none' => 'Unassigned',
			'orderby' => 'display_name',
			'selected' => $current_user->ID
			);
			wp_dropdown_users( $args );
			?>
			<input class="metabox-add-task-button button-primary" type="button" id="add-task" value="Add Task" />
		</td>
	</tr>
	<tr>
		<td>
			<textarea class="metabox-add-task-description widefat" name="task_description" id="_task_desc" placeholder="Description"></textarea>
		</td>
	</tr>
</table>
-->
<table width="100%" class="gen-table tasks-table" id="propel-tasks">
	<thead>
		<tr>
			<!--Change back to elements instead of colspan. The datables will raise an error using colspan when initialize-->
            <th width=5%></th>
            <th width=5%></th>
            <th width=5%></th>
			<th class="sortable" width=15%><p>Title</p></th>
			<th class="sortable" width=15%><p>Contributors</p></th>
			
			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
				<th class="sortable" width=15%><p>Started</p></th>
			<?php endif; ?>
			
			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
				<th class="sortable" width=15%><p>Due</p></th>
			<?php endif; ?>
			
			<th class="sortable" width=10%><p>Progress</p></th>
		</tr>
	</thead>
	
	<?php
	foreach($posts as $post) {
		$task = get_post($post->post_id);
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		if( $start ){
			//$start = date( get_option( 'date_format' ), $start );
			$start = date("m-d-y h:i a", $start);
		}

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		if( $end ){

			$day   = date('d'); // Day of the countdown
			$month = date('m'); // Month of the countdown
			$year  = date('Y'); // Year of the countdown
			$hour  = date('H'); // Hour of the day (east coast time)
			
			$calculation = ( $end - time() ) / 3600;
			$hours = (int)$calculation + 24;
			$days  = (int)( $hours / 24 ) - 1;
			
			$hours_remaining = $hours-($days*24)-24;
			
			if ( $hours < 0 && $hours > -24 ) {
				$status = "due";
			}else if ( $hours < -24 ) {
				$status = "past-due";
			}else{
				$status = "published";
			}
			
			$end = date("m-d-y h:i a", $end);
			
		}

		if( $task->post_author ) {
			$userdata = get_userdata( $task->post_author );
			$authid = $userdata->ID; 
			$author = $userdata->display_name;
		} else {
			$authid = '-1';
			$author = "Unassigned";
		}	
							
		$x = ($progress == 100) ? "" : "un";
		$nonce = wp_create_nonce('propel-trash');
		$completed = ($progress == 100) ? "style='width:0;margin:0;padding:0;'" : "";
		
		$usercnt = get_post_meta($task->ID,'_propel_user',true);
		
		$html='';
		for ($i=0; $i < $usercnt; $i++){
			$contributor = get_post_meta($task->ID,'_propel_user_'.$i,true);
			if ( $contributor == 'Undefined' || $contributor == 'undefined' ){
				$html .= '<span id="-1" class="span_contr">Undefined</span>';		
			}else{
				$user = get_userdatabylogin($contributor);
				$html .= '<span id="'.$user->ID.'" class="span_contr">'.$user->display_name.'</span>';
			}
		}
		
		/*
		* rob_eyouth : added by rob to show task for the current user and if user is admin
		*/
		//if user is admin
		//$current_user = wp_get_current_user();		
		//if ( $current_user->ID == $userdata->ID || $current_user->ID == 1) { 
		?>
		<tr id="<?php esc_attr_e( $task->ID ); ?>">
		
			<td class="gen-icon gen-delete-icon">
				<a href="javascript:;" class = "propel_trashtask" alt="<?php esc_attr_e( $task->ID ); ?>" title="Delete">Delete</a></td>
			
			<td class="gen-icon db-updated gen-<?php echo $status; ?>-icon" <?php $completed; ?> >
            	<?php $status == 'due' ? $status = 'Due today or tomorrow' : $status; ?>
				<p class="propeltooltip" <?php $completed; ?> title="<?php echo $status; ?>"></p></td>

			<td class="gen-icon gen-<?php echo $x; ?>checked-icon">
				<a href="post.php?action=complete&post=<?php esc_attr_e( $task->ID ); ?>" title="Mark as complete">Complete</a></td>
				
			<td class="title" data-value="<?php esc_attr_e($task->post_title); ?>" style="min-width: 200px;">
            	<?php 
					$len = strlen($task->post_content);
					$len <= 0 ? $pad = "style='padding-bottom:10px;'" : $pad ='';
				?>
				<p id="edit_title_<?php esc_attr_e( $task->ID ); ?>" <?php echo $pad; ?>><?php esc_html_e($task->post_title); ?></p>
                <?php 					
					if ( $len > 75 ) :
				 ?>
	            <div id="desc_<?php esc_attr_e( $task->ID ); ?>" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="<?php esc_html_e($task->post_content); ?>"><small style="color:#999;text-shadow:1px 1px white;padding-bottom:5px;"><?php esc_html_e( substr($task->post_content,0,75).' ...'); ?></small></div>
                <?php else : ?>
				<div id="desc_<?php esc_attr_e( $task->ID ); ?>" style="margin:-8px 0 3px 1px;" class="propeltooltip" title="<?php esc_html_e($task->post_content); ?>"><small style="color:#999;text-shadow:1px 1px white;padding-bottom:5px;"><?php esc_html_e($task->post_content); ?></small></div               
                ><?php endif  ?>
            </td>

			<td class="owner" data-value="<?php esc_attr_e( $author ); ?>">
				<?php if(empty($html)): ?>
					<p id="edit_owner_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($author); ?></p>
				<?php else: ?>
					<p id="edit_contr_<?php esc_attr_e( $task->ID ); ?>"><?php _e($html); ?></p>
				<?php endif; ?>
			</td>

			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $start ); ?>">
				<p style="font-size: 10px; color: #999;" id="edit_sdate_<?php esc_attr_e( $task->ID ); ?>"><?php esc_attr_e( $start ); ?></p>
			</td>
			<?php endif; ?>

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<td data-value="<?php  esc_attr_e( $end ); ?>">
				<p style="font-size: 10px; color: #999;" id="edit_edate_<?php esc_attr_e( $task->ID ); ?>"><?php esc_attr_e( $end ); ?></p></td>
			<?php endif; ?>

			<td data-value="<?php esc_attr_e( $progress ); ?>">
				<p style="font-size: 10px; color: #999;" id="edit_progr_<?php esc_attr_e( $task->ID ); ?>"><progress max="100" value="<?php esc_html_e($progress); ?>">
				</progress></p>
			</td>
		</tr>
		<?php
		//}
	}

	?>

</table>