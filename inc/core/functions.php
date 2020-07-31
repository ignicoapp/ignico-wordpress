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

/**
 * Save info log
 *
 * @param string $message Log message.
 * @param mixed  $context Log context.
 */
function ig_info( $message, $context = [] ) {
	$plugin = ignico();
	$plugin['logger']->info( $message, $context );
}

/**
 * Save error log
 *
 * @param string $message Log message.
 * @param mixed  $context Log context.
 */
function ig_error( $message, $context = [] ) {
	$plugin = ignico();
	$plugin['logger']->error( $message, $context );
}

/**
 * Function provided to render php file to the html string
 *
 * @param string $partial Partial path.
 * @param array  $vars    Partial variables.
 *
 * @return string
 *
 * @throws \Exception When partial can not be found.
 */
function ig_render( $partial, $vars = array() ) {

	if ( is_array( $vars ) && ! empty( $vars ) ) {
		extract( $vars );
	}

	try {
		if ( ! file_exists( $partial ) ) {
			throw new \Exception( sprintf( 'Partial file "%s" do not exist.', $partial ) );
		}

		ob_start();

		include $partial;

		$output = ob_get_clean();
	} catch ( \Exception $e ) {
		ob_end_clean();
		throw $e;
	}

	return $output;
}
