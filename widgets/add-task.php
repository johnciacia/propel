<form method="post" id="quick-task">

<h4><?php _e('Title') ?></h4>
<div class="input-text-wrap">
	<input type="text" name="pm-title" />
</div>

<h4><?php _e('Project') ?></h4>
<div class="input-select-wrap">
	<select name="pm-project">
		<?php
			foreach ($projects as $project) {
				echo '<option value="' . $project->id . '">' . $project->title . '</option>';
			}
		?>
	</select>
</div>

<?php if($advance) { ?>
<h4><?php _e('User') ?></h4>
<div class="input-select-wrap">
	<select name="pm-user">
		<option value="0">Anyone</option>
		<?php
			foreach($users as $user) {
				if($user->id == $task->uid) {
					echo '<option value="' . $user->id . '" selected="selected">' . $user->user_nicename . '</option>';                	
				} else {
					echo '<option value="' . $user->id . '">' . $user->user_nicename . '</option>';
				}
			}
		?>
	</select>
</div>

<h4><?php _e('Start') ?></h4>
<div class="input-select-wrap">
	<select name="pm-start_month">
	<?php 
		foreach($months as $value => $m) {
			if($m == $month) {
				echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
			} else {
				echo '<option value="'.$value.'">' . $m . '</option>';
			}
		}
	
	?>
	</select>
	<span class="wp-trac-input-wrap">
		<input type="text" name="pm-start_day" class="day" style="border: none" value="<?php echo $day; ?>"/>
	</span>
	&nbsp;
	<span class="wp-trac-input-wrap">
		<input type="text" name="pm-start_year" class="year" style="border: none" value="<?php echo $year; ?>" />
	</span>
</div>			

<?php } ?>

<h4><?php _e('End') ?></h4>
<div class="input-select-wrap">
	<select name="pm-end_month">
		<?php 
			foreach($months as $value => $m) {
				if($m == $month) {
					echo '<option value="'.$value.'" selected="selected">' . $m . '</option>';
				} else {
					echo '<option value="'.$value.'">' . $m . '</option>';
				}
			}
		?>
	</select>
	<span class="wp-trac-input-wrap">
		<input type="text" name="pm-end_day" class="day" style="border: none" value="<?php echo $day; ?>"/>
	</span>
	&nbsp;
	<span class="wp-trac-input-wrap">
		<input type="text" name="pm-end_year" class="year" style="border: none" value="<?php echo $year; ?>" />
	</span>
</div>


<?php if($advance) { ?>
<h4><?php _e('Priority') ?></h4>
<div class="input-select-wrap">
	<select name="pm-priority">
		<option value="1">Low</option>
        <?php
            for ($i = 1; $i <= 10; $i ++) {
            	echo '<option value="' . $i . '">' . $i . '</option>';
            }
        ?>
    	<option value="10">High</option>
	</select>
</div>

<h4><?php _e('Complete') ?></h4>
<div class="input-select-wrap">
	<select name="pm-complete">
		<?php
			for ($i = 0; $i <= 100; $i ++) {
				echo '<option value="' . $i . '">' . $i . '%</option>';
			}
		?> 
	</select>
</div>
<?php } ?>

<h4><?php _e('Content') ?></h4>
<div class="textarea-wrap">
	<textarea id="description" name="pm-description" cols="15" rows="3"></textarea>
</div>

<p class="submit">
	<input type="button" name="addtask" id="pm-submit" class="button-primary" value="<?php esc_attr_e('Add'); ?>" />
	<br class="clear" />
</p>

</form>


<script>
	jQuery(document).ready(function(){
		jQuery("#pm-submit").click(function(){
			jQuery('#message').remove();
			
			var start_month;
			var start_day;
			var start_year;
			var user;
			var priority;
			var complete;

			(typeof(jQuery('[name=pm-user]').val()) == "undefined") 
				? user = "0" : user = jQuery('[name=pm-user]').val();
			(typeof(jQuery('[name=pm-priority]').val()) == "undefined")  
				? priority = "1" : priority = jQuery('[name=pm-priority]').val();
			(typeof(jQuery('[name=pm-complete]').val()) == "undefined") 
				? complete = "0" : complete = jQuery('[name=pm-complete]').val();

							
			if(typeof(jQuery('[name=pm-start_month]').val()) == "undefined") {
				start_month = jQuery('[name=pm-end_month]').val();
				start_day = jQuery('[name=pm-end_day]').val();
				start_year = jQuery('[name=pm-end_year]').val();	
			} else {
				start_month = jQuery('[name=pm-start_month]').val();
				start_day = jQuery('[name=pm-start_day]').val();
				start_year = jQuery('[name=pm-start_year]').val();	
			}
				
			
			if(jQuery('[name=pm-title]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A title must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=pm-project]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A project must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=start_month]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A start month must be specified.</p></div>' );
				return false;				
			}



			if(jQuery('[name=start_day]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A start day must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=start_year]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A start year must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=end_month]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>An end month must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=end_day]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>An end day must be specified.</p></div>' );
				return false;				
			}
				
			if(jQuery('[name=end_year]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>An end year must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=pm-user]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A user must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=pm-priority]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A priority must be specified.</p></div>' );
				return false;				
			}

			if(jQuery('[name=pm-complete]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A status must be specified.</p></div>' );
				return false;				
			}	

			if(jQuery('[name=pm-description]').val() == "") {
				jQuery('#quick-task').prepend( '<div id="message" class="error"><p>A description must be specified.</p></div>' );
				return false;				
			}		
				
			jQuery('#pm-quick-task h3').append( '<img src="images/wpspin_light.gif" style="margin: 0 6px 0 0; vertical-align: middle" />' );


			var data = {
					action: 'quick-task',
					project: jQuery('[name=pm-project]').val(),
					title: jQuery('[name=pm-title]').val(),
					user: user,
					description: jQuery('[name=pm-description]').val(),
					start_month: start_month,
					start_day: start_day,
					start_year: start_year,
					end_month: jQuery('[name=pm-end_month]').val(),
					end_day: jQuery('[name=pm-end_day]').val(),
					end_year: jQuery('[name=pm-end_year]').val(),
					priority: priority,
					complete: complete
				};

			jQuery.ajax({
				  type: 'POST',
				  url: ajaxurl,
				  data: data,
				  success: function(response) {
						jQuery('#pm-quick-task h3 img').remove();
						var r = jQuery.parseJSON(response);
						
						jQuery('#quick-task').prepend( '<div id="message" class="updated"><p>' + 
												r.title + ' was added to your task list.</p></div>' );
						
						jQuery('#pm-milestones').prepend( '<tr id="pm-task-' + r.id + '"><td><img src="' + r.image + '" /></td><td><p>' +
													r.title + '</p><div id="progress-' + r.id + '" class="progressbar"></div>' +
													'<div id="description-' + r.id + '">' +
													'<p>' + r.description + '</p>' +
													'</div></td>' + 
													'<td><div class="row-actions">' +
													'<div class="pm-row-action"><a onclick="pm_complete(' + 
														r.id + ',' + r.show_completed + ')">Complete</a></div>' +
													'<div class="pm-row-action"><a onclick="pm_delete(' 
															+ r.id + ')">Delete</a></div>' +
													'</div></td></tr>' ).fadeIn(500);
						
						jQuery('#pm-task-' + r.id + '').mouseover(function() {
							jQuery('#progress-' + r.id + '').show();
							jQuery('#description-' + r.id + '').show();
	            		});

						jQuery('#pm-task-' + r.id + '').mouseout(function() {
							jQuery('#progress-' + r.id + '').hide();
							jQuery('#description-' + r.id + '').hide();
	            		});
	            		
						jQuery('#description-' + r.id + '').hide();
						jQuery('#progress-' + r.id + '').hide();
						jQuery('#progress-' + r.id + '').progressbar({value: r.percent});
						
						
						jQuery('[name=pm-description]').val('');
						jQuery('[name=pm-title]').val('');
					},
				  dataType: "JSON"
				});
			
			
			return false;
		});
	});


</script>