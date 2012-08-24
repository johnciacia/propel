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
			<th colspan="3">
			</th>
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

    <tr id="post_parent_">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
		<?php if( Propel_Options::option('show_start_date' ) ) : ?>
            <td></td>
        <?php endif; ?>
        
        <?php if( Propel_Options::option('show_end_date' ) ) : ?>
            <td></td>
        <?php endif; ?>
        <td></td>
    </tr>
	
	<?php
	foreach($posts as $post) {
		$task = get_post($post->post_id);
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		if( $start )
			$start = date( get_option( 'date_format' ), $start );

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		if( $end )
			$end = date( get_option( 'date_format' ), $end);

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
		$current_user = wp_get_current_user();		
		if ( $current_user->ID == $userdata->ID || $current_user->ID == 1) { 
		?>
		<tr class="toggle" id="<?php esc_attr_e( $task->ID ); ?>">
		
			<td class="gen-icon gen-delete-icon">
				<a href="post.php?action=propel-delete&post=<?php esc_attr_e( $task->ID ); ?>&_wpnonce=<?php echo $nonce; ?>" title="Delete">Delete</a></td>

			<td class="gen-icon gen-edit-icon">
				<a href="post.php?post=<?php esc_attr_e( $task->ID ); ?>&action=edit" title="Edit">Edit</a></td>

			<td class="gen-icon gen-<?php echo $x; ?>checked-icon">
				<a href="post.php?action=complete&post=<?php esc_attr_e( $task->ID ); ?>" title="Mark as complete">Complete</a></td>
				
			<td class="title" class="toggle" data-value="<?php esc_attr_e($task->post_title); ?>" style="width: 400px;">
				<p><?php esc_html_e($task->post_title); ?></p></td>

			<td class="owner" data-value="<?php esc_attr_e( $author ); ?>">
				<p><?php esc_html_e($author); ?></p>
			</td>

			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $start ); ?>">
				<p style="font-size: 10px; color: #999;"><?php esc_html_e($start); ?></p>
			</td>
			<?php endif; ?>

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $end ); ?>">
				<p style="font-size: 10px; color: #999;"><?php esc_html_e($end); ?></p></td>
			<?php endif; ?>

			<td data-value="<?php esc_attr_e( $progress ); ?>">
				<progress max="100" value="<?php esc_html_e($progress); ?>">
				</progress>
			</td>
		</tr>
		<?php
		}
	}

	?>

</table>