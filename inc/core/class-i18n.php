<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this theme
 * so that it is ready for translation.
 *
 * @link       http://igni.co/
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 * @author     Ignico <contact@igni.co>
 */

namespace IgnicoWordPress\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this themes
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 * @author     Ignico <contact@igni.co>
 */
class I18n {

	/**
	 * Plugin container.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param    object $plugin IgnicoWordPress Plugin container.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Load the theme text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ignico',
			false,
			str_replace( '_', '-', $this->plugin['id'] ) . '/languages'
		);
	}
}
