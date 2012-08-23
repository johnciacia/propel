<?php $users = get_users( array( 'orderby' => 'display_name', 'order' => 'ASC' ) ); ?>

<div id="propel_list_users" class="categorydiv">
	<ul id="propel_list_users-tabs" class="category-tabs">
		<li class="tabs">
			<a href="#propel_user-all">All Users</a>
		</li>
	</ul>

	<div id="propel_user-all" class="tabs-panel" style="display: block; ">
		<ul id="propel_userschecklist" class="list:propel_category categorychecklist form-no-clear">

		<?php foreach($users as $user) : ?>
			<li id="propel_user-<?php esc_attr_e($user->ID); ?>" class="popular-category">
				<label class="selectit">
					<input value="<?php  esc_attr_e($user->user_login); ?>" type="checkbox" name="coauthors[]" id="in-propel_user-<?php echo $user->ID; ?>" <?php 
					if( propel_is_coauthor( $user->ID ) ) { echo "checked='checked' "; }
					if( propel_is_parent_coauthor( $user->ID ) ) { echo "disabled='disabled'"; }
					?>> <?php esc_html_e($user->display_name); ?>
					<?php
					if( propel_is_parent_coauthor( $user->ID ) ) {
					?>
						<input value="<?php  esc_attr_e($user->user_login); ?>" type="hidden" name="coauthors[]" />
					<?php
					}
					?>
				</label>
			</li>
		<?php endforeach; ?>				
		</ul>
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
}