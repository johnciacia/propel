<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/*
Plugin Name: Propel
Plugin URI: http://www.johnciacia.com/propel/
Description: Easily manage your projects, clients, tasks, and files.
Version: 1.5.2
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

/*
 * Some code, ideas, and methodologies used by this plugin have been 
 * borrowed from three of my favorite plugins:
 * NextGEN Gallery
 * WordPress Download Monitor
 * All in One SEO Pack
 */

require_once ("PropelController.php");
require_once ("PropelModel.php");
$propel = new Propel();


/**
 * @see Propel::init()
 */
add_action('init', array($propel, 'init'));
/**
 * @see Propel::dashboard_widgets()
 */
add_action('wp_dashboard_setup', array($propel , 'dashboard_widgets'));
/**
 * @see Propel::admin_menu()
 */
add_action('admin_menu', array($propel , 'admin_menu'));
/**
 * @see Propel::ajax()
 */
add_action('wp_ajax_quick-task', array($propel , 'ajax'));
/**
 * @see Propel::ajax()
 */
add_action('wp_ajax_task-glance', array($propel , 'ajax'));
/**
 * @since 1.1
 * @depreciated 1.2
 * @see Propel::shortcode_projects()
 */
add_shortcode('projects', array($propel , 'shortcode_projects'));
/**
 * @since 1.2
 * @see Propel::shortcode_projects()
 */
add_shortcode('pl-projects', array($propel , 'shortcode_projects'));
/**
 * @see Propel::shortcode()
 */
add_shortcode('pl-feedback', array($propel , 'shortcode_feedback'));
/**
 * @see Propel::install()
 */
register_activation_hook(__FILE__, array($propel , 'install'));


/**
 * @see http://codex.wordpress.org/Writing_a_Plugin
 */
class Propel
{
    /**
     * @see http://codex.wordpress.org/Function_Reference/add_action
     * @since 1.0
     */
    public function admin_menu ()
    {
        $propel = new PropelController();
        add_menu_page('Propel', 'Propel', 'manage_options', 'propel', array(&$propel , 'propel'));
        add_submenu_page('propel', 'Overview', 'Overview', 'manage_options', 'propel-overview', array(&$propel , 'overview'));
        add_submenu_page('propel', 'Projects', 'Projects', 'manage_options', 'propel-projects', array(&$propel , 'projects'));
        add_submenu_page('propel', 'Tasks', 'Tasks', 'manage_options', 'propel-tasks', array(&$propel , 'tasks'));
        //add_submenu_page('propel', 'Files', 'Files', 'manage_options', 'propel-files', array(&$propel , 'files'));
        add_submenu_page('propel', 'Settings', 'Settings', 'manage_options', 'propel-settings', array(&$propel , 'settings'));
    }

    /**
     * Initialize CSS and JavaScript
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @since 1.1
     */
    public function init ()
    {    	
		wp_register_style("propel_theme", get_option('propel_theme'));
		wp_enqueue_style("propel_theme");

    	$styles = array("/propel/style.css");

    	$scripts = array("/propel/js/jquery-1.4.2.min.js",
    					 "/propel/js/jquery-ui-1.7.3.custom.min.js",  
    					 "/propel/js/functions.js");
    	
 		for($i = 0; $i < count($styles); $i++) {
			$url = WP_PLUGIN_URL . $styles[$i];
			$file = WP_PLUGIN_DIR . $styles[$i];
			if (file_exists($file)) {
				wp_register_style('propel_style_' . $i, $url);
				wp_enqueue_style('propel_style_' . $i);
			}   	
 		}
    	
     	for($i = 0; $i < count($scripts); $i++) {
			$url = WP_PLUGIN_URL . $scripts[$i];
			$file = WP_PLUGIN_DIR . $scripts[$i];
			if (file_exists($file)) {
				wp_register_script('propel_script_' . $i, $url);
				wp_enqueue_script('propel_script_' . $i);
			}   	
 		}   	
    }
    
    /**
     * Initialize dashboard widgets
     * @see http://codex.wordpress.org/Dashboard_Widgets_API
     * @since 1.1
     * @todo 
     */
    public function dashboard_widgets ()
    {
    	require_once('WidgetController.php');
    	$widgets = new Widgets();
		wp_add_dashboard_widget('propel-task-list', 'Tasks', 
								 array($widgets , 'task_list'), 
								 array($widgets , 'config_task_list'));
										
		
		wp_add_dashboard_widget('propel-quick-task', 'Add Task', 
								array($widgets , 'add_task'), 
								array($widgets , 'config_add_task'));	
	} 

    /**
     * @see http://codex.wordpress.org/Shortcode_API
     * [pl-project id="5"] (id is optional)
     * @since 1.1
     * @return
     */
    public function shortcode_projects ($atts)
    {
        $propel = new PropelController();
        return $propel->shortcode('project', $atts);
    }

    /**
     * @see http://codex.wordpress.org/Shortcode_API
     * @since 1.2
     * @return
     */
    public function shortcode_feedback ($atts)
    {
        $propel = new PropelController();
        return $propel->shortcode('feedback', $atts);
    }
    
	/**
	 * Handle AJAX requests
	 * @todo Use check_ajax_referer() for security
	 * @see http://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)
	 * @see http://codex.wordpress.org/AJAX_in_Plugins
	 * @since 1.2
	 */
	public function ajax ()
	{
    	require_once('WidgetController.php');
    	$widgets = new Widgets();
    	echo $widgets->ajax($_POST['action']);
	    die();
	}
	
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
        
		add_option('propel_theme', '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css');
        
         /*
          * @since 1.2
          */
        add_option("PROPEL_DBVERSION", 1.4);
    }
}

?>