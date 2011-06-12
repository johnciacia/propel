<div id="picasso-general" class="wrap">
<?php screen_icon('options-general'); ?>
<h2>Propel</h2>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<form action="admin-post.php" method="POST">

<table width="50%">
	<tr">
		<td width="10%">Title:</td>
		<td><input type="text" name="title" style="width:100%" />
	</tr>

	<tr>
		<td>Description:</td>
		<td><textarea style="width:100%" name="description"></textarea>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
		<td colspan="2">
			<input type="hidden" name="action" value="propel-create-project" />
			<input type="submit" value="Submit" class="button-primary" />
		</td>
	</tr>

</table>


</form>

</div>
</div>