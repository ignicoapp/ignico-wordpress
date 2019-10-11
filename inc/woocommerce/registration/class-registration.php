<?php
/**
 * Ignico class provided to add new user to Ignico when account is created in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */

namespace IgnicoWordPress\WooCommerce\Registration;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Ignico class provided to add new user to Ignico when account is created in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */
class Registration {

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
	 * @return Registration
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

		$this->plugin['loader']->add_action( 'woocommerce_register_post', $this, 'consent_validation', 10, 3 );
		$this->plugin['loader']->add_action( 'user_register', $this, 'add_or_assign_on_user_registration' );
	}

	/**
	 * Validate consent field
	 */
	public function consent_validation( $username, $email, $validation_errors ) {

		if ( ig_consent_required() && !$this->plugin['woocommerce/registration/consent']->is_valid() ) {
			$validation_errors->add( 'ignico_consent', __( 'Consent acceptance is required.', 'ignico' ) );
		}

		return $validation_errors;
	}

	/**
	 * Create or assign Ignico user on registration.
	 *
	 * To track partner referrals we have to add them to Ignico database to be
	 * aware which customer, identified by e-mail address, bought product.
	 * Without storing e-mail address we would only has single record in
	 * database about new anonymous referral.
	 *
	 * Ignico user may exist in database already because user also will be added
	 * to Ignico during placing order without registration in store. If user
	 * with given email address already exist in Ignico assign Ignico user to
	 * WooCoommerce user otherwise create new Ignico user.
	 *
	 * @param int $user_id Registered user ID.
	 *
	 * @return void
	 */
	public function add_or_assign_on_user_registration( $user_id ) {

		ig_info( 'Create or assign WooCommerce user to Ignico user from register form.' );

		if ( ig_consent_required() && ! $this->plugin['woocommerce/registration/consent']->is_valid() ) {
			ig_info( 'Create or assign WooCommerce user to Ignico not allowed because user do not give consent.' );
			return;
		}

		$ignico_repo    = $this->plugin['ignico/repository'];
		$ignico_service = $this->plugin['ignico/service'];
		$user_repo      = $this->plugin['wordpress/user/repository'];
		$user_service   = $this->plugin['wordpress/user/service'];

		$user  = $user_repo->get_user_or_throw_not_found( $user_id );
		$email = $user->user_email;

		if ( $ignico_repo->user_exists_by_email( $email ) ) {

			if ( ! $user_service->is_assigned( $user->ID ) ) {

				$ignico_user = $ignico_repo->get_user_by_email( $email );
				$user_service->assign( $user->ID, $ignico_user->attributes->id );
			}
		} else {
			$ignico_service->add_user( $user_id );
		}
	}
}
