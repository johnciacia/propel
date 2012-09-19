<style>
#propel_deleted_tasks .inside {
	margin: 0;
	padding: 0;
}
.propel_restore{
background:url(<?php echo plugins_url('/propel/ui/gen/images/unchecked.png'); ?>) no-repeat !important;
}
</style>
<table width="100%" class="gen-table tasks-table" id="propel-deleted">
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
		$task = get_post($post->ID);
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
			
			if ($task->post_status == 'trash'){
				$status = "deleted";
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
		
		/*
		* rob_eyouth : added by rob to show task for the current user and if user is admin
		*/
		//if user is admin
		//$current_user = wp_get_current_user();		
		//if ( $current_user->ID == $userdata->ID || $current_user->ID == 1) { 
		?>
		<tr id="<?php esc_attr_e( $task->ID ); ?>">
		
			<td class="gen-icon gen-delete-icon">
			 <!-- aps2012 -->
			    <a href="javascript:;" class = "propel_trashtask" alt="<?php esc_attr_e( $task->ID ); ?>" title="Delete">Delete</a> 
				<!--
				<a href="post.php?action=propel-delete&post=<?php//esc_attr_e( $task->ID ); ?>&_wpnonce=<?php// echo $nonce; ?>" title="Delete">Delete</a>--></td>
			
			<td class="gen-icon gen-deleted-icon db-updated" <?php $completed; ?> >
            	<?php $status == 'due' ? $status = 'Due today or tomorrow' : $status; ?>
				<p class="propeltooltip" <?php $completed; ?> title="deleted"></p></td>

			<td class="gen-icon">
				<a href="javascript:;" class="propel_restore" alt=<?php esc_attr_e( $task->ID ); ?>" title="Restore">Restore</a></td>
				
			<td class="title" data-value="<?php esc_attr_e($task->post_title); ?>" style="width: 400px;">
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
				<p id="edit_owner_<?php esc_attr_e( $task->ID ); ?>"><?php esc_html_e($author); ?></p>
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