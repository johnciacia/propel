<?php
/* this will be an ajax search for the contributors
 * - have replaced the existing and placed it in an
 *   ajax called file list-authors_ajax.php
 */
?>
<script type="text/javascript">
function _list_authors(){
	var http_req = new XMLHttpRequest();
	var module = "<?php echo plugins_url(); ?>/propel/metaboxes/list-authors_ajax.php";
	var user = document.getElementById("users_get").value; 
	var vars = "user="+ user ; 
	http_req.open("POST", module, true);
	http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_req.onreadystatechange = function(){
		if (http_req.readyState == 4 && http_req.status == 200){
			var return_info = http_req.responseText;
			document.getElementById("result_get").innerHTML = return_info;
		}
	}
	http_req.send(vars); 	
	document.getElementById("result_get").innerHTML = "..searching";
}
</script>
<?php $users = get_users( array( 'orderby' => 'display_name', 'order' => 'ASC' ) ); ?>

<div id="propel_list_users" class="categorydiv">
	<ul id="propel_list_users-tabs" class="category-tabs">
		<li class="tabs">
			<a href="#propel_user-all">All Contributors</a>
		</li>
	</ul>
	
    <div id="task_contributor_content"> 
    	<?php 
			global $typenow;
			if ( $typenow != 'propel_task' ){
		 ?>
	    	<input  class="widefat" type="text" name="user_task_contributor" id="user_task_contributor" placeholder="Contributor" autocomplete="off"/>    
 			<div id="propel_user-all" class="tabs-panel" style="display: block;padding:0;margin:0;border:none;">       
		<?php }else{ ?>
        	<div id="propel_user-all" class="tabs-panel" style="display: block;padding:15px;margin:0;border:1px solid #DFDFDF;"> 
        <?php } ?>

            <ul id="propel_userschecklist" class="list:propel_category categorychecklist form-no-clear">
    
            <?php foreach($users as $user) : 
				
				$lenstr = strlen($user->display_name);
				
				if ($lenstr > 25){
					$username = substr($user->display_name,0,25).'...';
				}else{
					$username = $user->display_name;
				}
				
				//preg_match ('/^@/',$user->user_login,$matches, PREG_OFFSET_CAPTURE);

				if ( strpos($user->user_login,'@') ){
					$userid = substr($user->user_login,0,strpos($user->user_login,'@'));
				}else{
					$userid = $user->user_login;
				}
				
			
                if ( propel_is_coauthor( $user->ID )){
                    $html_ .= "<li id='".$userid."' data-value='".$user->ID."' class='propel_is_added'><div class='user_del_contributor'></div>".$username."</li>";	
                }else{
                    $html .= "<li id='".$userid."' data-value='".$user->ID."' class='propel_not_added'><div class='user_add_contributor'></div>".$username."</li>";	
                }
			 
			 endforeach; 
			 
			 	echo $html.$html_;
			 	
			 ?>				
            </ul>
        </div>
	</div>
</div>

<?php
wp_nonce_field( 'coauthors-edit', 'coauthors-nonce' );
function propel_is_coauthor( $user_id ) {
	$coauthors = Propel_Authors::get_coauthors();
	foreach($coauthors as $coauthor) {
		if($coauthor->ID == $user_id) return true;
	}
	return false;
}

function propel_is_parent_coauthor( $user_id ) {
	$post = get_post( get_the_ID() );
	if( $post->post_type != 'propel_task' || $post->post_parent == 0 ) return false;

	$coauthors = Propel_Authors::get_coauthors( $post->post_parent );
	foreach($coauthors as $coauthor) {
		if($coauthor->ID == $user_id) return true;
	}
	return false;
}?>


<!--
<input value="<?php  //esc_attr_e($user->user_login); ?>" type="checkbox" name="coauthors[]" id="in-propel_user-<?php //echo $user->ID; ?>" 
-->
      	<?php
			if( propel_is_coauthor( $user->ID ) ) { 
				//echo "checked='checked' "; 
			}
			if( propel_is_parent_coauthor( $user->ID ) ) { 
				//echo "disabled='disabled'"; 
			}
		
			?> 
			
			<?php //esc_html_e($user->display_name); ?>
									
			<?php
			if( propel_is_parent_coauthor( $user->ID ) ) {
			?>
				<!--
                <input value="<?php  //esc_attr_e($user->user_login); ?>" type="hidden" name="coauthors[]" />
                -->
			<?php
			}
		?>
