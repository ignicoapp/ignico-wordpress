<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://igni.co
 * @since             0.1.0
 * @package           IgnicoWordPress
 *
 * @wordpress-plugin
 * Plugin Name:       Ignico for WordPress
 * Plugin URI:        http://igni.co
 * Description:       Ignico for WordPress is plugin provided to integrate Ignico - Rewards & Commission Automation service with popular WordPress e-commerce systems WooCommerce and Easy Digital Downloads.
 * Version:           0.1.0
 * Author:            Ignico Sp. z o.o.
 * Author URI:        http://igni.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ignico
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load autoloader to not bother to requiring classes.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/autoloader.php';

require_once plugin_dir_path( __FILE__ ) . 'inc/core/functions.php';

ignico();
