<?php
/**
 * Class to provide repository for WordPress user
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress/user
 */

namespace IgnicoWordPress\WordPress\User;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class to provide repository for WordPress user
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress/user
 */
class Repository {

	/**
	 * Ignico user ID meta key
	 *
	 * @var string
	 */
	const IGNICO_ID_META_KEY = '_ignico_id';

	/**
	 * Ignico user referral code meta key
	 *
	 * @var string
	 */
	const IGNICO_REFERRAL_CODE_META_KEY = '_ignico_referral_code';

	/**
	 * Ignico user referrer code meta key
	 *
	 * @var string
	 */
	const IGNICO_REFERRER_CODE_META_KEY = '_ignico_referrer_code';

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
	 * Save Ignico user ID
	 *
	 * @param int $user_id
	 * @param int $ignico_id
	 *
	 * @return mixed
	 */
	public function save_ignico_id( $user_id, $ignico_id ) {
		return update_user_meta( $user_id, self::IGNICO_ID_META_KEY, $ignico_id );
	}

	/**
	 * Get user Ignico ID
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_ignico_id( $user_id ) {
		return get_user_meta( $user_id, self::IGNICO_ID_META_KEY, true );
	}

	/**
	 * Save Ignico referral code
	 *
	 * @param int $user_id
	 * @param int $referral_code
	 *
	 * @return mixed
	 */
	public function save_referral_code( $user_id, $referral_code ) {
		return update_user_meta( $user_id, self::IGNICO_REFERRAL_CODE_META_KEY, $referral_code );
	}

	/**
	 * Get user Ignico referral code
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_referral_code( $user_id ) {
		return get_user_meta( $user_id, self::IGNICO_REFERRAL_CODE_META_KEY, true );
	}

	/**
	 * Save Ignico referrer code
	 *
	 * @param int $user_id
	 * @param int $referrer_code
	 *
	 * @return mixed
	 */
	public function save_referrer_code( $user_id, $referrer_code ) {
		return update_user_meta( $user_id, self::IGNICO_REFERRAL_CODE_META_KEY, $referrer_code );
	}

	/**
	 * Get user Ignico ID
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function get_referrer_code( $user_id ) {
		return get_user_meta( $user_id, self::IGNICO_REFERRER_CODE_META_KEY, true );
	}

	/**
	 * Get WordPress user by ID or throw exception if user do not exists
	 *
	 * @param int $user_id User ID
	 *
	 * @return WP_User
	 *
	 * @throws \Exception When user is not found.
	 */
	public function get_user_or_throw_not_found( $user_id ) {

		$user = get_user_by( 'id', $user_id );

		if( !($user instanceof \WP_User) ) {
			throw new \Exception( 'Can not find WordPress user by provided user ID.' );
		}

		return $user;
	}
}
