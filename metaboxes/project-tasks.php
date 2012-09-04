<table width="100%" class="gen-table tasks-table" id="propel-tasks">
	<thead>
		<tr>
			<!--Change back to elements instead of colspan. The datables will raise an error using colspan when initialize-->
            <th></th>
            <th></th>
            <th></th>
			<th class="sortable"><p>Title</p></th>
			<th class="sortable"><p>Contributors</p></th>
			
			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
				<th class="sortable"><p>Started</p></th>
			<?php endif; ?>
			
			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
				<th class="sortable"><p>Due</p></th>
			<?php endif; ?>
			
			<th class="sortable"><p>Progress</p></th>
		</tr>
	</thead>
	
	<?php
	foreach($posts as $post) {
		$task = get_post($post->post_id);
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		if( $start ){
			//$start = date( get_option( 'date_format' ), $start );
			$start = date("m-d-y H:i", $start);
		}

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		if( $end ){
			//$end = date( get_option( 'date_format' ), $end);
			$end = date("m-d-y H:i", $end);
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
		
		
		
		/*
		* rob_eyouth : added by rob to show task for the current user and if user is admin
		*/
		//if user is admin
		//$current_user = wp_get_current_user();		
		//if ( $current_user->ID == $userdata->ID || $current_user->ID == 1) { 
		?>
		<tr id="<?php esc_attr_e( $task->ID ); ?>">
		
			<td class="gen-icon gen-delete-icon">
				<a href="post.php?action=propel-delete&post=<?php esc_attr_e( $task->ID ); ?>&_wpnonce=<?php echo $nonce; ?>" title="Delete">Delete</a></td>

			<td class="gen-icon gen-edit-icon">
				<a href="#" title="Edit">Edit</a></td>

			<td class="gen-icon gen-<?php echo $x; ?>checked-icon">
				<a href="post.php?action=complete&post=<?php esc_attr_e( $task->ID ); ?>" title="Mark as complete">Complete</a></td>
				
			<td class="title" data-value="<?php esc_attr_e($task->post_title); ?>" style="width: 400px;">
				<p id="edit_title_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($task->post_title); ?></p>
                <?php 
					$len = strlen($task->post_content);
					if ( $len > 75 ) :
				 ?>
	            <div id="desc_<?php esc_attr_e( $task->ID ); ?>" style="margin:-8px 0 3px 1px;" class="tooltip" title="<?php esc_html_e($task->post_content); ?>"><small style="color:#999;text-shadow:1px 1px white"><?php esc_html_e( substr($task->post_content,0,75).' ...'); ?></small></div>
                <?php else : ?>
				<div id="desc_<?php esc_attr_e( $task->ID ); ?>" style="margin:-8px 0 3px 1px;" class="tooltip" title="<?php esc_html_e($task->post_content); ?>"><small style="color:#999;text-shadow:1px 1px white"><?php esc_html_e($task->post_content); ?></small></div               
                ><?php endif  ?>
            </td>

			<td class="owner" data-value="<?php esc_attr_e( $author ); ?>">
				<p id="edit_owner_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($author); ?></p>
			</td>

			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $start ); ?>">
				<p style="font-size: 10px; color: #999;" id="edit_sdate_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($start); ?></p>
			</td>
			<?php endif; ?>

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $end ); ?>">
				<p style="font-size: 10px; color: #999;" id="edit_edate_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($end); ?></p></td>
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