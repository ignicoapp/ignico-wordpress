<?php
/**
 * File provided for custom plugin core functions
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function ignico() {

	static $plugin;

	if ( isset( $plugin ) && $plugin instanceof \IgnicoWordPress\Core\Init ) {
		return $plugin;
	}

	$plugin = new \IgnicoWordPress\Core\Init();
	$plugin->load();
	$plugin->run();

}
