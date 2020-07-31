<?php
/**
 * Ignico class provided to handle Ignico logic when certain operations
 * are carried out on WooCommerce coupon.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use WC_Coupon;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Ignico class provided to handle Ignico logic when certain operations
 * are carried out on WooCommerce coupon.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class Coupon {

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
	 * Update coupon usage
	 *
	 * Change coupon status to realized when coupon is applied
	 *
	 * @param int $order_id Coupon code
	 *
	 * @return void
	 */
	public function update_coupon_usage( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$has_recorded = $order->get_data_store()->get_recorded_coupon_usage_counts( $order );

		if ( $order->has_status( 'cancelled' ) && $has_recorded ) {
			$action = 'reduce';
		} elseif ( ! $order->has_status( 'cancelled' ) && ! $has_recorded ) {
			$action = 'increase';
		} else {
			return;
		}

		if ( count( $order->get_coupon_codes() ) > 0 ) {
			foreach ( $order->get_coupon_codes() as $code ) {
				if ( ! $code ) {
					continue;
				}

				$woo_coupon       = new WC_Coupon( $code );
				$ignico_coupon_id = $this->get_ignico_coupon_id( $woo_coupon->get_id() );

				if ( $ignico_coupon_id ) {
					switch ( $action ) {
						case 'reduce':
							update_post_meta( $ignico_coupon_id, Status::ID, Status::NOT_USED );
							update_post_meta( $ignico_coupon_id, '_ignico_coupon_date_realized', null );
							break;
						case 'increase':
							update_post_meta( $ignico_coupon_id, Status::ID, Status::USED );
							update_post_meta( $ignico_coupon_id, '_ignico_coupon_date_realized', current_time( 'mysql', 1 ) );
							break;
					}
				}
			}
		}
	}

	/**
	 * Get Ignico coupon ID
	 *
	 * return int
	 */
	private function get_ignico_coupon_id( $woo_coupon_id ) {

		global $wpdb;

		$table = $wpdb->prefix . 'postmeta';

		$sql = "SELECT post_id FROM $table where meta_key = '%s' and meta_value = %d";

		$meta_key   = '_ignico_coupon_id';
		$meta_value = $woo_coupon_id;

		return $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $meta_value ) );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		/**
		 * Change coupon status to realized when coupon is applied
		 */
		$this->plugin['loader']->add_action( 'woocommerce_order_status_pending', $this, 'update_coupon_usage', 5 ); // Execute hook before actual coupon is updated
		$this->plugin['loader']->add_action( 'woocommerce_order_status_completed', $this, 'update_coupon_usage', 5 ); // Execute hook before actual coupon is updated
		$this->plugin['loader']->add_action( 'woocommerce_order_status_processing', $this, 'update_coupon_usage', 5 ); // Execute hook before actual coupon is updated
		$this->plugin['loader']->add_action( 'woocommerce_order_status_on-hold', $this, 'update_coupon_usage', 5 ); // Execute hook before actual coupon is updated
		$this->plugin['loader']->add_action( 'woocommerce_order_status_cancelled', $this, 'update_coupon_usage', 5 ); // Execute hook before actual coupon is updated
	}
}
