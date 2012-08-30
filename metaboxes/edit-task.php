<?php
/* this will be an ajax search for the Owners / Authors
 */
?>
<script type="text/javascript">
function _list_owners(){
	var http_req = new XMLHttpRequest();
	var module = "<?php echo plugins_url(); ?>/propel/metaboxes/owner_ajax.php";
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
		$state("#srbtn").hover(function(){
			$state("#ttip").toggle();	
		});
});
</script>
<style>
#ttip{
background: #f0f9ff; /* Old browsers */
background: -moz-linear-gradient(top,  #f0f9ff 0%, #cbebff 47%, #a1dbff 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f0f9ff), color-stop(47%,#cbebff), color-stop(100%,#a1dbff)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* IE10+ */
background: linear-gradient(to bottom,  #f0f9ff 0%,#cbebff 47%,#a1dbff 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f9ff', endColorstr='#a1dbff',GradientType=0 ); /* IE6-9 */
position:absolute;left:-122px; top:240px; width:200px; height:150px;border:1px solid #333;display:none; z-index:999;
}
</style>
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
			<input type="text" name="propel_post_author2" id="propel_post_author2" value="<?php echo $user_info->user_login;  ?>" ><div style="clear:both">
			<input id="srbtn" type="button" value="search" class="button-primary" onClick="_list_owners();">
			<div id="ttip">
			  <h3>Search Tips</h3>
			  <div style="margin:12px">
			  	1.) Type in combination of letters that can be found in the Users Login; <br/>
				2.) Leave it to blanks to search for all available Users; <br/>
			  </p>
			
			</div>
		</td>
	</tr>
		<tr id="owner_result">

	</tr>
	
	
</table>