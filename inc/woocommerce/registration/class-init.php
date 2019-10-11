<?php
/**
 * Initial class to manage checkout part of the WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/registration
 */

namespace IgnicoWordPress\WooCommerce\Registration;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage checkout part of the WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/registration
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
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	public function load() {
		$this->plugin['woocommerce/registration/registration'] = new Registration( $this->plugin );
		$this->plugin['woocommerce/registration/consent']      = new Consent( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/registration/registration']->run();
		$this->plugin['woocommerce/registration/consent']->run();
	}
}
