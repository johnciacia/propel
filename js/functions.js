jQuery(document).ready(function($) {
	
	jQuery.fn.dataTableExt.oSort['percent-asc']  = function(a,b) {
		var x = (a == "-") ? 0 : a.replace( /%/, "" );
		var y = (b == "-") ? 0 : b.replace( /%/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};

	jQuery.fn.dataTableExt.oSort['percent-desc'] = function(a,b) {
		var x = (a == "-") ? 0 : a.replace( /%/, "" );
		var y = (b == "-") ? 0 : b.replace( /%/, "" );
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
	
	cols = jQuery(".tasks-table:first th").size()
	var asdf = []
	for(i = 0; i < cols; i++) {
		asdf.push(null)
	}

	var oTable = jQuery('.tasks-table').dataTable( 
		{
		"bStateSave": true,
		//"sPaginationType": "full_numbers",
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"aoColumnDefs": [
			{ "bSortable": false, "aTargets": [ 0 ] }
		],
		"aaSorting": [[1, 'asc']],
				"aoColumns" : asdf
	});

} );

function get_details(id) {
	var data = {
		action: 'get_task_description',
		id: id
	};

	jQuery.post(ajaxurl, data, function(response) {
		if(response == "")
			jQuery('#detail-' + id).html("&nbsp;");
		else
			jQuery('#detail-' + id).html(response);
	});
}