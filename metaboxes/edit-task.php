<?php
/* this will be an ajax search for the Owners / Authors
 */
?>
<script type="text/javascript">
function _list_owners(){
	var http_req = new XMLHttpRequest();
	var module = "<?php echo plugins_url(); ?>/propel/metaboxes/owner_ajax.php";
	var user = document.getElementById("propel_post_author_display").value; 
	var vars = "user="+ user ; 
	http_req.open("POST", module, true);
	http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_req.onreadystatechange = function(){
		if (http_req.readyState == 4 && http_req.status == 200){
			var return_info = http_req.responseText;
			document.getElementById("owner_result").innerHTML = return_info;
			$state("#owner_result").show();
		}
	}
	http_req.send(vars); 	
	document.getElementById("owner_result").innerHTML = "..searching";
}

if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, ''); 
  }
}

var $state = jQuery.noConflict();
$state(document).ready(function(){
		$state("#propel_post_author_display").hover(function(){
            $state("#ttip").toggle();
		});
		$state(".search_itm").live('hover',function(){
			$state(".search_itm").css({background:"none"});
			$state(this).css({background:"#CCC"});
		});
		$state(".search_itm").live('click',function(){
		   document.getElementById("propel_post_author_display").value = $state(this).text().trim();	
		   $state("#propel_post_author").val($state(this).attr('id'));
		   $state(this).parent().parent().hide();	
		});
});
</script>
<style>
#ttip{
position:absolute;
width:300px;
height:100px;
border:1px solid #333;
background:white;
left:-80px;
top:160px;
z-index:999;
display:none;
}
#owner_result{
position:absolute;
width:200px;
height:120px;
overflow:auto;
border:1px solid #333;
background:white;
left:20px;
top:290px;
z-index:9999;
display:none;
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
			<input type="text" name="propel_post_author_display" id="propel_post_author_display" value="<?php echo $user_info->user_login ?>" onKeyUp="_list_owners()" >
			<input type="hidden" name = "propel_post_author" id="propel_post_author" value="<?php echo $user_info->ID ?>">
			
			<div id="ttip">
			<h3>Seach Tips</h3>
					<div style="margin:12px; line-height:25px">
					1.) Type in character/s found in a User's Login.<br/>
					2.) Leave it to blanks to search for all Users.
					</div>
			</div>
			<div id="owner_result"></div>
		</td>
	</tr>
	
</table>