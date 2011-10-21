<table width="100%" class="gen-table tablesorter" id="propel-tasks">
	<thead>
		<tr>
			<th class="sortable"><p>Name</p></th>
			<th class="sortable"><p>Priority</p></th>
			<th class="sortable"><p>Owner</p></th>
			<th class="sortable"><p>Start</p></th>
			<th class="sortable"><p>Due</p></th>
			<th class="sortable"><p>Progress</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php
	foreach($tasks as $task) {
		
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
			$author = $userdata->user_nicename;
		} else {
			$author = "Unassigned";
		}

		$x = ($progress == 100) ? "" : "un";
		$nonce = wp_create_nonce('propel-trash');
		echo "<tbody onClick='gen_expand(this)' id='{$task->ID}'><tr><td data-value='{$task->post_title}'><p>{$task->post_title}</p></td>";
		echo "<td data-value='$priority'><p>$priority</p></td>";
		echo "<td data-value='{$author}'><p>$author</p></td>";
		echo "<td data-value='{$start}'><p>$start</p></td>";
		echo "<td data-value='{$end}'><p>$end</p></td>";
		echo "<td data-value='{$progress}'><p>{$progress}%</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='post.php?action=delete&post={$task->ID}&_wpnonce=$nonce' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='post.php?post={$task->ID}&action=edit' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='post.php?action=complete&post={$task->ID}' title='Mark as complete'>Complete</a></td>";
		echo "</tr><tr class='gen-hidden' id='gen-row-{$task->ID}'><td>&nbsp</td><td colspan='8'><p>{$task->post_content}</p></td></tr></tbody>";
	}

	?>

</table>


<script type="text/JavaScript">

	function gen_expand(elem) {
		jQuery('#gen-row-' + elem.id).toggle();
	}
	
</script>