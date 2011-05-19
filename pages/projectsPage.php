<div id="picasso-general" class="wrap">
<?php screen_icon('options-general'); ?>
<h2>Propel
<a href="admin.php?page=propel-create-project" class="button add-new-h2">Add New</a></h2>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<table class="widefat">
<thead>
		<th>ID</th>
		<th>Name</th>
		<th>ASDF</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>ASDF</th>
	</tr>
</tfoot>
<tbody>
	<?php
		foreach($projects as $project) {
			echo "<tr>";
			echo "<td>{$project->id}</td>";
			echo "<td><a href='admin.php?page=propel-edit-project&id={$project->id}'>{$project->title}</a> | <a href='?action=propel-delete-project&id={$project->id}' onClick='return delete_project()'>Delete</a></td>";
			echo "<td>asdf</td>";
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
</div>
</div>

<script type="text/javascript">

  function delete_project() {
    return confirm('Deleting this project will also remove all tasks associated with it. Are you sure you want to continue?');
  }


</script>