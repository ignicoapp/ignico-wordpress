<?php
/**
 * Ignico class provided to integrate WooCommerce with Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce;

/**
 * Ignico class provided to integrate WooCommerce with Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */
class Ignico {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Ignico
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Add transaction with to Ignico
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function add_transaction( $order_id ) {

		$referral_key = '_ignico_referral';
		$referral     = get_post_meta( $order_id, $referral_key, true );

		$transaction_added_key = '_ignico_transaction_added';
		$transaction_added     = (int) get_post_meta( $order_id, $transaction_added_key, true );

		/**
		 * If there is no referral do nothing
		 */
		if ( ! $referral || empty( $referral ) ) {
			return;
		}

		/**
		 * If order is already sent to Ignico stop executing.
		 */
		if ( 1 === $transaction_added ) {
			return;
		}

		try {

			$order = new \WC_Order( $order_id );

			/* Translators: %d is WooCommerce order id */
			$title = sprintf( esc_html__( 'new WooCommerce order %d', 'ignico' ), $order_id );
			$date  = current_time( 'Y-m-d H:i:s' );

			$total = $order->get_total();

			$data = array(
				'data' => array(
					'type'       => 'action',
					'attributes' => array(
						'type'      => 'transaction',
						'title'     => $title,
						'takenAt'   => $date,
						'performer' => array(
							'referralCode' => $referral,
						),
						'params'    => array(
							'value' => $total,
						),
					),
				),
			);

			$this->plugin['ignico/client']->action()->add( $data );

			update_post_meta( $order_id, $transaction_added_key, 1 );

		} catch ( \Exception $e ) {

			/**
			 * TODO: Add logger to provide information to user why there might be a problem
			 */

			return;
		}
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		/**
		 * The best moment to add transaction to Ignico service is when order
		 * is paid by user. WooCommerce has convention to set status of paid
		 * orders to processing.
		 */
		$this->plugin['loader']->add_action( 'woocommerce_order_status_processing', $this, 'add_transaction', 10, 2 );

		/**
		 * If for some reason WooCommerce will omit processing status would be
		 * good add transaction when order is set to completed.
		 */
		$this->plugin['loader']->add_action( 'woocommerce_order_status_completed', $this, 'add_transaction', 10, 2 );
	}
}
