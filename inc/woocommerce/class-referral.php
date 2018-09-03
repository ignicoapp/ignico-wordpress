<?php
/**
 * Ignico class provided to save referral when order is placed in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Ignico class provided to save referral when order is placed in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */
class Referral {

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
	 * Save referral when order is placed
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function save_referral( $order_id ) {

		$settings = $this->plugin['admin/settings']->get_settings();

		$referral_key = '_ignico_referral';
		$referral     = $this->plugin['ignico/referral']->get_referral();

		if ( ! $referral || empty( $referral ) ) {
			return;
		}

		update_post_meta( $order_id, $referral_key, $referral );

		if ( (bool) $settings['cookie_removal'] ) {
			$this->plugin['ignico/referral']->delete_cookie();
		}
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_action( 'woocommerce_new_order', $this, 'save_referral' );
	}
}
