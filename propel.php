<?php
/**
* @todo: create import tool
* @todo: remove unnecessary code
*/

/*
Plugin Name: Propel
Plugin URI: http://www.johnciacia.com/propel/
Description: Easily manage your projects, clients, tasks, and files.
Version: 1.8
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

/**
 * @since 1.7.0
*/
if(get_option('PROPEL_DBVERSION') == 1.4)
	add_action('admin_notices', 'propel_add_notice');

if(get_option('PROPEL_DBVERSION') == 1.5)
	add_action('admin_notices', 'propel_add_notice');	
	
function propel_add_notice () {
	echo "<div id='my_admin_notice' class='updated fade'><p><strong>Propel has changed its database structure. To continue using this plugin, you must first use our <a href='?page=propel_migrate_tool'>migration tool</a></strong></p></div>";
}
 

Propel::initialize();
	


class Propel {	
	
	public static function initialize() {
		add_action('admin_init', array(__CLASS__, 'admin_init'));
		add_action('init', array(__CLASS__, 'init'));
		add_action('admin_menu', array(__CLASS__, 'admin_menu'));
		add_shortcode('pl-projects', array(__CLASS__, 'projectsShortcode'));
		register_activation_hook(__FILE__, array(__CLASS__, 'install'));

		require_once( __DIR__ . '/post-types/project.php' );
		require_once( __DIR__ . '/post-types/task.php' );
		require_once( __DIR__ . '/post-types/time.php' );
	}
		
	/**
	* @since 1.0
	*/
	public static function admin_menu ()
	{
		if( get_option('PROPEL_DBVERSION') == 1.4 ||
			get_option('PROPEL_DBVERSION') == 1.5 ) {
			//add_menu_page(null, 'Propel', 'activate_plugins', 
			//	'propel_migrate_tool', array(__CLASS__ , 'migrateTool'));			
			//return;
		}

		add_submenu_page('options-general.php', 'Propel', "Propel", 
			'publish_pages', 'propel-settings', array(__CLASS__, 'settingsPage'));				
	}

	/**
	* @since 1.6
	*/
	public static function admin_init () {
		
		//wp_enqueue_script('wp-lists');
		//wp_enqueue_script('common');
		//wp_enqueue_script('postbox');	
		//wp_enqueue_script('jquery-datatables', 
		//	WP_PLUGIN_URL . '/propel/js/jquery.dataTables.min.js', array('jquery', 'jquery-ui-core') );		
		wp_register_style("propel-admin-jquery-ui", WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css');
		wp_enqueue_style('propel-admin-jquery-ui');
	}
	
    /**
     * Initialize CSS and JavaScript
     * @TODO: Only load the JavaScript when necessary
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @since 1.1
     */
	public static function init () { 
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-datepicker', 
			WP_PLUGIN_URL . '/propel/js/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
		wp_enqueue_script('jquery-ui-progressbar', 
			WP_PLUGIN_URL . '/propel/js/jquery.ui.progressbar.min.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget') );
		//wp_enqueue_script('propel-functions', 
		//	WP_PLUGIN_URL . '/propel/js/functions.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget') );
								
		wp_register_style("propel-jquery-ui", get_option('propel_theme'));
		wp_register_style("genesis-ui", WP_PLUGIN_URL . '/propel/gen/ui.css');
		wp_register_style("propel-ui", WP_PLUGIN_URL . '/propel/style.css');

		wp_enqueue_style('genesis-ui');
		wp_enqueue_style('propel-jquery-ui');
		if(get_option('PROPEL_INCLUDE_CSS') == true)
			wp_enqueue_style('propel-ui');
	}

 
	
	public static function settingsPage () {
		require_once('models/misc.php');
		$helper = new Helper();
		$themes = $helper->getTemplates();
		require_once('pages/settingsPage.php');
	}
	
	public static function updateSettingsAction ()
	{	
		
		if($_POST['propel_include_css'] == "on") {
			update_option('PROPEL_INCLUDE_CSS', true);
		} else {
			update_option('PROPEL_INCLUDE_CSS', false);			
		}
		
		update_option('propel_theme', $_POST['propel_theme']);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}

	
	
	/***************************************************\
	|                    SHORTCODES                     |
	\***************************************************/
	public static function projectsShortcode ($atts)
	{
		/*
		extract(shortcode_atts(array('id' => NULL), $atts));
		
		if($id == NULL) { 
			$projects = $this->projectsModel->getProjects();

			foreach($projects as $project) {

				$tasks[$project->post_name] = $this->tasksModel->getTasksByProject($project->ID);
			}
		} else {
			$projects[] = $this->projectsModel->getProjectById($id);
			$tasks[$projects[0]->post_name] = $this->tasksModel->getTasksByProject($projects[0]->ID);
		}
	
					
		ob_start();
		require_once('frontend/projects_new.php');
		return ob_get_clean();
		*/
	}
	/***************************************************\
	|                       MISC                        |
	\***************************************************/	
	public static function migrateTool ()
	{
		global $wpdb;
		define('PROPEL_MIGRATE_DB', 1);
		require_once('migrate.php');
	}	
	
	
	/**
	* @since 1.0
	*/
	public static function install ()
	{
		add_option( 'propel_theme', WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css' );
		/*
		* @since 1.6
		*/
		add_option( 'PROPEL_ERROR', '' );
		/*
		* @since 1.7
		*/
		add_option( 'PROPEL_INCLUDE_CSS', true );
		/*
		* @since 1.2
		*/
		add_option( 'PROPEL_DBVERSION', 1.6 );
	}
	
}

?>