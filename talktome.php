<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Zara
 *
 * @wordpress-plugin
 * Plugin Name:       Zara
 * Plugin URI:        http://example.com/talktome-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            omayib
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zara
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TALKTOME_VERSION', '1.0.0' );
define( 'TALKTOME_URL', trailingslashit( plugins_url('/', __FILE__) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-talktome-activator.php
 */
function activate_talktome() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-talktome-activator.php';
	Talktome_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-talktome-deactivator.php
 */
function deactivate_talktome() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-talktome-deactivator.php';
	Talktome_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_talktome' );
register_deactivation_hook( __FILE__, 'deactivate_talktome' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-talktome.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_talktome() {

	$plugin = new Talktome();
	$plugin->run();

}
run_talktome();
