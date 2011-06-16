<form action="admin-post.php" method="POST">
	<table class="propel" width="100%">
		<tr>
			<td width="20%"><p>Name</p></td>
			<td><input type="text" name="title" style="width:100%"/></td>
		</tr>

		<tr>
			<td><p>Start Date</p></td>
			<td><input type="text" name="start_date" class="date" /></td>
		</tr>

		<tr>
			<td><p>End Date</p></td>
			<td><input type="text" name="end_date" class="date" /></td>
		</tr>

		<tr>
			<td><p>User</p></td>
			<td>
				<select name="user">
					<option value="0">Unassigned</option>
	                <?php
	                foreach($users as $user) {
	                    echo '<option value="' . $user->id . '">' . $user->user_nicename . '</option>';

	                }
	                ?>
	            </select>
	        </td>
	    </tr>

		<tr>
			<td><p>Priority</p></td>
			<td>
				<select name="priority">
					<option value="1">Low</option>
	                <?php
	                for ($i = 1; $i <= 10; $i ++) {
	                    echo '<option value="' . $i . '">' . $i . '</option>';

	                }
	                ?>
	                <option value="10">High</option>
	            </select>
	        </td>
	    </tr>

		<tr>
			<td><p><?php _e("Progress", "propel"); ?></p></td>
			<td>
	            <select name="complete">
	                <?php
	                for ($i = 0; $i <= 100; $i ++) {
	                    echo '<option value="' . $i . '">' . $i . '%</option>';

	                }
	                ?> 
	            </select>		
			</td>
		</tr>

		<tr>
			<td><p>Description</p></td>
			<td><textarea class="propel-description" style="width:100%" name="description"></textarea></td>
		</tr>

		<tr>
			<td><p>Tags (comma separated)</p></td>
			<td><input type="text" name="tags" /></td>
		</tr>
		
		<tr>
			<td><p>Send E-Mail</p></td>
			<td><input type="checkbox" name="email" />
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
				<input type="hidden" name="action" value="propel_create_task" />	
			</td>
		</tr>
		
		<tr>
			<td colspan="2"><input type="submit" value="Submit" class="button-primary" /></td>
		</tr>
	</table>
</form>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.date').datepicker({
		dateFormat : 'yy-mm-dd'
	});
});
</script>