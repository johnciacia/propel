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
				<option value="<?php echo $project->ID; ?>"><?php echo $project->post_title; ?></option>
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
		dateFormat : 'MMM dd yyyy'
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
			jQuery("#propel-my-tasks").dataTable().fnAddData(
				["", 
				"<div style='background-color: " + r.color + ";' class='propel-status'>" + r.status + "</div>", 
				"<p>" + r.post_title + "</p>", 
				"<p>" + r.pid + "</p>", 
				"<p>" + r.priority + "</p>", 
				"<p>" + r.complete + "</p>", 
				" ", 
				" ", 
				" "]);

		});
		
		return false;
	});
	


});
</script>