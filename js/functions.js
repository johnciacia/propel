function pm_complete(id, fade){
	
	jQuery('#progress-' + id).progressbar("option", "value", 100);
	jQuery('#pl-status-' + id + ' div.av').html("Complete");
	jQuery('#pl-status-' + id + ' div.at').css('background-color', '#0000cc').css('border-color', '#0000cc');
	jQuery('#pl-status-' + id + ' div.au').css('border-color', '#0000cc');

	
	if(fade == 1)
		jQuery("#pm-task-" + id).fadeOut('fast');
	
	var data = {
			action: 'task-glance',
			action2: 'complete',
			id: id
		};
	
	jQuery.post(ajaxurl, data);
	return false;
}

function pm_delete(id){
	
	jQuery("#pm-task-" + id).fadeOut('fast');
	var data = {
			action: 'task-glance',
			action2: 'delete',
			id: id
		};
	
	jQuery.post(ajaxurl, data);
	return false;
}

function validateBugReport() {
	
	if(jQuery('[name=project]').val() == "") {
		alert("You must select a project.");
		return false;				
	}	

	if(jQuery('[name=title]').val() == "") {
		alert("You must enter a title.");
		return false;				
	}	
	
	if(jQuery('[name=description]').val() == "") {
		alert("You must enter a description.");
		return false;				
	}	
	
	return true;
}
