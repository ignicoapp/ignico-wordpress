<?php
/**
 * Ignico class provided to integrate Easy Digital Downloads with Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easy-digital-downloads
 */

namespace IgnicoWordPress\EasyDigitalDownloads;

/**
 * Ignico class provided to integrate Easy Digital Downloads with Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easy-digital-downloads
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
	 * Add transaction to Ignico when payment get status completed
	 *
	 * @param int 	 $payment_id Payment id
	 * @param string $status     Payment status
	 * @param string $old_status Payment old status
	 *
	 * @return void
	 */
	public function add_transaction( $payment_id, $status, $old_status ) {

		/**
		 * If status of the payment is not publish = complete = completed
		 * stop executing
		 */
		if( 'publish' !== $status ) {
			return;
		}

		$referral_key = '_ignico_referral';
		$referral     = get_post_meta( $payment_id, $referral_key, true );

		$transaction_added_key = '_ignico_transaction_added';
		$transaction_added     = (int) get_post_meta( $payment_id, $transaction_added_key, true );

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

			$payment = new \EDD_Payment( $payment_id );

			/* Translators: %d is Easy Digital Downloads payment id */
			$title = sprintf( esc_html__( 'new Easy Digital Downloads payment %d', 'ignico' ), $payment_id );
			$date  = current_time( 'Y-m-d H:i:s' );

			$total = $payment->total;

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

			update_post_meta( $payment_id, $transaction_added_key, 1 );

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

		$this->plugin['loader']->add_action( 'edd_update_payment_status', $this, 'add_transaction', 10, 3 );
	}
}
