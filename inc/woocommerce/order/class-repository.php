<?php
/**
 * Class to provide repository for WooCommerce order
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */

namespace IgnicoWordPress\WooCommerce\Order;

use \Exception;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class to provide repository for WooCommerce order
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/order
 */
class Repository {

	/**
	 * Action added meta key
	 *
	 * @var string
	 */
	const ACTION_ADDED_META_KEY = '_ignico_action_added';

	/**
	 * Referrer meta key
	 *
	 * @var string
	 */
	const REFERRER_META_KEY = '_ignico_referrer';

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
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get user for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id..
	 */
	public function get_user( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );
		$user  = $order->get_user();

		if ( $user instanceof \WP_User ) {
			return $user;
		}

		return null;
	}

	/**
	 * Check if order has user
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id.
	 */
	public function has_user( $order_id ) {
		return $this->get_user( $order_id ) instanceof \WP_User;
	}

	/**
	 * Get user email for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id.
	 */
	public function get_email( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );
		$user  = $order->get_user();

		if ( $user ) {
			return $user->user_email;
		}

		return $order->get_billing_email();
	}

	/**
	 * Get user country for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id.
	 */
	public function get_country( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );

		return $order->get_billing_country();
	}

	/**
	 * Get user first name for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id.
	 */
	public function get_first_name( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );
		$user  = $order->get_user();

		if ( $user ) {
			return $user->user_first_name;
		}

		return $order->get_billing_first_name();
	}

	/**
	 * Get user last name for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 *
	 * @throws Exception When there is no order for given id.
	 */
	public function get_last_name( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );
		$user  = $order->get_user();

		if ( $user ) {
			return $user->user_last_name;
		}

		return $order->get_billing_last_name();
	}

	/**
	 * Get order total
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return string
	 *
	 * @throws Exception When order not found.
	 */
	public function get_total( $order_id ) {
		$order = $this->get_order_or_throw_not_found( $order_id );
		return $order->get_total();
	}

	/**
	 * Save indicator that action was already added for given order
	 *
	 * @param int   $order_id     WooCommerce order id.
	 * @param mixed $action_added Action added meta value.
	 *
	 * @return string
	 */
	public function save_action_added( $order_id, $action_added ) {
		return update_post_meta( $order_id, self::ACTION_ADDED_META_KEY, $action_added );
	}

	/**
	 * Get indicator that action was already addeed for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 */
	public function get_action_added( $order_id ) {
		return get_post_meta( $order_id, self::ACTION_ADDED_META_KEY, true );
	}

	/**
	 * Determine if action was already added for given order
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 */
	public function has_action_been_added( $order_id ) {
		return $this->get_action_added( $order_id ) === 1;
	}

	/**
	 * Save order referrer
	 *
	 * @param int    $order_id WooCommerce order id.
	 * @param string $referrer Ignico referrer id.
	 *
	 * @return string
	 */
	public function save_referrer( $order_id, $referrer ) {
		return update_post_meta( $order_id, self::REFERRER_META_KEY, $referrer );
	}

	/**
	 * Get order referrer
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return string
	 */
	public function get_referrer( $order_id ) {
		return get_post_meta( $order_id, self::REFERRER_META_KEY, true );
	}

	/**
	 * Determine if order has referrer
	 *
	 * @param int $order_id WooCommerce order id.
	 *
	 * @return bool
	 */
	public function has_referrer( $order_id ) {
		$referrer = $this->get_referrer( $order_id );
		return is_string( $referrer ) && ! empty( $referrer );
	}

	/**
	 * Get WordPress order by ID or throw exception if order do not exist
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return WC_Order|WC_Order_Refund
	 *
	 * @throws Exception When order not found.
	 */
	public function get_order_or_throw_not_found( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! ( $order instanceof \WC_Order ) ) {
			throw new Exception( 'Can not find WooCommerce order by provided order ID.' );
		}

		return $order;
	}
}
