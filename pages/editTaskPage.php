<?php $referer = $_SERVER['HTTP_REFERER']; ?>
<?php $meta = get_post_meta($task->ID, "_propel_task_metadata", true); ?>
<?php $tags = wp_get_post_tags($task->ID); 
foreach($tags as $tag) {
  $a[] = $tag->name;
}
$tags = @implode(",", $a); ?>
<div id="picasso-general" class="wrap">
<?php screen_icon('options-general'); ?>
<h2>Propel</h2>
<div id="poststuff" class="metabox-holder has-right-sidebar">

<form action="admin-post.php" method="POST">
	<table class="propel" width="100%">
		<tr>
			<td width="20%"><p>Name</p></td>
			<td><input type="text" name="title" style="width:100%" value="<?php echo $task->post_title; ?>"/></td>
		</tr>

		<tr>
			<td><p>Start Date</p></td>
			<td><input type="text" name="start_date" class="date" value="<?php echo $meta['start']; ?>" /></td>
		</tr>

		<tr>
			<td><p>End Date</p></td>
			<td><input type="text" name="end_date" class="date" value="<?php echo $meta['end']; ?>" /></td>
		</tr>

		<tr>
			<td><p>User</p></td>
			<td>
				<select name="user">
					<option value="0">Unassigned</option>
	                <?php
	                foreach($users as $user) {
						if($user->id == $meta['assigned_to'])
	                    	echo '<option value="' . $user->id . '" selected>' . $user->user_nicename . '</option>';
						else
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
						if($i == $meta['priority']) {
	                    	echo '<option value="' . $i . '" selected>' . $i . '</option>';
						} else {
						    echo '<option value="' . $i . '">' . $i . '</option>';
	                	}
					}
	                ?>
	                <option value="10">High</option>
	            </select>
	        </td>
	    </tr>

		<tr>
			<td><p>Completed</p></td>
			<td>
	            <select name="complete">
	                <?php
	                for ($i = 0; $i <= 100; $i++) {
						if($i == $meta['complete'])
							echo '<option value="' . $i . '" selected>' . $i . '%</option>';
		                else 
	                    	echo '<option value="' . $i . '">' . $i . '%</option>';
	                }
	                ?> 
	            </select>		
			</td>
		</tr>

		<tr>
			<td><p>Description</p></td>
			<td><textarea class="propel-description" style="width:100%" name="description"><?php echo $task->post_content; ?></textarea></td>
		</tr>

		<tr>
			<td><p>Tags (comma separated)</p></td>
			<td><input type="text" name="tags" value="<?php echo $tags; ?>" /></td>
		</tr>

		<tr>
			<td><p>Send E-Mail</p></td>
			<td><input type="checkbox" name="email" /></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
				<input type="hidden" name="action" value="propel-update-task" />	
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<input type="hidden" name="redirect" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
				<input type="submit" value="Submit" class="button-primary" /></td>
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

</div>
</div>
