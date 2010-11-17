function pm_complete(id, fade){
	
	$('#progress-' + id).progressbar("option", "value", 100);
	
	if(fade == 1)
		$("#pm-task-" + id).fadeOut('fast');
	
	var data = {
			action: 'task-glance',
			action2: 'complete',
			id: id
		};
	
	jQuery.post(ajaxurl, data);
	return false;
}

function pm_delete(id){
	
	$("#pm-task-" + id).fadeOut('fast');
	
	var data = {
			action: 'task-glance',
			action2: 'delete',
			id: id
		};
	
	jQuery.post(ajaxurl, data);
	return false;
}

function validateBugReport() {
	
	if($('[name=project]').val() == "") {
		alert("You must select a project.");
		return false;				
	}	

	if($('[name=title]').val() == "") {
		alert("You must enter a title.");
		return false;				
	}	
	
	if($('[name=description]').val() == "") {
		alert("You must enter a description.");
		return false;				
	}	
	
	return true;
}
