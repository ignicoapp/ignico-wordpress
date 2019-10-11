<?php
/**
 * Ignico class provided to add new user to Ignico when account is created in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce\Order;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Ignico class provided to add new user to Ignico when account is created in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */
class Service {

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
	 * @return Service
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add referrer to order
	 *
	 * To track partner referrals we have to add them to Ignico database to be
	 * aware which customer, identified by e-mail address, bought product.
	 * Without storing e-mail address we would only has single record in
	 * database about new anonymous referral.
	 *
	 * Ignico user may exist in database already because user also will be added
	 * to Ignico during registration without placing order in store. If user
	 * with given email address already exist in Ignico assign Ignico user to
	 * WooCommerce user otherwise create user new Ignico user.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function add_referrer_to_order( $order_id ) {

		ig_info( 'Add referrer to order.' );

		$order_repo = $this->plugin['woocommerce/order/repository'];
		$referrer   = $this->plugin['ignico/referrer'];

		$user     = $order_repo->get_user( $order_id );
		$referrer = $referrer->get_referrer( $user->ID );

		if ( $referrer ) {
			$result = $order_repo->save_referrer( $order_id, $referrer );

			if ( false !== $result ) {
				ig_info( 'Referrer successfully added to order.' );
			} else {
				ig_info( 'Referrer could not be added to order.' );
			}
		} else {
			ig_info( 'Referrer is not provided.' );
		}
	}

	/**
	 * Create or assign Ignico user on placing order.
	 *
	 * To track partner referrals we have to add them to Ignico database to be
	 * aware which customer, identified by e-mail address, bought product.
	 * Without storing e-mail address we would only has single record in
	 * database about new anonymous referral.
	 *
	 * Ignico user may exist in database already because user also wlll be added
	 * to Ignico during registration without placing order in store. If user
	 * with given email addres already exist in Ignico assign Ignico user to
	 * WooCoommerce user otherwise create user new Ignico user.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function add_user_to_ignico( $order_id ) {

		ig_info( 'Create or assign WooCommerce user to Ignico user based on order.' );

		$ignico_repo    = $this->plugin['ignico/repository'];
		$ignico_service = $this->plugin['ignico/service'];
		$user_service   = $this->plugin['wordpress/user/service'];
		$order_repo     = $this->plugin['woocommerce/order/repository'];

		$order_repo->get_order_or_throw_not_found( $order_id );
		$email = $order_repo->get_email( $order_id );

		if ( $ignico_repo->user_exists_by_email( $email ) ) {
			if ( $order_repo->has_user( $order_id ) ) {
				$user = $order_repo->get_user( $order_id );
				if ( ! $user_service->is_assigned( $user->ID ) ) {
					$ignico_user = $ignico_repo->get_user_by_email( $email );
					$user_service->assign( $user->ID, $ignico_user->attributes->id );
				}
			}
		} else {
			$ignico_service->add_user_based_on_order( $order_id );
		}
	}

	/**
	 * Add action with to Ignico
	 *
	 * @param int $order_id Order id.
	 *
	 * @return void
	 */
	public function add_action_based_on_order( $order_id ) {

		ig_info( 'Add action to Ignico.', [ 'order_id' => $order_id ] );

		$ignico_service = $this->plugin['ignico/service'];
		$order_repo     = $this->plugin['woocommerce/order/repository'];

		// If order is already sent to Ignico stop executing.
		if ( $order_repo->has_action_been_added( $order_id ) ) {
			ig_info( 'Action for given order already has been added to Ignico.', [ 'order' => $order_id ] );
			return;
		}

		$ignico_service->add_action_based_on_order( $order_id );
	}
}
