<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this theme
 * so that it is ready for translation.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 */

namespace IgnicoWordPress\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this themes
 * so that it is ready for translation.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 */
class I18n {

	/**
	 * Plugin container.
	 *
	 * @var      object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param    object $plugin IgnicoWordPress Plugin container.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Load the theme text domain for translation.
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ignico',
			false,
			str_replace( '_', '-', $this->plugin['id'] ) . '/languages'
		);
	}
}
