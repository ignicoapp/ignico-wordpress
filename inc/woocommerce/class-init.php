<?php
/**
 * Initial class to manage woocommerce functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce;

use \IgnicoWordPress\Core\Init as CoreInit;

use \IgnicoWordPress\WooCommerce\Registration\Init as RegistrationInit;
use \IgnicoWordPress\WooCommerce\Checkout\Init as CheckoutInit;
use \IgnicoWordPress\WooCommerce\Order\Init as OrderInit;
use \IgnicoWordPress\WooCommerce\MyAccount\Init as MyAccountInit;
use \IgnicoWordPress\WooCommerce\Coupon\Init as CouponInit;
use \IgnicoWordPress\WooCommerce\Payout\Init as PayoutInit;


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
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	public function load() {
		$this->plugin['woocommerce/registration'] = new RegistrationInit( $this->plugin );
		$this->plugin['woocommerce/checkout']     = new CheckoutInit( $this->plugin );
		$this->plugin['woocommerce/order']        = new OrderInit( $this->plugin );
		$this->plugin['woocommerce/myaccount']    = new MyAccountInit( $this->plugin );
		$this->plugin['woocommerce/coupon']       = new CouponInit( $this->plugin );
		$this->plugin['woocommerce/payout']       = new PayoutInit( $this->plugin );

		$this->plugin['woocommerce/registration']->load();
		$this->plugin['woocommerce/checkout']->load();
		$this->plugin['woocommerce/order']->load();
		$this->plugin['woocommerce/myaccount']->load();
		$this->plugin['woocommerce/coupon']->load();
		$this->plugin['woocommerce/payout']->load();
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/registration']->run();
		$this->plugin['woocommerce/checkout']->run();
		$this->plugin['woocommerce/order']->run();
		$this->plugin['woocommerce/myaccount']->run();
		$this->plugin['woocommerce/coupon']->run();
		$this->plugin['woocommerce/payout']->run();
	}
}
