<div id="propel-general" class="wrap">

	<?php screen_icon('options-general'); ?>

	<h2>Propel <a href="admin.php?page=propel-create-project" class="button add-new-h2" id="create-project">Add New</a></h2>
	
	
	<!-- BEGINNING OF POSTSTUFF -->	
	<div id="poststuff" class="metabox-holder has-right-sidebar">
	
		<table id="propel-my-projects" class="widefat">
			<thead>
				<tr>
					<th style="width:50px;"></th>
					<th>Project</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Priority</th>
					<th>%</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			
			<?php
			foreach($projects as $project) {
			
				$meta = get_post_meta($project->ID, "_propel_project_metadata", true);
				list($z, $color) = propel_get_status($meta);				
				$x = ($meta['complete'] == 100) ? "" : "un"; 
		
				echo "<tr id='propel-project-{$project->ID}' data-value='{$project->ID}'>";
					echo "<td><div style='background-color: $color' class='propel-status'>$z</div></td>";
					echo "<td><a href='admin.php?page=propel-edit-project&id={$project->ID}'>{$project->post_title}</a></td>";
					echo "<td><p>{$meta['start']}</p></td>";
					echo "<td><p>{$meta['end']}</p></td>";
					echo "<td><p>{$meta['priority']}</p></td>";
					echo "<td><p>{$meta['complete']}</p></td>";
					echo "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-project&project={$project->ID}' title='Delete'>Delete</a></td>";
					echo "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-project&id={$project->ID}' title='Edit'>Edit</a></td>";
					echo "<td class='gen-icon gen-{$x}checked-icon'><a href='?action=propel-complete-project&project={$project->ID}' title='Mark as complete'>Complete</a></td>";	
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
			
			jQuery('#propel-my-projects thead tr').each( function () {
				this.insertBefore( nCloneTh, this.childNodes[0] );
			} );
			
			jQuery('#propel-my-projects tbody tr').each( function () {
				this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
			} );
			
		
			var oTable = jQuery('#propel-my-projects').dataTable( {
				"bStateSave": true,
				"sPaginationType": "full_numbers",
				"aoColumnDefs": [
					{ "bSortable": false, "aTargets": [ 0 ] }
				],
				"aaSorting": [[1, 'asc']]
			});
			
			
			jQuery('#propel-my-projects tbody td img').live('click', function () {
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
					action: 'propel-get-project-details',
					id: id
				};
		
				jQuery.post(ajaxurl, data, function(response) {
					jQuery("#" + id).html(response);
				});
				
		}
		</script>
		
		<!---
		<h2>Archived Projects</h2>
		<table class="widefat fixed" cellspacing="0">
		    <thead>
		    <tr>
		        <tr>
		            <th id="cb" class="manage-column column-cb check-column" scope="col"></th>
		            <th id="columnname" class="manage-column column-columnname" scope="col">ID</th>
		            <th id="columnname" class="manage-column column-columnname num" scope="col">Name</th>
		        </tr>
		    </tr>
		    </thead>
		
		    <tfoot>
		    <tr>
		        <tr>
		            <th class="manage-column column-cb check-column" scope="col"></th>
		            <th class="manage-column column-columnname" scope="col"></th>
		            <th class="manage-column column-columnname num" scope="col"></th>
		        </tr>
		    </tr>
		    </tfoot>
		
		    <tbody>
		        <tr class="alternate" valign="top">
		            <th class="check-column" scope="row"></th>
		            <td class="column-columnname">a
		                <div class="row-actions">
		                    <span><a href="#">Action</a> |</span>
		                    <span><a href="#">Action</a></span>
		                </div>
		            </td>
		            <td class="column-columnname">b</td>
		        </tr>
		        <tr valign="top">
		            <th class="check-column" scope="row"></th>
		            <td class="column-columnname">
		                <div class="row-actions">
		                    <span><a href="#">Action</a> |</span>
		                    <span><a href="#">Action</a></span>
		                </div>
		            </td>
		            <td class="column-columnname"></td>
		        </tr>
		    </tbody>
		</table>
		-->
	
	 	<!-- BEGINNING OF DIALOG-FORM -->
		<div id="dialog-form" style="visibility: hidden;" title="Create new project"> 
			<p class="validateTips">All form fields are required.</p> 
		 
			<form action="admin-post.php" id="propel-create-project" method="POST">
			<table width="100%">
				<tr>
					<td width="10%"><p>Title:</p></td>
					<td><input type="text" name="title" style="width:100%" />
				</tr>
		
				<tr>
					<td><p>Description:</p></td>
					<td><textarea style="width:100%" name="description"></textarea>
				</tr>

				<tr>
					<td><p>Start Date</p></td>
					<td><input type="text" name="start_date" class="date" value="" /></td>
				</tr>
		
				<tr>
					<td><p>End Date</p></td>
					<td><input type="text" name="end_date" class="date" value="" /></td>
				</tr>

				<tr>
					<td><p>Priority</p></td>
					<td>
						<select name="priority">
							<option value="1">Low</option>
			                <?php
			                for ($i = 1; $i <= 10; $i ++) {
								if($i == $meta['priority']) {
			                    	echo '<option value="' . $i . '" selected>' . $i . '</option>';
								} else {
								    echo '<option value="' . $i . '">' . $i . '</option>';
			                	}
							}
			                ?>
			                <option value="10">High</option>
			            </select>
			        </td>
			    </tr>
		
				<tr>
					<td><p>Completed</p></td>
					<td>
			            <select name="complete">
			                <?php
			                for ($i = 0; $i <= 100; $i++) {
								if($i == $meta['complete'])
									echo '<option value="' . $i . '" selected>' . $i . '%</option>';
				                else 
			                    	echo '<option value="' . $i . '">' . $i . '%</option>';
			                }
			                ?> 
			            </select>		
					</td>
				</tr>
		
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<input type="hidden" name="action" value="propel-create-project" />
					</td>
				</tr>
		
			</table>
			</form> 
		
		</div><!-- END OF DIALOG-FORM -->
	
	</div><!-- END OF POSTSTUFF -->
	
</div><!-- END OF PROPEL-GENERAL -->




 



<script src="http://jqueryui.com/ui/jquery.ui.mouse.js"></script> 
<script src="http://jqueryui.com/ui/jquery.ui.button.js"></script> 
<script src="http://jqueryui.com/ui/jquery.ui.draggable.js"></script> 
<script src="http://jqueryui.com/ui/jquery.ui.position.js"></script> 
<script src="http://jqueryui.com/ui/jquery.ui.resizable.js"></script> 
<script src="http://jqueryui.com/ui/jquery.ui.dialog.js"></script> 
<script src="http://jqueryui.com/ui/jquery.effects.core.js"></script>

<script type="text/javascript">
jQuery(function() {
	

	
	jQuery( "#dialog-form" ).dialog({
		autoOpen: false,
		height: 250,
		width: 400,
		modal: true,
		buttons: {
			"Create": function() {
				var bValid = true;
					jQuery( this ).dialog( "close" );
					jQuery("#propel-create-project").submit();
			},
			Cancel: function() {
				jQuery( this ).dialog( "close" );
			}
		}
	});

	jQuery( "#create-project" ).click(function() {
			jQuery( "#dialog-form" ).dialog( "open" );
			jQuery("#dialog-form").css("visibility", "visible");
			return false;
		});
});


function delete_project() {
  return confirm('Deleting this project will also remove all tasks associated with it. Are you sure you want to continue?');
}

jQuery(document).ready(function(){
	jQuery('.date').datepicker({
		dateFormat : 'yy-mm-dd'
	});
});


</script>