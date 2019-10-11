<?php
/**
 * Class provided for manage admin settings
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin;

use IgnicoWordPress\Api\Res\Authorization\AccessToken;

/**
 * Class provided for manage admin settings
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */
class Settings {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Cookie flow type.
	 *
	 * @var string Type of cookie flow.
	 */
	const ALLOW_OVERWRITE = 'allow_overwrite';

	/**
	 * Cookie flow type.
	 *
	 * @var string Type of cookie flow.
	 */
	const DO_NOT_ALLOW_OVERWRITE = 'do_not_allow_overwrite';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	private $defaults = array(
		'workspace'        => '',
		'client_id'        => '',
		'client_secret'    => '',

		'cookie_flow'      => self::ALLOW_OVERWRITE,
		'cookie_removal'   => false,

		'consent_required' => true,
		'consent_text'     => 'I want to join rewards program and I accept all <a href="%s" target="_blank">terms and conditions</a> associated with it.',

		'payout_available' => true,
		'coupon_available' => true,

		'access_token'     => '',

	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Settings
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

		$settings                 = $this->get_settings();
		$settings['access_token'] = get_option( 'ignico_access_token', new \stdClass() );

		$this->plugin['settings'] = $settings;
	}

	/**
	 * Get settings
	 *
	 * @return array $values Settings
	 */
	public function get_settings() {

		$settings = (array) get_option( $this->plugin['settings_id'] );

		return wp_parse_args( $settings, $this->defaults );
	}

	/**
	 * Get consent text
	 *
	 * @return string
	 */
	public function get_consent_text() {
		$settings = $this->get_settings();

		return sprintf( strip_tags( __( $settings['consent_text'], 'ignico' ), '<a>' ), get_permalink( wc_terms_and_conditions_page_id() ) );
	}

	/**
	 * Check if payout withdrawal is available
	 *
	 * @return bool
	 */
	public function is_payout_available() {
		$settings = $this->get_settings();

		return (bool) $settings['payout_available'];
	}

	/**
	 * Check if coupon generation option is available
	 *
	 * @return bool
	 */
	public function is_coupon_available() {
		$settings = $this->get_settings();

		return (bool) $settings['coupon_available'];
	}

	/**
	 * Get available cookie flow settings
	 *
	 * @return array Available cookie flow settings
	 */
	public function get_available_cookie_flow() {

		return array(
			self::ALLOW_OVERWRITE        => 'Allow to overwrite cookie with new referral link',
			self::DO_NOT_ALLOW_OVERWRITE => 'Do not allow to overwrite cookie with new referral link',
		);
	}

	/**
	 * Check if plugin is configured.
	 *
	 * Get settings and check if there is client_id and client_secret.
	 *
	 * @return boolean
	 */
	public function is_configured() {

		$settings = $this->plugin['settings'];

		if (
			! empty( $settings['workspace'] ) &&
			! empty( $settings['client_id'] ) &&
			! empty( $settings['client_secret'] )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Check if plugin is authorized with oauth2.
	 *
	 * @return boolean
	 */
	public function is_authorized() {

		if ( $this->plugin['settings']['access_token'] instanceof AccessToken ) {
			return true;
		}

		return false;
	}
}
