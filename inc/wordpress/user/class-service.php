<?php
/**
 * Class to provide service for WordPress user
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress/user
 */

namespace IgnicoWordPress\WordPress\User;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class to provide service for WordPress user
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress/user
 */
class Service {

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
	 * Check if user with given ID is has assigned Ignico user
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function assign( $user_id, $ignico_id ) {

		ig_info( 'Assign Ignico user to WordPress user.');

		$user  = get_user_by( 'id', $user_id );

		if( !($user instanceof \WP_User) ) {
			throw new \Exception( 'Can not find WordPress user by provided user ID.' );
		}

		$ignico_id = apply_filters('ignico_before_user_assign', $ignico_id, $user_id );

		$ignico_repo = $this->plugin['ignico/repository'];
		$ignico_user = $ignico_repo->get_user_by_id( $ignico_id );

		$ignico_id            = $ignico_user->attributes->id;
		$ignico_referral_code = $ignico_user->attributes->referralCode;

		$this->assign_ignico_id( $user_id, $ignico_id );
		$this->assign_referral_code( $user_id, $ignico_referral_code );

		$ignico_id = apply_filters('ignico_after_user_assign', $ignico_id, $user_id );
	}

	/**
	 * Assign Ignico user ID
	 *
	 * @param int $user_id User ID
	 * @param int $ignico_id User Ignico ID
	 *
	 * @return void
	 */
	private function assign_ignico_id( $user_id, $ignico_id ) {

		ig_info( 'Assign Ignico user ID to WordPress user.');

		$u_repo = $this->plugin['wordpress/user/repository'];

		$ignico_id = apply_filters('ignico_before_user_assign_ignico_id', $ignico_id, $user_id );
		$u_repo->save_ignico_id( $user_id, $ignico_id );
		$ignico_id = apply_filters('ignico_after_user_assign_ignico_id', $ignico_id, $user_id );
	}

	/**
	 * Assign Ignico referral code
	 *
	 * @param int $user_id User ID
	 * @param int $referral_code User referral code
	 *
	 * @return void
	 */
	private function assign_referral_code( $user_id, $referral_code ) {

		ig_info( 'Assign Ignico referral code to WordPress user.');

		$u_repo = $this->plugin['wordpress/user/repository'];

		$referral_code = apply_filters('ignico_before_user_assign_referral_code', $referral_code, $user_id );
		$u_repo->save_referral_code( $user_id, $referral_code );
		$referral_code = apply_filters('ignico_after_user_assign_referral_code', $referral_code, $user_id );
	}

	/**
	 * Check if user with given ID is has assigned Ignico user
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function is_assigned( $user_id ) {

		$user  = get_user_by( 'id', $user_id );

		if( !($user instanceof \WP_User) ) {
			throw new \Exception( 'Can not find WordPress user by provided user ID.' );
		}

		$is_assigned = true;
		$u_repo = $this->plugin['wordpress/user/repository'];

		$ignico_user_id       = $u_repo->get_ignico_id( $user_id );
		$ignico_referral_code = $u_repo->get_referral_code( $user_id );

		if( !$ignico_user_id || empty( $ignico_user_id ) ) {
			$is_assigned = false;
		}

		if( !$ignico_referral_code || empty( $ignico_referral_code ) ) {
			$is_assigned = false;
		}

		return apply_filters( 'ignico_user_is_assigned', $is_assigned, $user_id );
	}
}
