<?php
/* this will be an ajax search for the Owners / Authors
 */
?>
<script type="text/javascript">
function _list_owners(){
	var http_req = new XMLHttpRequest();
	var module = "<?php echo plugins_url(); ?>/propel2/metaboxes/owner_ajax.php";
	var user = document.getElementById("propel_post_author2").value; 
	var vars = "user="+ user ; 
	http_req.open("POST", module, true);
	http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_req.onreadystatechange = function(){
		if (http_req.readyState == 4 && http_req.status == 200){
			var return_info = http_req.responseText;
			document.getElementById("owner_result").innerHTML = return_info;
		}
	}
	http_req.send(vars); 	
	document.getElementById("owner_result").innerHTML = "..searching";
}

var $state = jQuery.noConflict();
$state(document).ready(function(){
		$state("#propel_post_author").live('change',function(){
			document.getElementById('propel_post_author2').value = $state("#propel_post_author option:selected").text();
		});
});
</script>

<table width="100%">
	<?php
		$projects = get_posts( array( 'post_type' => 'propel_project', 'numberposts' => -1 ) );
		if( count( $projects ) > 0 ) :
	?>
	<tr>
		<td>Project</td>
		<td>
			<?php
			echo "<select name='parent_id' id='parent_id'>";
			foreach($projects as $project) :
				if($project->ID == $parent) {
					echo '<option value=' . $project->ID . ' selected>';
					echo $project->post_title;
					echo '</option>';
				} else {
					echo '<option value=' . $project->ID . '>';
					echo $project->post_title;
					echo '</option>';				
				}
			endforeach;
			?>
		</td>
	</tr>
	<?php endif; ?>
	<?php if( Propel_Options::option('show_start_date' ) ) : ?>
	<tr>
		<td><p>Start Date</p></td>
		<td><input type="text" name="start_date" class="date" value="<?php echo $start; ?>" /></td>
	</tr>
	<?php endif; ?>

	<?php if( Propel_Options::option('show_end_date' ) ) : ?>
	<tr>
		<td><p>End Date</p></td>
		<td><input type="text" name="end_date" class="date" value="<?php echo $end; ?>" /></td>
	</tr>
	<?php endif; ?>
	
	<tr>
		<td><p>Priority</p></td>
		<td>
			<select name="priority">
			<?php 
			$priorities = propel_get_priorities();
			for($i = 0; $i < count($priorities); $i++) :
				echo "<option value='$i'".selected($priority, $i).">$priorities[$i]</option>";
			endfor;
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><p>Type</p></td>
		<td>
			<select name="tax_input[propel_type]">
			<?php 
			$terms = get_terms( 'propel_type', array( 'hide_empty' => 0 ) );
			foreach( $terms as $term) :
				echo "<option value='$term->name'".selected($type, $term->term_id).">$term->name</option>";
			endforeach;
			?>
			</select>
		</td>
	</tr>

	<tr>
		<td><p>Progress</p></td>
		<td>
			<select name="complete">
				<?php
				for ($i = 0; $i <= 100; $i = $i+5) :
					echo "<option value='$i'".selected($complete, $i).">$i</option>";
				endfor;
				?> 
			</select>		
		</td>
	</tr>

	<tr>
		<td><p>Manager</p></td>
		<td style="position:relative">
		<?php $user_info = get_userdata($post->post_author); ?>
			<input type="text" name="propel_post_author2" id="propel_post_author2" value="<?php echo $user_info->user_login;  ?>" >
			<input type="button" value="search" style="position:absolute; left:223px;" onClick="_list_owners();">
		</td>
	</tr>
		<tr id="owner_result">

	</tr>
	
	
</table>