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
 * Plugin URI:        http://example.com/zara-uri/
 * Description:       Zara is an AI-powered WordPress plugin that makes content discovery interactive and fun. It uses engaging questions to help users quickly find the content they need.
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
define( 'ZARA_VERSION', '1.0.0' );
define( 'ZARA_URL', trailingslashit( plugins_url('/', __FILE__) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zara-activator.php
 */
function activate_zara() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zara-activator.php';
	Zara_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zara-deactivator.php
 */
function deactivate_zara() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zara-deactivator.php';
	Zara_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_zara' );
register_deactivation_hook( __FILE__, 'deactivate_zara' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zara.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_zara() {

	$plugin = new Zara();
	$plugin->run();

}
run_zara();
