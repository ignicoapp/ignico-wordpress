<?php
/**
 * Class provided to handle plugin logic when certain operations are carried out
 * on WooCommerce order.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/checkout
 */

namespace IgnicoWordPress\WooCommerce\Checkout;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class provided to handle plugin logic when certain operations are carried out
 * on WooCommerce order.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */
class Checkout {

	/**
	 * Plugin container.
	 *
	 * @var Init $plugin Ignico plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Checkout
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		/**
		 * Handle new order
		 */
		$this->plugin['loader']->add_action( 'woocommerce_checkout_order_processed', $this, 'handle_new_order' );
	}

	/**
	 * Handle new order hook
	 *
	 * @param int $order_id WooCommerce order ID
	 *
	 * @return void
	 */
	public function handle_new_order( $order_id ) {

		ig_info( 'Create or assign WooCommerce user to Ignico user from checkout form.' );

		if ( ig_consent_required() && ! $this->plugin['woocommerce/checkout/consent']->is_valid() ) {
			ig_info( 'Create or assign WooCommerce user to Ignico not allowed because user do not give consent.' );
			return;
		}

		$service = $this->plugin['woocommerce/order/service'];
		$service->add_user_to_ignico( $order_id );
	}
}
