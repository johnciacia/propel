<table width="100%" class="gen-table tasks-table" id="propel-tasks">
	<thead>
		<tr>
			<th class="sortable"><p>Name</p></th>
			<th class="sortable"><p>Priority</p></th>
			<th class="sortable"><p>Owner</p></th>
			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<th class="sortable"><p>Start</p></th>
			<?php endif; ?>
			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<th class="sortable"><p>Due</p></th>
			<?php endif; ?>
			<th class="sortable"><p>Progress</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php
	foreach($posts as $post) {
		$task = get_post($post->post_id);
		$progress = get_post_meta( $task->ID, '_propel_complete', true );
		$priority = get_post_meta( $task->ID, '_propel_priority', true );
		$start = get_post_meta( $task->ID, '_propel_start_date', true );
		if( $start )
			$start = date( get_option( 'date_format' ), $start );

		$end = get_post_meta( $task->ID, '_propel_end_date', true );
		if( $end )
			$end = date( get_option( 'date_format' ), $end);

		if( $task->post_author ) {
			$userdata = get_userdata( $task->post_author );
			$author = $userdata->display_name;
		} else {
			$author = "Unassigned";
		}

		$x = ($progress == 100) ? "" : "un";
		$nonce = wp_create_nonce('propel-trash');
		?>
		<tr class="toggle" id="<?php esc_attr_e( $task->ID ); ?>">
			<td data-value="<?php esc_attr_e($task->post_title); ?>">
				<p><?php esc_html_e($task->post_title); ?></p></td>

			<td data-value="<?php esc_attr_e( $priority ); ?>">
				<p><?php 
					$priorities = propel_get_priorities();
					esc_html_e($priorities[$priority]); 
					?></p></td>

			<td data-value="<?php esc_attr_e( $author ); ?>">
				<p><?php esc_html_e($author); ?></p></td>

			<?php if( Propel_Options::option('show_start_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $start ); ?>">
				<p><?php esc_html_e($start); ?></p></td>
			<?php endif; ?>

			<?php if( Propel_Options::option('show_end_date' ) ) : ?>
			<td data-value="<?php esc_attr_e( $end ); ?>">
				<p><?php esc_html_e($end); ?></p></td>
			<?php endif; ?>

			<td data-value="<?php esc_attr_e( $progress ); ?>">
				<p><?php esc_html_e($progress); ?>%</p></td>

			<td class="gen-icon gen-delete-icon">
				<a href="post.php?action=propel-delete&post=<?php esc_attr_e( $task->ID ); ?>&_wpnonce=<?php echo $nonce; ?>" title="Delete">Delete</a></td>

			<td class="gen-icon gen-edit-icon">
				<a href="post.php?post=<?php esc_attr_e( $task->ID ); ?>&action=edit" title="Edit">Edit</a></td>

			<td class="gen-icon gen-<?php echo $x; ?>checked-icon">
				<a href="post.php?action=complete&post=<?php esc_attr_e( $task->ID ); ?>" title="Mark as complete">Complete</a></td>
		</tr>
		<?php
	}

	?>

</table>
<div style="clear:both;"></div>


<script type="text/JavaScript">

	function gen_expand(elem) {
		jQuery('#gen-row-' + elem.id).toggle();
	}
	
</script>