<input class="metabox-add-task-title widefat" type="text" name="task_title" id="_task_title" placeholder="Title" />

<?php if( Propel_Options::option('show_end_date' ) ) : ?>
<input class="metabox-add-task-title widefat date" type="text" name="task_end_date" placeholder="End Date" />
<?php endif; ?>

<label>Manager:</label>
<?php 
$current_user = wp_get_current_user();
$args = array(
'class' => 'metabox-add-task-user',
'name' => 'propel_post_author',
'show_option_none' => 'Unassigned',
'orderby' => 'display_name',
'selected' => $current_user->ID
);
wp_dropdown_users( $args );
?>
<input class="metabox-add-task-button button-primary" type="button" id="add-task" value="Add Task" />

<textarea class="metabox-add-task-description widefat" name="task_description" id="_task_desc" placeholder="Description"></textarea>