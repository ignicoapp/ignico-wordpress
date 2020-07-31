<?php
/**
 * Class to provide controller for referral pogram page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

use IgnicoWordPress\Core\Notice;

use IgnicoWordPress\Admin\Settings;

use IgnicoWordPress\WooCommerce\Payout\Form as PayoutForm;
use IgnicoWordPress\WooCommerce\Coupon\Form as CouponForm;

/**
 * Class to provide controller for rewards program page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */
class Shortcode {


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
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		add_shortcode( 'ignico-referral-links', [ $this, 'referral_links' ] );
		add_shortcode( 'ignico-commission', [ $this, 'commission' ] );
	}

	public function referral_links() {
		return ig_render( __DIR__ . '/partials/shortcodes/referral-links.php' );
	}

	public function commission() {

		$user_id = get_current_user_id();

		$wallet        = $this->get_user_wallet( $user_id );
		$wallet_amount = $this->get_wallet_amount( $wallet );

		$coupons = $this->plugin['woocommerce/coupon/repository']->find_by_user( $user_id );
		$payouts = $this->plugin['woocommerce/payout/repository']->find_by_user( $user_id );
		$actions = $this->get_user_actions( $user_id );

		return ig_render(
			__DIR__ . '/partials/shortcodes/commission.php', [
				'wallet_amount' => $wallet_amount,
				'actions'       => $actions,
				'coupons'       => $coupons,
				'payouts'       => $payouts,
			]
		);
	}

	/**
	 * Get user wallet from Ignico
	 *
	 * @param int $user_id WooCommerce user ID
	 *
	 * @return \stdObject|null
	 */
	private function get_user_wallet( $user_id ) {

		$ignico_user_id = get_user_meta( $user_id, '_ignico_id', true );

		$response = $this->plugin['ignico/client']->wallet()->find( $ignico_user_id );

		if ( isset( $response->data ) ) {
			return $response->data->attributes;
		}

		return null;
	}

	/**
	 * Get and format wallet amount on money
	 *
	 * This is view helper used by some of the views
	 *
	 * @param \stdObject $wallet
	 *
	 * @return string
	 */
	public function get_wallet_amount( $wallet ) {
		if ( ! $wallet || ! isset( $wallet->balanceAccounting ) ) {
			return 0;
		}

		return $wallet->balanceAccounting;
	}

	/**
	 * Get user actions from Ignico
	 *
	 * @param int $user_id WooCommerce user ID
	 *
	 * @return array
	 */
	private function get_user_actions( $user_id ) {
		$actionsFiltered = [];

		$current_user_id = get_current_user_id();
		$ignico_user_id  = get_user_meta( $user_id, '_ignico_id', true );

		$response = $this->plugin['ignico/client']->action()->all(
			[
				'filter[cancelled]' => 0,
				'include'           => 'performer',
			]
		);

		if ( isset( $response->data ) ) {

			$actions = $response->data;

			foreach ( $actions as $action ) {
				if ( $action->relationships->performer &&
					(int) $action->relationships->referrer->data->id === (int) $ignico_user_id
				) {
					$performer_id = (int) $action->relationships->performer->data->id;

					if ( $current_user_id !== $performer_id ) {

						$performer = $this->get_performer_from_includes( $response, $performer_id );

						$actionsFiltered[] = [
							'performer'  => $performer->email,
							'title'      => $action->attributes->title,
							'value'      => $action->attributes->params->value,
							'created_at' => $action->attributes->createdAt,
						];
					}
				}
			}
		}

		return $actionsFiltered;
	}

	/**
	 * Get user byt meta value
	 *
	 * @param string $meta_key   User meta key
	 * @param string $meta_value User meta value
	 *
	 * @return WP_User
	 */
	private function get_user_by_meta_value( $meta_key, $meta_value ) {
		$users = get_users(
			[
				'meta_key'   => $meta_key,
				'meta_value' => $meta_value,
			]
		);

		if ( ! $users ) {
			return null;
		}

		return current( $users );
	}

	/**
	 * Get performer from includes
	 *
	 * @return WP_User
	 */
	private function get_performer_from_includes( $response, $performer_id ) {

		$includes = $response->included;

		foreach ( $includes as $include ) {

			if ( $include->type === 'user' && (int) $include->id === (int) $performer_id ) {
				return $include->attributes;
			}
		}

		return null;
	}
}
