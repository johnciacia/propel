<?php
// functions.php
function modify_menu()
{
  global $submenu;
  unset($submenu['edit.php?post_type=propel_project'][10]);

  // for posts it should be: 
  // unset($submenu['edit.php'][10]);
}
// call the function to modify the menu when the admin menu is drawn
add_action('admin_menu','modify_menu');
?>