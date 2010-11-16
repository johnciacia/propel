<div class="pl-feedback">

	<form>
		<select name="project" class="pl-feedback-projects">
			<?php foreach($projects as $project) { ?>
			<option value="<?php echo $project->id; ?>"><?php echo $project->title; ?></option>
			<?php } ?>
		</select><br />
		
		<textarea name="description" class="pl-feedback-description"></textarea>
		<br />
		
		<input type="submit" class="pl-feedback-submit" />
	</form>
	
</div>