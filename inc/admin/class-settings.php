<?php
/**
 * Class provided for manage admin settings
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin;

use IgnicoWordPress\Api\Resource\Authorization\AccessToken;

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
	 * Default settings
	 *
	 * @var array
	 */
	private $defaults = array(
		'workspace'     => '',
		'client_id'     => '',
		'client_secret' => '',
		'access_token'  => '',
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
	 * Get settings
	 *
	 * @return array $values Settings
	 */
	public function get_settings() {

		$settings = (array) get_option( $this->plugin['settings_id'] );

		$values = wp_parse_args( $settings, $this->defaults );

		return $values;
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
}
