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
class Referral {


	/**
	 * Referral parameter name
	 */
	const REFERRAL_PARAMETER_NAME = '__igrc';

	/**
	 * Referral cookie name
	 */
	const REFERRAL_COOKIE_NAME = 'igrc';

	/**
	 * Referral
	 *
	 * @var string
	 */
	private $referral;

	/**
	 * Constructor
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Referral
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Initialize referral from cookie
	 *
	 * @return mixed
	 */
	public function init_referral() {

		$this->referral = filter_input( INPUT_COOKIE, self::REFERRAL_COOKIE_NAME, FILTER_SANITIZE_STRING );
	}

	/**
	 * Get referral
	 *
	 * @return mixed
	 */
	public function get_referral() {

		return $this->referral;
	}

	/**
	 * Save cookie with referral
	 *
	 * Get referral form GET parameter and save it to cookie.
	 *
	 * @return void
	 */
	public function save_cookie() {

		$referral_get = filter_input( INPUT_GET, self::REFERRAL_PARAMETER_NAME, FILTER_SANITIZE_STRING );

		$settings = $this->plugin['admin/settings']->get_settings();

		/**
		 * When get parameter is not provided do nothing.
		 */
		if ( ! $referral_get || empty( $referral_get ) ) {
			return;
		}

		/**
		 * When cookie is already provided and settings do not allow to
		 * overwrite cookie do nothing.
		 */
		if (
			( $this->referral && ! empty( $this->referral ) ) &&
			( Settings::DO_NOT_ALLOW_OVERWRITE === $settings['cookie_flow'] )
		) {
			return;
		}

		$this->referral = $referral_get;

		$url  = get_bloginfo( 'url' );
		$host = wp_parse_url( $url, PHP_URL_HOST );

		// phpcs:disable -- WordPres VIP do not allow to set cookies
		setcookie( self::REFERRAL_COOKIE_NAME, $this->referral, strtotime( '+10 years' ), '/', $host );
		// phpcs:enable
	}

	/**
	 * Delete cookie with referral
	 *
	 * @return void
	 */
	public function delete_cookie() {

		// phpcs:disable -- WordPres VIP do not allow to use cookies
		if ( isset( $_COOKIE[ self::REFERRAL_COOKIE_NAME ] ) ) {
			unset( $_COOKIE[ self::REFERRAL_COOKIE_NAME ] );
		// phpcs:enable

			$url  = get_bloginfo( 'url' );
			$host = wp_parse_url( $url, PHP_URL_HOST );

			// phpcs:disable -- WordPres VIP do not allow to set cookies
			setcookie( self::REFERRAL_COOKIE_NAME , '', time() - 3600, '/', $host );
			// phpcs:enable
		}
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		/**
		 * Execute as soon as possible because some plugins do not use
		 * action hooks and .e.g "init" hook is to late. We are not using any
		 * WordPress function in init_referral method so we are safe.
		 */
		$this->init_referral();

		$this->plugin['loader']->add_action( 'init', $this, 'save_cookie' );
	}
}
