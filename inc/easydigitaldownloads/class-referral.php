<?php
/**
 * Ignico class provided to save referral when order is placed in Easy Digital Downloads
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easydigitaldownloads
 */

namespace IgnicoWordPress\EasyDigitalDownloads;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Ignico class provided to save referral when order is placed in Easy Digital Downloads
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easydigitaldownloads
 */
class Referral {

	/**
	 * Plugin container.
	 *
	 * @var Init $plugin Ignico for WordPress plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin Ignico for WordPress plugin container.
	 *
	 * @return Referral
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Save referral when order is placed
	 *
	 * @param int 	 	   $payment_id Payment id
	 * @param \EDD_Payment $payment    Payment object
	 *
	 * @return void
	 */
	public function save_referral( $payment_id, $payment ) {

		$status = $payment->status;

		/**
		 * If status of the payment is not pending stop executing
		 */
		if( 'pending' !== $status ) {
			return;
		}

		$referral_key = '_ignico_referral';
		$referral     = $this->plugin['ignico/referral']->get_referral();

		if ( ! $referral || empty( $referral ) ) {
			return;
		}

		update_post_meta( $payment_id, $referral_key, $referral );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_action( 'edd_payment_saved', $this, 'save_referral', 10, 2 );
	}
}
