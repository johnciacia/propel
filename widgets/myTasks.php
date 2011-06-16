<table width="100%" id="propel-my-tasks" class="gen-table">
	<thead>
		<tr>
			<th></th>
			<th class="sortable"><p><?php _e("Name", "propel"); ?></p></th>
			<?php if($_GET['page'] == "propel-edit-project") { ?>
				<th class="sortable"><p><?php _e("Owner", "propel"); ?></p></th>
			<?php } else { ?>
				<th class="sortable"><p><?php _e("Project", "propel"); ?></p></th>
			<?php } ?>
			<th class="sortable"><p><?php _e("Start Date", "propel") ?></p></th>
			<th class="sortable"><p><?php _e("End Date", "propel") ?></p></th>
			<th class="sortable"><p><?php _e("Priority", "propel"); ?></p></th>
			<th class="sortable"><p><?php _e("Progress", "propel"); ?></p></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	
	<?php
	foreach($tasks as $task) {
		//Get the post meta information
		$meta = get_post_meta($task->ID, "_propel_task_metadata", true);		
		//Get the owner of this task. If the task is unassigned display a dash
		$owner = ($meta['assigned_to'] == 0) ? "-" : $this->tasksModel->getUserById($meta['assigned_to'])->user_nicename;
		//Get the project name this task is associated with
		$project = $this->projectsModel->getProjectById($task->post_parent);
		//Get the project status
		list($z, $color) = propel_get_status($meta);

		//If there is no start date or end date display a dash
		($meta['start'] == "0000-00-00") ? $start = "-" : $start = $meta['start'];
		($meta['end'] == "0000-00-00") ? $end = "-" : $end = $meta['end'];
		$x = ($meta['complete'] == 100) ? "" : "un"; 
		
		echo "<tr id='propel-task-{$task->ID}' data-value='{$task->ID}'>";
		echo "<td><div style='background-color: $color' class='propel-status'>$z</div></td>";
		echo "<td><p>{$task->post_title}</p></td>";
		
		if($_GET['page'] == "propel-edit-project") {
			echo "<td><p>{$owner}</p></td>";	
		} else {
			echo "<td><p>{$project->post_title}</p></td>";
		}
		
		echo "<td><p>{$start}</p></td>";
		echo "<td><p>{$end}</p></td>";
		echo "<td><p>{$meta['priority']}</p></td>";
		echo "<td><p>{$meta['complete']}%</p></td>";
		echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task={$task->ID}' title='Delete'>Delete</a></td>";
		echo "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-task&id={$task->ID}' title='Edit'>Edit</a></td>";
		echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-task&task={$task->ID}' title='Mark as complete'>Complete</a></td>";	
		echo "</tr>";

	}
	?>
</table>
<div style="clear:both;"></div>
<script type="text/JavaScript">

jQuery(document).ready(function() {

	var nCloneTh = document.createElement( 'th' );
	var nCloneTd = document.createElement( 'td' );
	nCloneTd.innerHTML = '<img style="margin-left: 5px; margin-right:5px;" src="<?php echo WP_PLUGIN_URL ?>/propel/images/details_open.png" />';
	nCloneTd.className = "center";
	
	jQuery('#propel-my-tasks thead tr').each( function () {
		this.insertBefore( nCloneTh, this.childNodes[0] );
	} );
	
	jQuery('#propel-my-tasks tbody tr').each( function () {
		this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
	} );
	
	jQuery.fn.dataTableExt.oSort['percent-asc']  = function(a,b) {
			console.log("asc");
		var x = (a == "-") ? 0 : a.replace( /%/, "" );
		var y = (b == "-") ? 0 : b.replace( /%/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};

	jQuery.fn.dataTableExt.oSort['percent-desc'] = function(a,b) {
			console.log("desc");
		var x = (a == "-") ? 0 : a.replace( /%/, "" );
		var y = (b == "-") ? 0 : b.replace( /%/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
	
	/*
	jQuery.fn.dataTableExt.oStdClasses.sPaging = "tablenav-pages";
	jQuery.fn.dataTableExt.oStdClasses.sPageFirst = "first-page";
 	jQuery.fn.dataTableExt.oStdClasses.sPageLast = "last-page";
	jQuery.fn.dataTableExt.oStdClasses.sPageButton = "";
	jQuery.fn.dataTableExt.oStdClasses.sPageButtonActive = "current-page";
	jQuery.fn.dataTableExt.oStdClasses.sPageButtonStaticDisabled = "disabled";
	jQuery.fn.dataTableExt.oStdClasses.sPagePrevious = "prev-page";
	jQuery.fn.dataTableExt.oStdClasses.sPageNext = "next-page";
	
	
	jQuery.fn.dataTableExt.oStdClasses.sStripOdd = "alternate";
	*/
	
	var oTable = jQuery('#propel-my-tasks').dataTable( {
		"bStateSave": true,
		"sPaginationType": "full_numbers",
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [ 0 ] }
		],
		"aaSorting": [[1, 'asc']],
				"aoColumns" : [
			null, null, null, null, null, null, null, null, null, null, null
		]
	});
	

	
	jQuery('#propel-my-tasks tbody td img').live('click', function () {
		var nTr = this.parentNode.parentNode;
		if ( this.src.match('details_close') )
		{
			/* This row is already open - close it */
			this.src = "<?php echo WP_PLUGIN_URL ?>/propel/images/details_open.png";
			oTable.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			this.src = "<?php echo WP_PLUGIN_URL ?>/propel/images/details_close.png";
			oTable.fnOpen( nTr, '<tr><td><p style="margin-left: 50px;" id="'+jQuery("#" + nTr.id ).attr('data-value')+'">'+get_details(jQuery("#" + nTr.id ).attr('data-value'))+'</p></td></tr>', 'details' );
		}
	} );
} );

function get_details(id) {
		var data = {
			action: 'propel-get-task-details',
			id: id
		};

		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#" + id).html(response);
		});
		
}
</script>