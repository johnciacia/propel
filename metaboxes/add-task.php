<div>
<input class="metabox-add-task-title widefat" type="text" name="task_title" id="_task_title" placeholder="Title" />

<?php if( Propel_Options::option('show_end_date' ) ) : ?>
<input class="metabox-add-task-date widefat date" type="text" name="task_end_date" placeholder="End Date" />
<?php endif; ?>
<input class="metabox-add-task-button button-primary" type="button" id="add-task" value="Add Task" />
<div style="border:1px solid #DFDFDF;background:#FFF;margin-top:5px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;">
<ul id="selected_task_contributor">
</ul>
<input  class="metabox-add-task-title widefat" type="text" name="task_contributor" id="task_contributor" placeholder="Contributor" />

<?php 
	
	global $post;
	
	$coauthor_terms = wp_get_post_terms( $post->ID, 'author', $args );	
	if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
		$select = "<select id='propel_post_author' class='metabox-add-task-contributor task-priority'>"; 
		$select .= "<option value='-1'>Undefined</option>"; 
		foreach($coauthor_terms as $coauthor) {	
			$post_author =  get_user_by( 'login', $coauthor->name );
			$select .= "<option value='".$post_author->ID."'>".$post_author->display_name."</option>"; 
		}
		$select .= "<select>"; 
		_e($select);
	}	
	
	
//	$args = array(
//	'class' => 'metabox-add-task-contributor',
//	'name' => 'propel_post_author',
//	'show_option_none' => 'Unassigned',
//	'orderby' => 'display_name',
//	);
//	wp_dropdown_users( $args );

	$coauthor_terms = wp_get_post_terms( $post->ID, 'author', $args );	
	if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
		$html = "<ul id='task_contributor_list'>";
		$html .= "<li id='Undefined' data-value='-1' class='propel_not_added'><div class='add_contributor'></div>Undefined</li>";
		foreach($coauthor_terms as $coauthor) {	
			$post_author =  get_user_by( 'login', $coauthor->name );
			if ( $post_author->ID == $post->post_author ){
				$html .= "<li id='".$post_author->user_login."' data-value='".$post_author->ID."' class='propel_is_added'><div class='del_contributor'></div>".$post_author->display_name."</li>";	
			}else{
				$html .= "<li id='".$post_author->user_login."' data-value='".$post_author->ID."' class='propel_not_added'><div class='add_contributor'></div>".$post_author->display_name."</li>";	
			}	
		}
		$html .= "</ul>";
		_e($html);

	}
	
//	$html = "<ul id='task_contributor_list'>";
//	$users = get_users();
//	foreach($users as $user):
//		if ( $user->user_login == $task->post_author ){
//			$html .= "<li id='".$user->user_login."' data-value='".$user->ID."' class='propel_is_added'><div class='del_contributor'></div>".$user->display_name."</li>";	
//		}else{
//			$html .= "<li id='".$user->user_login."' data-value='".$user->ID."' class='propel_not_added'><div class='add_contributor'></div>".$user->display_name."</li>";	
//		}		
//	endforeach;
//	$html .= "</ul>";
//	_e($html);
	
?>
</div>
</div>
<textarea class="metabox-add-task-description widefat" name="task_description" id="_task_desc" placeholder="Description" style="min-height:80px;"></textarea>
<div id="propel_add_media">
    <img id="img_propel_attach" src="<?php echo plugins_url();?>/propel/images/attachment.png" title="Click to add media"/>
    <ul id="propel_ul_img_attach">
    	<li><a href="<?php echo plugins_url();?>/propel/images/attachment.png" title="Click to view" target="_blank">Attachment1.png </a><p class="propel_media_remove">x</p></li>
    	<li><a href="<?php echo plugins_url();?>/propel/images/attachment.png" title="Click to view" target="_blank">Attachment2.png </a><p class="propel_media_remove">x</p></li>
    	<li><a href="<?php echo plugins_url();?>/propel/images/attachment.png" title="Click to view" target="_blank">Attachment3.png </a><p class="propel_media_remove">x</p></li>
    	<li><a href="<?php echo plugins_url();?>/propel/images/attachment.png" title="Click to view" target="_blank">Attachment4.png </a><p class="propel_media_remove">x</p></li>                        
    </ul>
	    
</div>