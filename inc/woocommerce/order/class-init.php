<?php
/**
 * Initial class to manage woocommerce order functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */

namespace IgnicoWordPress\WooCommerce\Order;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage woocommerce order functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
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
		$this->plugin['woocommerce/order/repository'] = new Repository( $this->plugin );
		$this->plugin['woocommerce/order/service']    = new Service( $this->plugin );
		$this->plugin['woocommerce/order/handler']    = new Handler( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/order/handler']->run();
	}
}
