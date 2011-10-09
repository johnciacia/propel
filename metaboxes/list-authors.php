<?php 
$users = get_users(); 
$contributors = get_post_meta( get_the_ID(), '_propel_contributors');
?>


<div id="propel_list_users" class="categorydiv">
	<ul id="propel_list_users-tabs" class="category-tabs">
		<li class="tabs">
			<a href="#propel_user-all">All Users</a>
		</li>
	</ul>



	<div id="propel_user-all" class="tabs-panel" style="display: block; ">
		<ul id="propel_userschecklist" class="list:propel_category categorychecklist form-no-clear">

		<?php foreach($users as $user) : ?>
		<li id="propel_user-<?php echo $user->ID; ?>" class="popular-category">
			<label class="selectit">
				<?php if( is_array($contributors[0]) && in_array( $user->ID, $contributors[0] ) ) : ?>
				<input value="<?php echo $user->ID; ?>" type="checkbox" name="propel_user[]" id="in-propel_user-<?php echo $user->ID; ?>" checked> <?php echo $user->display_name; ?>
				<?php else : ?>
				<input value="<?php echo $user->ID; ?>" type="checkbox" name="propel_user[]" id="in-propel_user-<?php echo $user->ID; ?>"> <?php echo $user->display_name; ?>
				<?php endif; ?>
			</label>
		</li>
		<?php endforeach; ?>				
		</ul>
	</div>
</div>