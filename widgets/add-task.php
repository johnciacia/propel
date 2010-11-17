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
	$(document).ready(function(){
		$("#pm-submit").click(function(){
			$('#message').remove();
			

			
			var start_month;
			var start_day;
			var start_year;
			var user;
			var priority;
			var complete;

			(typeof($('[name=pm-user]').val()) == "undefined") 
				? user = "0" : user = $('[name=pm-user]').val();
			(typeof($('[name=pm-priority]').val()) == "undefined")  
				? priority = "1" : priority = $('[name=pm-priority]').val();
			(typeof($('[name=pm-complete]').val()) == "undefined") 
				? complete = "0" : complete = $('[name=pm-complete]').val();

							
			if(typeof($('[name=pm-start_month]').val()) == "undefined") {
				start_month = $('[name=pm-end_month]').val();
				start_day = $('[name=pm-end_day]').val();
				start_year = $('[name=pm-end_year]').val();	
			} else {
				start_month = $('[name=pm-start_month]').val();
				start_day = $('[name=pm-start_day]').val();
				start_year = $('[name=pm-start_year]').val();	
			}
				
			
			if($('[name=pm-title]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A title must be specified.</p></div>' );
				return false;				
			}

			if($('[name=pm-project]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A project must be specified.</p></div>' );
				return false;				
			}

			if($('[name=start_month]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A start month must be specified.</p></div>' );
				return false;				
			}



			if($('[name=start_day]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A start day must be specified.</p></div>' );
				return false;				
			}

			if($('[name=start_year]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A start year must be specified.</p></div>' );
				return false;				
			}

			if($('[name=end_month]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>An end month must be specified.</p></div>' );
				return false;				
			}

			if($('[name=end_day]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>An end day must be specified.</p></div>' );
				return false;				
			}
				
			if($('[name=end_year]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>An end year must be specified.</p></div>' );
				return false;				
			}

			if($('[name=pm-user]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A user must be specified.</p></div>' );
				return false;				
			}

			if($('[name=pm-priority]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A priority must be specified.</p></div>' );
				return false;				
			}

			if($('[name=pm-complete]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A status must be specified.</p></div>' );
				return false;				
			}	

			if($('[name=pm-description]').val() == "") {
				$('#quick-task').prepend( '<div id="message" class="error"><p>A description must be specified.</p></div>' );
				return false;				
			}		
				
			$('#pm-quick-task h3').append( '<img src="images/wpspin_light.gif" style="margin: 0 6px 0 0; vertical-align: middle" />' );


			var data = {
					action: 'quick-task',
					project: $('[name=pm-project]').val(),
					title: $('[name=pm-title]').val(),
					user: user,
					description: $('[name=pm-description]').val(),
					start_month: start_month,
					start_day: start_day,
					start_year: start_year,
					end_month: $('[name=pm-end_month]').val(),
					end_day: $('[name=pm-end_day]').val(),
					end_year: $('[name=pm-end_year]').val(),
					priority: priority,
					complete: complete
				};

			$.ajax({
				  type: 'POST',
				  url: ajaxurl,
				  data: data,
				  success: function(response) {
						$('#pm-quick-task h3 img').remove();
						var r = $.parseJSON(response);
						
						$('#quick-task').prepend( '<div id="message" class="updated"><p>' + 
												r.title + ' was added to your task list.</p></div>' );
						
						$('#pm-milestones').prepend( '<tr id="pm-task-' + r.id + '"><td><img src="' + r.image + '" /></td><td><p>' +
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
						
	            		$('#pm-task-' + r.id + '').mouseover(function() {
	            			$('#progress-' + r.id + '').show();
	            			$('#description-' + r.id + '').show();
	            		});

	             		$('#pm-task-' + r.id + '').mouseout(function() {
	            			$('#progress-' + r.id + '').hide();
	            			$('#description-' + r.id + '').hide();
	            		});
	            		
	            		$('#description-' + r.id + '').hide();
	            		$('#progress-' + r.id + '').hide();
						$('#progress-' + r.id + '').progressbar({value: r.percent});
						
						
						$('[name=pm-description]').val('');
						$('[name=pm-title]').val('');
					},
				  dataType: "JSON"
				});
			
			
			return false;
		});
	});


</script>