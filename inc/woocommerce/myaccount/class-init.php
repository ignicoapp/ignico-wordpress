<?php
/**
 * Initial class to manage WooCommerce My Account page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

use IgnicoWordPress\Core\Init as CoreInit;

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
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	public function load() {
		require_once __DIR__ . '/functions.php';

		$this->plugin['woocommerce/myaccount/activation'] = new Activation( $this->plugin );
		$this->plugin['woocommerce/myaccount/controller'] = new Controller( $this->plugin );
		$this->plugin['woocommerce/myaccount/view']       = new View( $this->plugin );
		$this->plugin['woocommerce/myaccount/shortcode']  = new Shortcode( $this->plugin );

		$this->plugin['woocommerce/myaccount/form/consent'] = new Consent_Form( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/myaccount/activation']->run();
		$this->plugin['woocommerce/myaccount/controller']->run();
		$this->plugin['woocommerce/myaccount/view']->run();
		$this->plugin['woocommerce/myaccount/shortcode']->run();

		$this->plugin['woocommerce/myaccount/form/consent']->run();
	}
}
