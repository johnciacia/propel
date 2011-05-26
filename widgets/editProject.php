<form action="admin-post.php" method="POST">
<table width="100%">
	<tr>
		<td width="20%"><p>Name: </p></td>
		<td><input style="width:100%" type="text" name="title" value="<?php echo $project->title; ?>" /></td>
	</tr>
	
	<tr>
		<td><p>Overview: </p></td>
		<td><textarea style="width:100%" name="description"><?php echo $project->description; ?></textarea></td>
	</tr>
	
	<tr>
		<td colspan="2"><input class="button-primary" type="submit" value="Submit"></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
<input type="hidden" name="action" value="propel_update_project" />
</form>