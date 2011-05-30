<table width="100%" id="propel-my-tasks" class="gen-table">
	<thead>
		<tr>
			<th class="sortable" width="80"></th>
			<th class="sortable"><p>Name</p></th>
			<th class="sortable"><p>Project</p></th>
			<th class="sortable"><p>Priority</p></th>
			<th class="sortable"><p>%</p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php


	foreach($tasks as $task) {
		$meta = get_post_meta($task->ID, "_propel_task_metadata", true);
		$project = $this->projectsModel->getProjectById($task->post_parent);
		if($meta['complete'] == 100) {
			$z = "Complete";
			$color = "#0000cc";
		} 
		
		else {
			if(date("Y-m-d") == $meta['end']) {
				$z = "Today";
				$color = "#ffa500";
			} else if(date("Y-m-d") > $meta['end']) {
				$z = "Overdue";
				$color = "#ff0000";
			} else {
				$z = "Later";
				$color = "#008000";
			}
		}
		
		$x = ($meta['complete'] == 100) ? "" : "un"; 
		//echo "<tbody>";
		echo "<tr id='propel-task-{$task->ID}' data-value='{$task->ID}'>";
		echo "<td><div style='background-color: $color' class='propel-status'>$z</div></td>";
		echo "<td><p>{$task->post_title}</p></td>";
		echo "<td><p>{$project->post_title}</p></td>";
		echo "<td><p>{$meta['priority']}</p></td>";
		echo "<td><p>{$meta['complete']}</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->ID}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-task&id={$task->ID}' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->ID}' title='Mark as complete'>Complete</a></td>";	
		echo "</tr>";
		//echo "<tr><td></td><td colspan='3'><p>{$task->post_content}</p></td></tr>";
		//echo "</tbody>";	
		
	}
	?>
</table>
<div style="clear:both;"></div>
<script type="text/JavaScript">

jQuery(document).ready(function() {
	/*
	 * Insert a 'details' column to the table
	 */
	var nCloneTh = document.createElement( 'th' );
	var nCloneTd = document.createElement( 'td' );
	nCloneTd.innerHTML = '<img style="margin-left: 5px; margin-right:5px;" src="http://www.datatables.net/examples/examples_support/details_open.png" />';
	nCloneTd.className = "center";
	
	jQuery('#propel-my-tasks thead tr').each( function () {
		this.insertBefore( nCloneTh, this.childNodes[0] );
	} );
	
	jQuery('#propel-my-tasks tbody tr').each( function () {
		this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
	} );
	
	/*
	 * Initialse DataTables, with no sorting on the 'details' column
	 */
	var oTable = jQuery('#propel-my-tasks').dataTable( {
		"bStateSave": true,
		"sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [ 0 ] }
		],
		"aaSorting": [[1, 'asc']]
	});
	
	/* Add event listener for opening and closing details
	 * Note that the indicator for showing which row is open is not controlled by DataTables,
	 * rather it is done here
	 */
	jQuery('#propel-my-tasks tbody td img').live('click', function () {
		var nTr = this.parentNode.parentNode;
		if ( this.src.match('details_close') )
		{
			/* This row is already open - close it */
			this.src = "http://www.datatables.net/examples/examples_support/details_open.png";
			oTable.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			this.src = "http://www.datatables.net/examples/examples_support/details_close.png";
			oTable.fnOpen( nTr, '<tr><td><p style="margin-left: 50px;" id="'+jQuery("#" + nTr.id ).attr('data-value')+'">'+get_details(jQuery("#" + nTr.id ).attr('data-value'))+'</p></td></tr>', 'details' );
		}
	} );
} );

function get_details(id) {
		var data = {
			action: 'propel-get-task-details',
			id: id
		};

		var r;
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#" + id).html(response);
		});
		
}
</script>