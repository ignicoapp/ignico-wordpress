<?php
/**
 * Initial class to manage WooCommerce My Account page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

/**
 * Initial class to manage WooCommerce My Account page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */
class Init {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	private function load_dependencies() {

	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

	}
}
