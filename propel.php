<?php
/*
Plugin Name: Propel
Plugin URI: http://www.johnciacia.com/propel/
Description: Easily manage your projects, clients, tasks, and files.
Version: 2.0.4
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

define('PROPEL_CURRENT_DBVERSION', 1.6);
/**
 * @since 1.7.0
*/
if(get_option('PROPEL_DBVERSION') < PROPEL_CURRENT_DBVERSION)
	add_action('admin_notices', 'propel_add_notice');

	
function propel_add_notice () {
	echo "<div id='my_admin_notice' class='updated fade'><p><strong>Propel has changed its database structure. To continue using this plugin, you must first use our <a href='?page=propel_migrate_tool'>migration tool</a></strong></p></div>";
}
 

Propel_Options::initialize();
Propel::initialize();



class Propel {	
	
	public static function initialize() {
		if(get_option('PROPEL_DBVERSION') < PROPEL_CURRENT_DBVERSION) {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
			return;
		}

		add_action( 'admin_init', array(__CLASS__, 'admin_init' ) );
		add_action( 'init', array( __CLASS__, 'init'));
		
		require_once( dirname(__FILE__) . '/functions.php' );
		require_once( dirname(__FILE__) . '/post-types/project.php' );
		require_once( dirname(__FILE__) . '/post-types/task.php' );
		require_once( dirname(__FILE__) . '/deprecated.php' );
		if( Propel_Options::get_option('time_tracking') ) 
			require_once( dirname(__FILE__) . '/post-types/time.php' );
		if( Propel_Options::get_option('user_restrictions') ) 
			require_once( dirname(__FILE__) . '/plugins/users.php' );
	}
		
	/**
	* @since 1.0
	*/
	public static function admin_menu() {
		if( get_option( 'PROPEL_DBVERSION' ) < PROPEL_CURRENT_DBVERSION ) {
			add_menu_page( null, 'Propel', 'activate_plugins', 'propel_migrate_tool', array( __CLASS__ , 'migration_tool' ) );
			return;
		}
	}

	public static function migration_tool() {
		global $wpdb;
		define( 'PROPEL_MIGRATE_DB', 1 );
		require_once( 'migrate.php' );
	}

	/**
	* @since 1.6
	*/
	public static function admin_init () {
		wp_enqueue_script('jquery-datatables', 
			WP_PLUGIN_URL . '/propel/js/jquery.dataTables.min.js', array('jquery', 'jquery-ui-core') );
		wp_enqueue_script('propel-functions', 
			WP_PLUGIN_URL . '/propel/js/functions.js', array( 'jquery-datatables' ) );
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

		$options = get_option( 'propel_options' );
		wp_register_style("propel-jquery-ui", $options['theme'] );
		wp_register_style("genesis-ui", WP_PLUGIN_URL . '/propel/gen/ui.css');
		wp_register_style("propel-ui", WP_PLUGIN_URL . '/propel/style.css');

		wp_enqueue_style('genesis-ui');
		wp_enqueue_style('propel-jquery-ui');
		if(get_option('PROPEL_INCLUDE_CSS') == true)
			wp_enqueue_style('propel-ui');
	}	

	
}

class Propel_Options {
	
	public static function get_option($option) {
		$options = get_option('propel_options');

		if( isset( $options[$option] ) ) 
			return $options[$option];

		return 0;
	}

	public static function initialize() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

	}

	public static function admin_menu() {
		add_options_page( 'Propel', 'Propel', 'manage_options', 'propel-options', array( __CLASS__, 'options' ) );			
	}

	public static function options() { 
	?>
	<div class="wrap">
		<h2>Propel Options</h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'propel_options' ); ?>
			<?php do_settings_sections( 'propel' ); ?>

			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</form>
	</div>

	<?php
	}

	public static function admin_init(){
		register_setting( 'propel_options', 'propel_options', array( __CLASS__, 'options_validate' ) );
		add_settings_section( 'propel_main', 'Main Settings', array( __CLASS__, 'plugin_section_text' ), 'propel' );
		add_settings_section( 'propel_deprecated', 'Deprecated Settings', array( __CLASS__, 'plugin_section_deprecated' ), 'propel' );
		// add_settings_field( 'propel_beta_options', 'Beta Options', array( __CLASS__, 'propel_beta_options' ), 'propel', 'propel_main' );
		add_settings_field( 'propel_ui_options', 'UI Options', array( __CLASS__, 'propel_ui_options' ), 'propel', 'propel_main' );
		add_settings_field( 'propel_deprecated_options', 'Custom Theme Directory', array( __CLASS__, 'propel_deprecated_options' ), 'propel', 'propel_deprecated' );
	}

	public static function plugin_section_text() {
		echo '<p>These options allow you to customize Propel.</p>';
	}

	public static function plugin_section_deprecated() {
		echo '<p>These options are for deprecated features.</p>';
	}

	public static function propel_beta_options() {
		$options = get_option( 'propel_options' );

		echo '<input name="propel_options[dnd]" id="propel_dnd" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['dnd']), false ) . ' /> Enable Drag and Drop Ordering';
		echo '<br />';
		echo '<input name="propel_options[user_restrictions]" id="propel_user_restrictions" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['user_restrictions']), false ) . ' /> Enable User Restrictions';
		echo '<br />';
		echo '<input name="propel_options[time_tracking]" id="propel_time_tracking" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['time_tracking']), false ) . ' /> Enable Time Tracking';
	}

	public static function propel_ui_options() {
		$options = get_option( 'propel_options' );

		echo '<input name="propel_options[show_start_date]" id="show_start_date" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['show_start_date']), false ) . ' /> Show Start Date';
		echo '<br />';

		echo '<input name="propel_options[show_end_date]" id="show_end_date" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['show_end_date']), false ) . ' /> Show End Date';
		echo '<br />';
		
		echo '<input name="propel_options[show_client]" id="show_client" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['show_client']), false ) . ' /> Show Client';
		echo '<br />';
		echo '<input name="propel_options[show_progress]" id="show_progress" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['show_progress']), false ) . ' /> Show Project Progress';
		echo '<br />';
		echo '<br /><br />';
	}

	public static function propel_deprecated_options() {
		$options = get_option( 'propel_options' );
		do_action( 'propel_deprecated_options', $options );
		echo '<br /><br />';
	}

	public static function options_validate( $input ) {
		return $input;
	}

	public static function option( $option ) {
		$options = get_option('propel_options');
		return (isset($options[$option])) ? (bool)$options[$option] : false;
	}
}

function propel_get_priorities() {
	return array( 'Low', 'Medium', 'High' );
}



register_activation_hook( __FILE__, 'propel_install' );
function propel_install () {
		/*
		* @since 2.0.3
		*/
		$options = get_option( 'propel_options' );
		if( ! isset( $options['theme'] ) || empty( $options['theme'] ) ) {
			$options['theme'] = WP_PLUGIN_URL . '/propel/themes/smoothness/jquery-ui-1.8.6.custom.css';
			update_option( 'propel_options', $options );
		}

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
		add_option( 'PROPEL_DBVERSION', PROPEL_CURRENT_DBVERSION );
}
?>