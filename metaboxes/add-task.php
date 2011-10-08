<form>
<label for="task_title">Title: </label>
<input type="text" name="task_title" class="widefat" /><br /><br />

<label for="task_description">Description: </label>
<textarea name="task_description" class="widefat"></textarea><br /><br />

<label for="start_date">Start Date: </label>
<input type="text" name="start_date" class="widefat" /><br /><br />

<label for="end_date">End Date: </label>
<input type="text" name="end_date" class="widefat" /><br /><br />

<label for="priority">Priority: </label>
<select name="priority">
<?php for($i = 0; $i <= 100; $i = $i+5) : ?>
<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
<?php endfor; ?>
</select><br /><br />

<label for="progress">Progress: </label>
<select name="progress">
<?php for($i = 0; $i <= 10; $i++) : ?>
<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
<?php endfor; ?>
</select><br /><br />

<input type="submit" class="button-primary" value="Add Task" />
</form>