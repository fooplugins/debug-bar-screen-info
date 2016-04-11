<?php
/**
 * Debug Bar Screen Info
 *
 * Show screen info of the current admin page in a new tab within the debug bar
 *
 * @package   WordPress\Plugins\debug-bar-screen-info
 * @author    Brad Vincent <brad@fooplugins.com>
 * @link      https://github.com/fooplugins/debug-bar-screen-info
 * @version   1.1.5
 * @copyright 2013-2016 FooPlugins LLC
 * @license   http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Debug Bar Screen Info
 * Plugin URI:  https://github.com/fooplugins/debug-bar-screen-info
 * Description: Show screen info of the current admin page in a new tab within the debug bar
 * Version:     1.1.5
 * Author:      bradvin, jrf
 * Author URI:  http://fooplugins.com
 * Depends:     Debug Bar
 * Text Domain: debug-bar-screen-info
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

add_action( 'admin_init', 'dbsi_has_parent_plugin' );

if ( ! function_exists( 'dbsi_has_parent_plugin' ) ) {
	/**
	 * Show notice & de-activate itself if debug-bar plugin not active.
	 */
	function dbsi_has_parent_plugin() {
		if ( is_admin() && ( ! class_exists( 'Debug_Bar' ) && current_user_can( 'activate_plugins' ) ) ) {
			add_action( 'admin_notices', create_function( null, 'echo \'<div class="error"><p>\', sprintf( __( \'Activation failed: Debug Bar must be activated to use the <strong>Debug Bar Screen Info</strong> Plugin. <a href="%s">Visit your plugins page to activate</a>.\', \'debug-bar-screen-info\' ), admin_url( \'plugins.php#debug-bar\' ) ), \'</p></div>\';' ) );

			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
}

// Include plugin class.
require_once plugin_dir_path( __FILE__ ) . 'class-debug-bar-screen-info.php';

// Run it baby!
Debug_Bar_Admin_Screen_Info::get_instance();
