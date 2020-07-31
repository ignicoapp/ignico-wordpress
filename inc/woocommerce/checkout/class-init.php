<?php
/**
 * Initial class to manage checkout part of the WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Checkout;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage checkout part of the WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
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

		$this->plugin['woocommerce/checkout/checkout'] = new Checkout( $this->plugin );
		$this->plugin['woocommerce/checkout/consent']  = new Consent( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/checkout/checkout']->run();
		$this->plugin['woocommerce/checkout/consent']->run();
	}
}
