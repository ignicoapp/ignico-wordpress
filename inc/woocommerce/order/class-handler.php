<?php
/**
 * Class provided to handle plugin logic when certain operations are carried out
 * on WooCommerce order.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */

namespace IgnicoWordPress\WooCommerce\Order;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class provided to handle plugin logic when certain operations are carried out
 * on WooCommerce order.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */
class Handler {

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
	 * @return Referral
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
		$this->plugin['loader']->add_action( 'woocommerce_new_order', $this, 'handle_new_order' );

		/**
		 * The best moment to add transaction to Ignico service is when order
		 * is paid by user. WooCommerce has convention to set status of paid
		 * orders to processing.
		 */
		$this->plugin['loader']->add_action( 'woocommerce_order_status_processing', $this, 'handle_order_placed', 10, 2 );

		/**
		 * If for some reason WooCommerce will omit processing status would be
		 * good add transaction when order is set to completed.
		 */
		$this->plugin['loader']->add_action( 'woocommerce_order_status_completed', $this, 'handle_order_placed', 10, 2 );
	}

	/**
	 * Handle new order hook
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return void
	 */
	public function handle_new_order( $order_id ) {
		$service = $this->plugin['woocommerce/order/service'];
		$service->add_referrer_to_order( $order_id );
	}

	/**
	 * Handle order placed hook
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return void
	 */
	public function handle_order_placed( $order_id ) {
		$service = $this->plugin['woocommerce/order/service'];
		$service->add_action_based_on_order( $order_id );
	}
}
