<?php
/**
 * Initial class to manage woocommerce functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage woocommerce functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
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

		$this->plugin['woocommerce/referral'] = new Referral( $this->plugin );
		$this->plugin['woocommerce/ignico']   = new Ignico( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/referral']->run();
		$this->plugin['woocommerce/ignico']->run();
	}
}
