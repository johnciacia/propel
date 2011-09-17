/* FIXME - Hard coded paths */
jQuery(document).ready(function() {

	var nCloneTh = document.createElement( 'th' );
	var nCloneTd = document.createElement( 'td' );
	nCloneTd.innerHTML = '<img style="margin-left: 5px; margin-right:5px;" src="../wp-content/plugins/propel/images/details_open.png" />';
	nCloneTd.className = "center";
	
	jQuery('.tasks-table thead tr').each( function () {
		this.insertBefore( nCloneTh, this.childNodes[0] );
	} );
	
	jQuery('.tasks-table tbody tr').each( function () {
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
	
	var oTable = jQuery('.tasks-table').dataTable( {
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
	

	
	jQuery('.tasks-table tbody td img').live('click', function () {
		var nTr = this.parentNode.parentNode;
		if ( this.src.match('details_close') )
		{
			/* This row is already open - close it */
			this.src = "../wp-content/plugins/propel/images/details_open.png";
			oTable.fnClose( nTr );
		}
		else
		{
			/* Open this row */
			this.src = "../wp-content/plugins/propel/images/details_close.png";
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