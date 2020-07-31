<?php
/**
 * Client for WordPress calls to Ignico service.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */

namespace IgnicoWordPress\Ignico;

use IgnicoWordPress\Core\Init as CoreInit;

use IgnicoWordPress\Admin\Settings;

/**
 * Client for WordPress calls to Ignico service.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */
class Referrer {

	/**
	 * Referrer parameter name
	 */
	const REFERRER_PARAMETER_NAME = '__igrc';

	/**
	 * Referrer cookie name
	 */
	const REFERRER_COOKIE_NAME = 'igrc';

	/**
	 * Constructor
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Referrer
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

		$this->plugin['loader']->add_action( 'init', $this, 'save_cookie' );
	}

	/**
	 * Save cookie with referrer
	 *
	 * Get referrer form GET parameter and save it to cookie.
	 *
	 * @return void
	 */
	public function save_cookie() {

		// When get parameter is not provided do not do anything.
		$referrer = filter_input( INPUT_GET, self::REFERRER_PARAMETER_NAME, FILTER_SANITIZE_STRING );

		if ( ! $referrer || empty( $referrer ) ) {
			return;
		}

		// When cookie is already provided and settings do not allow to
		// overwrite cookie do not do anything.
		$settings = $this->plugin['admin/settings']->get_settings();

		if ( $this->cookie_is_set() && Settings::DO_NOT_ALLOW_OVERWRITE === $settings['cookie_flow'] ) {
			ig_info( 'There is already cookie referrer and we do not allow to overwrite cookie.' );
			return;
		}

		ig_info( 'Save cookie with referrer from get parameter:', [ 'referrer' => $referrer ] );

		// phpcs:disable -- WordPres VIP do not allow to set cookies
		// Save cookie
		setcookie( self::REFERRER_COOKIE_NAME, $referrer, strtotime( '+10 years' ), '/', wp_parse_url( get_bloginfo( 'url' ), PHP_URL_HOST ) );
		// phpcs:enable

		do_action( 'ignico_save_referrer_cookie', self::REFERRER_COOKIE_NAME, $referrer );
	}

	/**
	 * Delete cookie with referrer
	 *
	 * @return void
	 */
	public function delete_cookie() {

		if ( $this->cookie_is_set() ) {
			// phpcs:disable -- WordPres VIP do not allow to use cookies
			unset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] );
			// phpcs:enable

			$url  = get_bloginfo( 'url' );
			$host = wp_parse_url( $url, PHP_URL_HOST );

			// phpcs:disable -- WordPres VIP do not allow to set cookies
			setcookie( self::REFERRER_COOKIE_NAME , '', time() - 3600, '/', $host );
			// phpcs:enable

			do_action( 'ignico_delete_referrer_cookie', self::REFERRER_COOKIE_NAME );
		}
	}

	/**
	 * Get referrer
	 *
	 * @param int $user_id User ID.
	 *
	 * @return mixed
	 *
	 * @throws \Exception When user can not be found for given user id.
	 */
	public function get_referrer( $user_id = null ) {

		ig_info( 'Try to get referrer.', [ 'user_id' => $user_id ] );

		// If user is provided find referer in Ignico by email.
		if ( ! is_null( $user_id ) ) {

			$user = get_user_by( 'id', $user_id );

			if ( ! ( $user instanceof \WP_User ) ) {
				throw new \Exception( 'Can not find WordPress user by provided user ID.' );
			}

			$ig_user = $this->plugin['ignico/repository']->get_user_by_email( $user->email );

			if ( $ig_user && isset( $ig_user->attributes ) && isset( $ig_user->attributes->referralCode ) ) {

				$referrer = $ig_user->attributes->referralCode;

				ig_info(
					'Get referrer directly from Ignico.', [
						'user_id'  => $user_id,
						'referrer' => $referrer,
					]
				);

				// Delete any cookie with referrer.
				$this->delete_cookie();

				return $referrer;
			}
		}

		// If there is cookie assign it to the referred user.
		$referrer = filter_input( INPUT_COOKIE, self::REFERRER_COOKIE_NAME, FILTER_SANITIZE_STRING );

		if ( $referrer ) {
			ig_info(
				'Get referrer from cookie.', [
					'user_id'  => $user_id,
					'referrer' => $referrer,
				]
			);

			return $referrer;
		}

		ig_info( 'There is no referrer in Ignico and cookie.', [ 'user_id' => $user_id ] );

		return null;
	}

	/**
	 * Check if cookie is set
	 *
	 * @return bool
	 */
	private function cookie_is_set() {
		// phpcs:disable -- WordPres VIP do not allow to use cookies
		return isset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] );
		// phpcs:enable
	}
}
