<div id="propel-general" class="wrap">
<?php screen_icon('options-general'); ?>
<h2>Propel
<a href="admin.php?page=propel-create-project" class="button add-new-h2" id="create-project">Add New</a></h2>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<table class="widefat">
<thead>
		<th width="5%">ID</th>
		<th width="85%">Name</th>
		<th></th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th></th>
	</tr>
</tfoot>
<tbody>
	<?php
		foreach($projects as $project) {
			echo "<tr>";
			echo "<td>{$project->ID}</td>";
			echo "<td><a href='admin.php?page=propel-edit-project&id={$project->ID}'>{$project->post_title}</a></td>";
			echo "<td><a href='?action=propel-delete-project&id={$project->ID}' onClick='return delete_project()'>Delete</a> | <a href='admin.php?page=propel-edit-project&id={$project->ID}'>Edit</a></td>";
			echo "</tr>";
		
		}
	?>
</tbody>
</table>

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

 
<div id="dialog-form" style="visibility:hidden;" title="Create new project"> 
	<p class="validateTips">All form fields are required.</p> 
 
	<form action="admin-post.php" id="propel-create-project" method="POST">
	<table width="100%">
		<tr">
			<td width="10%"><p>Title:</p></td>
			<td><input type="text" name="title" style="width:100%" />
		</tr>

		<tr>
			<td><p>Description:</p></td>
			<td><textarea style="width:100%" name="description"></textarea>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
				<input type="hidden" name="action" value="propel-create-project" />
			</td>
		</tr>

	</table>
	</form> 

</div>

</div>
</div>




 



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


</script>