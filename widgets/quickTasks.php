<form id="propel-quick-tasks">
<table width="100%">
	<tr>
		<td><p>Title: </p></td>
		<td><input type="text" id="title" name="title" style="width:100%;" />
	</tr>
	
	<tr>
		<td><p>Project: </p></td>
		<td>
			<select id="pid" name="pid">
			<?php foreach($projects as $project) : ?>
				<option value="<?php echo $project->id; ?>"><?php echo $project->title; ?></option>
			<?php endforeach; ?>		
			</select>
		</td>
	</tr>
	
	<tr>
		<td><p>End: </p></td>
		<td><input class="date" type="text" id="end" name="end" style="width:100%;" /></td>
	</tr>
	
	<tr>
		<td><p>Description: </p></td>
		<td><textarea id="description" name="description" style="width:100%;"></textarea>
	</tr>
	
	<tr>
		<td colspan="2">
			<input type="submit" value="Submit" class="button-primary" />
		</td>
	</tr>

</table>
</form>

<script type="text/javascript">
jQuery(document).ready(function($){
	
	jQuery('.date').datepicker({
		dateFormat : 'yy-mm-dd'
	});
	
	$('#propel-quick-tasks').submit(function() {
		var data = {
			action: 'propel-quick-tasks',
			title: jQuery("#title").val(),
			id: jQuery("#pid").val(),
			end_date: jQuery("#end").val(),
			description: jQuery("#description").val()
		};

		jQuery.post(ajaxurl, data, function(response) {
			r = jQuery.parseJSON(response);
			jQuery("#propel-my-tasks").prepend(
				"<tbody onClick='gen_expand(this)' id='"+r.id+"'>"
				+ "<tr>"
				+ "<td><div style='background-color: " + r.color + ";' class='propel-status'>" + r.status + "</div></td>"
				+ "<td><p>" + r.title + "</p></td>" 
				+ "<td><p>" + r.pid + "</p></td> "
				+ "<td><p>" + r.priority + "</p></td>"
				+ "<td><p>" + r.complete + "</p></td>"
				+ "<td class='gen-icon gen-delete-icon'><a href='?action=propel-delete-task&task="+r.id+"' title='Delete'>Delete</a></td>"
				+ "<td class='gen-icon gen-edit-icon'><a href='?page=propel-edit-task&id="+r.id+"' title='Edit'>Edit</a></td>"
				+ "<td class='gen-icon gen-unchecked-icon'><a href='?action=propel-complete-task&task="+r.id+"' title='Mark as complete'>Complete</a></td>"
				+ "</tr>"
				+ "<tr class='gen-hidden' id='gen-row-"+r.id+"'><td>&nbsp;</td><td colspan='7'><p>" + r.description + "</p></td></tr>"
				+ "</tbody>");
			

			console.log(r)
		});
		
		return false;
	});
	


});
</script>