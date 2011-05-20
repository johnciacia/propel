<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/*
Plugin Name: Propel
Plugin URI: http://www.johnciacia.com/propel/
Description: Easily manage your projects, clients, tasks, and files.
Version: 1.6
Author: John Ciacia
Author URI: http://www.johnciacia.com

Copyright 2009  John Ciacia  (email : software [at] johnciacia [dot] com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

require_once('models/projectsModel.php');
require_once('models/tasksModel.php');
$propel = new Propel();


/**
 *
 */
add_action('init', array($propel, 'init'));
/**
*
*/
add_action('admin_init', array($propel, 'admin_init'));
/**
 *
 */
add_action('admin_menu', array($propel , 'admin_menu'));
/**
 * 
 */
add_filter('screen_layout_columns', 
	array(&$propel, 'set_columns'), 10, 2);
/**
*
*/  
add_action('load-propel_page_propel-dashboard', 
	array(&$propel, 'on_load_propel_page_propel_dashboard'));
	
add_action('load-admin_page_propel-edit-project',
	array(&$propel, 'on_load_admin_page_propel_edit_project'));

add_action('load-toplevel_page_propel',
	array(&$propel, 'on_load_toplevel_page_propel'));	
/**
 * Actions
 */
add_action('admin_post_propel_update_project', 
	array(&$propel, 'updateProjectAction'));

add_action('admin_action_propel-delete-project', 
	array(&$propel, 'deleteProjectAction'));

add_action('admin_post_propel-create-project', 
	array(&$propel, 'createProjectAction'));
		
add_action('admin_action_propel-delete-task', 
	array(&$propel, 'deleteTaskAction'));
	
add_action('admin_action_propel-complete-task', 
	array(&$propel, 'completeTaskAction'));	
	
add_action('admin_post_propel_create_task', 
	array(&$propel, 'createTaskAction'));

add_action('admin_post_propel-update-task', 
	array(&$propel, 'updateTaskAction'));

add_action('wp_ajax_propel-quick-tasks', 
	array(&$propel, 'quickTaskAjax'));	

add_action('wp_ajax_propel-rss', 
	array(&$propel, 'rss'));	
/**
 * Shortcodes
 */
add_shortcode('pl-projects', array($propel , 'projectsShortcode'));
/**
 *
 */
register_activation_hook(__FILE__, array($propel , 'install'));

/**
 * @see http://codex.wordpress.org/Writing_a_Plugin
 */
class Propel
{	
	private $projectsModel;
	private $tasksModel;
	
	public function __construct()
	{
		$this->projectsModel = new ProjectsModel();
		$this->tasksModel = new TasksModel();
	}
	
	/**
	* @see http://codex.wordpress.org/Function_Reference/add_action
	* @since 1.0
	*/
	public function admin_menu ()
	{
		
		add_menu_page('Propel', 'Propel', 'publish_pages', 
			'propel', array(&$this , 'propelPage'));
			
		add_submenu_page('propel', 'Dashboard', "Dashboard", 
			'publish_pages', 'propel-dashboard', array(&$this, 'dashboardPage'));
		
		add_submenu_page('propel', 'Projects', "Projects", 
			'publish_pages', 'propel-projects', array(&$this, 'projectsPage'));
						
		add_submenu_page(null, null, 'Edit Project', 
			'publish_pages', 'propel-edit-project', array(&$this, 'editProjectPage'));
			
		add_submenu_page(null, null, 'Edit Task', 
			'publish_pages', 'propel-edit-task', array(&$this, 'editTaskPage'));

		add_submenu_page(null, null, 'Create Project', 
			'publish_pages', 'propel-create-project', array(&$this, 'createProjectPage'));					
	}

	/**
	* @since 1.6
	*/
	public function admin_init ()
	{
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('common');
		wp_enqueue_script('postbox');			
	}
	
    /**
     * Initialize CSS and JavaScript
     * @TODO: Only load the JavaScript when necessary
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @since 1.1
     */
	public function init ()
	{ 
			
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-datepicker', 
			WP_PLUGIN_URL . '/propel/js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
		wp_enqueue_script('jquery-ui-progressbar', 
			WP_PLUGIN_URL . '/propel/js/jquery.ui.progressbar.min.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget') );
					
		wp_register_style("propel-jquery-ui", get_option('propel_theme'));
		wp_register_style("genesis-ui", WP_PLUGIN_URL . '/propel/gen/ui.css');
		wp_register_style("propel-ui", WP_PLUGIN_URL . '/propel/style.css');

		wp_enqueue_style('genesis-ui');
		wp_enqueue_style('propel-jquery-ui');
		wp_enqueue_style('propel-ui');
	}

    /**
     * @since 1.6
     */
	function set_columns($columns, $screen) {
		$columns['toplevel_page_propel'] = 2;
		$columns['propel_page_propel-dashboard'] = 2;
		$columns['admin_page_propel-edit-project'] = 2;
		return $columns;
	}


	/**
	* Add widgets to the edit-album page
	*/
	public function on_load_propel_page_propel_dashboard() 
	{
		add_meta_box('propel-quick-tasks', 'Quick Tasks', array(&$this, 'quickTasksWidget'), 
			'propel_page_propel-dashboard', 'side', 'core');

		add_meta_box('propel-list-my-tasks', 'My Tasks', array(&$this, 'listMyTasksWidget'), 
			'propel_page_propel-dashboard', 'normal', 'core'); 

	}
	
	public function on_load_admin_page_propel_edit_project ()
	{			
		add_meta_box('propel-list-tasks', 'Tasks', array(&$this, 'listTasksWidget'), 
			'admin_page_propel-edit-project', 'normal', 'core');
			
		add_meta_box('propel-add-task', 'Add Task', array(&$this, 'createTaskWidget'), 
			'admin_page_propel-edit-project', 'side', 'core');	
			
		add_meta_box('propel-edit-project', 'Edit Project', array(&$this, 'editProjectWidget'), 
			'admin_page_propel-edit-project', 'side', 'core');	
	}
	
	public function on_load_toplevel_page_propel ()
	{
		add_meta_box('propel-about', 'About', array(&$this, 'aboutWidget'), 
			'toplevel_page_propel', 'normal', 'core');
			
		add_meta_box('propel-faq', 'FAQ', array(&$this, 'faqWidget'), 
			'toplevel_page_propel', 'normal', 'core');
			
		add_meta_box('propel-support', 'Support', array(&$this, 'supportWidget'), 
			'toplevel_page_propel', 'normal', 'core');
			
		add_meta_box('propel-contribute', 'Contribute', array(&$this, 'contributeWidget'), 
			'toplevel_page_propel', 'normal', 'core');

		add_meta_box('propel-latest-news', 'Latest News', array(&$this, 'latestNewsWidget'), 
			'toplevel_page_propel', 'side', 'core');
			
		add_meta_box('propel-support-forums', 'Support Forums', array(&$this, 'supportForumsWidget'), 
			'toplevel_page_propel', 'side', 'core');

		add_meta_box('propel-revision-log', 'Revision Log', array(&$this, 'revisionLogWidget'), 
			'toplevel_page_propel', 'side', 'core');
	}
	
	/***************************************************\
	|                       PAGES                       |
	\***************************************************/	
	public function propelPage ()
	{
		global $screen_layout_columns;
		$data = array(
					'feeds' => array(
						"http://www.johnciacia.com/category/propel/feed",
						"http://wordpress.org/support/rss/tags/propel",
						"http://plugins.trac.wordpress.org/log/propel?limit=10&mode=stop_on_copy&format=rss"
						)
					);
		$pagehook = "toplevel_page_propel";
		require_once('template.php');
	}
	
	public function projectsPage ()
	{
		$projects = $this->projectsModel->getProjects();
		require_once('pages/projectsPage.php');
	}
	
	public function dashboardPage ()
	{
		global $screen_layout_columns;
		$data = array();
		$pagehook = "propel_page_propel-dashboard";
		require_once('template.php');
	}
	
	public function editProjectPage ()
	{
		global $screen_layout_columns;
		$data = array();
		$pagehook = "admin_page_propel-edit-project";
		require_once('template.php');
	}
	
	public function createProjectPage ()
	{
		require_once('pages/createProjectPage.php');
	}
	
	public function editTaskPage ()
	{
		$task = $this->tasksModel->getTaskById($_GET['id']);
		$users = $this->tasksModel->getUsers();
		require_once('pages/editTaskPage.php');
	}
	
	
	
	/***************************************************\
	|                      WIDGETS                      |
	\***************************************************/
	public function aboutWidget()
	{
		require_once('widgets/about.php');
	}
	
	public function faqWidget ()
	{
		require_once('widgets/faq.php');
	}
	
	public function supportWidget ()
	{
		require_once('widgets/support.php');		
	}
	
	public function contributeWidget ()
	{
		require_once('widgets/contribute.php');
	}
	
	public function latestNewsWidget ($data)
	{
		$id = 0;
		$feed = $data['feeds'][$id];
		require('widgets/rss.php');
	}

	public function supportForumsWidget ($data)
	{
		$id = 1;
		$feed = $data['feeds'][$id];
		require('widgets/rss.php');
	}
	
	public function revisionLogWidget ($data)
	{
		$id = 2;
		$feed = $data['feeds'][$id];
		require('widgets/rss.php');
	}
		
	public function editProjectWidget ()
	{
		$project = $this->projectsModel->getProjectById($_GET['id']);
		require_once('widgets/editProject.php');
	} 
	
	public function listProjectsWidget ()
	{
		echo "List projects";
	}
	
	public function listMyTasksWidget ()
	{
		$tasks = $this->tasksModel->getTasksByUser();
		require_once('widgets/myTasks.php');
	}
	
	public function quickTasksWidget ()
	{	
		$projects = $this->projectsModel->getProjects();
		require_once('widgets/quickTasks.php');
	}
	
	public function listTasksWidget ()
	{
		$tasks = $this->tasksModel->getTasksByProject($_GET['id']);
		require_once('widgets/listTasks.php');
	}
	
	public function createTaskWidget ()
	{
		$users = $this->tasksModel->getUsers();
		require_once('widgets/createTask.php');
	}


	/***************************************************\
	|                      ACTIONS                      |
	\***************************************************/
	public function updateProjectAction ()
	{
		$this->projectsModel->updateProject($_POST);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function deleteProjectAction ()
	{
		$this->projectsModel->deleteProject($_GET['id']);
		$this->tasksModel->deleteTasksByProject($_GET['id']);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function createProjectAction ()
	{
		$this->projectsModel->createProject($_POST);
		wp_redirect("admin.php?page=propel-projects");	
	}
	
	public function createTaskAction ()
	{
		if($this->tasksModel->createTask($_POST) == false)
			update_option('PROPEL_ERROR', "Task creation failed.");
			
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function completeTaskAction () 
	{
		$this->tasksModel->completeTask($_GET['task']);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function deleteTaskAction ()
	{
		$this->tasksModel->deleteTask($_GET['task']);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	
	public function updateTaskAction ()
	{
		$this->tasksModel->updateTask($_POST);
		wp_redirect($_POST['redirect']);
	}
	
	public function quickTaskAjax ()
	{
		$id = $this->tasksModel->createTask($_POST);
		$task = $this->tasksModel->getTaskById($id, ARRAY_A);
		
		if($task['complete'] == 100) {
			$task['status'] = "Complete";
			$task['color'] = "#0000cc";
		} 
		
		else {
			if(date("Y-m-d") == $task['end']) {
				$task['status'] = "Today";
				$task['color'] = "#ffa500";
			} else if(date("Y-m-d") > $task['end']) {
				$task['status'] = "Overdue";
				$task['color'] = "#ff0000";
			} else {
				$task['status'] = "Later";
				$task['color'] = "#008000";
			}
		}
		
		echo json_encode($task);
		die();		
	}
	
	public function rss ()
	{
		$rss = fetch_feed($_POST['feed']);
		if (!is_wp_error( $rss ) ) { 
		    $maxitems = $rss->get_item_quantity(5); 
		    $rss_items = $rss->get_items(0, $maxitems); 
		}
		
		echo '<ul>';
		if ($maxitems == 0) echo '<li>No items.</li>';
		else {
			foreach ($rss_items as $item) { 
				echo "<li>
						<p><a href='".$item->get_permalink()."' title='Posted ". $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a><br />";
				$description = strip_tags($item->get_description()); 
		       	$description = substr($description, 0, -37);
		       	echo $description;
			    echo '</p></li>';
			}
		}
				     
		echo '</ul>';	
		die();	
	}
	
	/***************************************************\
	|                    SHORTCODES                     |
	\***************************************************/
	public function projectsShortcode ($atts)
	{
		
		$tasks = $this->tasksModel->getTasksByProject(1);
		require_once('frontend/projects_new.php');
	}
	/***************************************************\
	|                       MISC                        |
	\***************************************************/		
	/**
	* @see http://codex.wordpress.org/Creating_Tables_with_Plugins
	* @since 1.0
	*/
	public function install ()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "projects";

		$sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
			`id` int(11) NOT NULL auto_increment,
			`title` varchar(255) NOT NULL,
			`description` text NOT NULL,
			`start` date NOT NULL,
			`end` date NOT NULL,
			PRIMARY KEY  (`id`)
			);";
		$result = $wpdb->query($sql);
        
		$table_name = $wpdb->prefix . "tasks";
		$sql = "CREATE TABLE `" . $table_name . "` (
			`id` int(11) NOT NULL auto_increment,
			`pid` int(11) NOT NULL,
			`uid` int(11) NOT NULL default '0',
			`title` varchar(255) NOT NULL,
			`description` text NOT NULL,
			`start` date NOT NULL,
			`end` date NOT NULL,
			`priority` int(11) NOT NULL,
			`complete` int(11) NOT NULL default '0',
			`approved` int(11) NOT NULL default '1',
			PRIMARY KEY  (`id`)
			);";
		$result = $wpdb->query($sql);

		/*
		* There was an error creating the database.
		*/
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return;
		}

		add_option('propel_theme', WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css');
		add_option('PROPEL_ERROR', '');
		/*
		* @since 1.2
		*/
		add_option("PROPEL_DBVERSION", 1.4);
	}
	
}

?>