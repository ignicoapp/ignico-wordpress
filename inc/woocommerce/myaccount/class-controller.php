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
class Controller {

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
		add_action( 'wp', array( $this, 'dispatch' ) );
	}

	/**
	 * Dispatch controller
	 *
	 * @return void
	 */
	public function dispatch() {

		if ( ig_is_rewards_program_page() ) {
			$this->rewards_program_action();
		}

		if ( ig_is_consent_page() ) {
			$this->consent_action();
		}

		if ( ig_is_coupon_page() ) {
			$this->coupon_action();
		}

		if ( ig_is_payout_page() ) {
			$this->payout_action();
		}
	}

	/**
	 * Action provided to execute code before rewards program page will be displayed
	 *
	 * @return void
	 */
	public function rewards_program_action() {

		$this->redirect_if_not_logged_in();
		$this->redirect_if_not_assigned();
	}

	/**
	 * Action provided to execute code before consent page will be displayed
	 *
	 * @return void
	 */
	public function consent_action() {

		$this->redirect_if_not_logged_in();

		if ( $this->is_consent_given() ) {
			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}

		// Prepare form.
		$form = $this->plugin['woocommerce/myaccount/form/consent'];

		if ( $form->is_submitted() ) {

			ig_info( 'Create or assign WooCommerce user to Ignico user from my account consent page.' );

			if ( ig_consent_required() && ! $form->is_valid() ) {
				ig_info( 'User do not give consent.' );
				return;
			}

			$ignico_repo    = $this->plugin['ignico/repository'];
			$ignico_service = $this->plugin['ignico/service'];
			$user_repo      = $this->plugin['wordpress/user/repository'];
			$user_service   = $this->plugin['wordpress/user/service'];

			$user  = $user_repo->get_user_or_throw_not_found( get_current_user_id() );
			$email = $user->user_email;

			if ( $ignico_repo->user_exists_by_email( $email ) ) {

				if ( ! $user_service->is_assigned( $user->ID ) ) {

					$ignico_user = $ignico_repo->get_user_by_email( $email );
					$user_service->assign( $user->ID, $ignico_user->attributes->id );
				}
			} else {
				$ignico_service->add_user( get_current_user_id() );
			}

			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}
	}

	/**
	 * Action provided to execute code before coupon page will be displayed
	 *
	 * @return void
	 *
	 * @throws \Exception When coupon can not be generated.
	 */
	public function coupon_action() {

		$this->redirect_if_not_logged_in();
		$this->redirect_if_not_assigned();
		$this->redirect_if_coupon_payout_is_not_available();

		$user = wp_get_current_user();

		// Prepare form.
		$form = new CouponForm( $this->plugin, [ 'amount' => $this->get_user_wallet_amount( $user->ID ) ] );

		if ( $form->is_submitted() && $form->is_valid() ) {

			global $wpdb;

			$wpdb->query( 'START TRANSACTION' );

			try {

				// Get data from the form and sanitize it.
				$data = $this->sanitize( $form->get_data() );

				// Create coupon.
				$coupon_id = $this->plugin['woocommerce/coupon/service']->add( $data );

				// Check if everything went ok.
				if ( ! $coupon_id || $coupon_id instanceof \WP_Error ) {
					throw new \Exception( 'Can not create coupon. Create method from coupon service do not returned post ID.' );
				}

				$wpdb->query( 'COMMIT' );
			} catch ( \Exception $e ) {
				$wpdb->query( 'ROLLBACK' );

				$this->plugin['notice']->add_flash_notice( __( 'We are sorry but we were unable to generate coupon. Please try again later or contact support.', 'ignico' ), Notice::ERROR );

				wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
				exit;
			}

			$this->plugin['notice']->add_flash_notice( __( 'You have successfully created coupon.', 'ignico' ), Notice::SUCCESS );

			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}

		$this->plugin['woocommerce/myaccount/form/coupon'] = $form;
	}

	/**
	 * Action provided to execute code before payout page will be displayed
	 *
	 * @return void
	 *
	 * @throws \Exception When payout can not be created.
	 */
	public function payout_action() {

		$this->redirect_if_not_logged_in();
		$this->redirect_if_not_assigned();
		$this->redirect_if_payout_payout_is_not_available();

		$user = wp_get_current_user();

		// Prepare form.
		$form = new PayoutForm( $this->plugin, [ 'amount' => $this->get_user_wallet_amount( $user->ID ) ] );

		if ( $form->is_submitted() && $form->is_valid() ) {

			global $wpdb;

			$wpdb->query( 'START TRANSACTION' );

			try {

				// Get data from the form and sanitize it.
				$data = $this->sanitize( $form->get_data() );

				// Create payout.
				$payout_id = $this->plugin['woocommerce/payout/service']->add( $data );

				// Check if everything went ok.
				if ( ! $payout_id || $payout_id instanceof \WP_Error ) {
					throw new \Exception( 'Can not create payout. Create method from payout service do not returned post ID.' );
				}

				$wpdb->query( 'COMMIT' );

				// Initialize mailer to send emails.
				WC()->mailer();

			} catch ( \Exception $e ) {

				$wpdb->query( 'ROLLBACK' );

				$this->plugin['notice']->add_flash_notice( __( 'We are sorry but we were unable to create payout request. Please try again later or contact support.', 'ignico' ), Notice::ERROR );

				wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
				exit;
			}

			$this->plugin['notice']->add_flash_notice( __( 'You have successfully created payout request. Please wait for a contact from administration to determine the form of payout.', 'ignico' ), Notice::SUCCESS );

			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}

		$this->plugin['woocommerce/myaccount/form/payout'] = $form;
	}

	/**
	 * Redirect user if he is not logged in
	 *
	 * @return void
	 */
	private function redirect_if_not_logged_in() {

		// If user is not logged in redirect him to my account page to login.
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
			exit;
		}
	}

	/**
	 * Redirect user if he is not assigned
	 *
	 * @return void
	 */
	private function redirect_if_not_assigned() {

		$user_repo    = $this->plugin['wordpress/user/repository'];
		$user_service = $this->plugin['wordpress/user/service'];

		$user = $user_repo->get_user_or_throw_not_found( get_current_user_id() );

		if ( ! $user_service->is_assigned( $user->ID ) ) {
			wp_safe_redirect( get_permalink( ig_get_consent_page_id() ) );
			exit;
		}
	}

	/**
	 * Redirect user payout option is not available
	 *
	 * @return void
	 */
	public function redirect_if_payout_payout_is_not_available() {
		if ( ! ig_is_payout_available() ) {
			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}
	}

	/**
	 * Redirect user coupon option is not available
	 *
	 * @return void
	 */
	public function redirect_if_coupon_payout_is_not_available() {
		if ( ! ig_is_coupon_available() ) {
			wp_safe_redirect( get_permalink( ig_get_rewards_program_page_id() ) );
			exit;
		}
	}

	/**
	 * Check is consent is given
	 *
	 * @return void
	 */
	private function is_consent_given() {
		return get_user_meta( get_current_user_id(), '_ignico_consent_given', true );
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
	 * Get user wallet amount from Ignico
	 *
	 * @param int $user_id WooCommerce user ID
	 *
	 * @return float
	 */
	private function get_user_wallet_amount( $user_id ) {

		$wallet = $this->get_user_wallet( $user_id );

		if ( is_null( $wallet ) ) {
			return 0;
		}

		return $wallet->balanceAccounting;
	}

	/**
	 * Sanitize form data
	 *
	 * @param array $data Unsafe data.
	 *
	 * @return array
	 */
	private function sanitize( $data ) {

		// Define filters.
		$filters = array(
			'amount' => array(
				'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
				'flags'  => FILTER_FLAG_ALLOW_FRACTION,
			),
		);

		// Fields sanitization.
		return filter_var_array( $data, $filters );
	}
}
