<?php
/**
 * Class provided for manage settings admin page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin\Pages;

use IgnicoWordPress\Api\Res\Settings\AccessToken;
use IgnicoWordPress\Api\Res\Exception\SettingsException;

use IgnicoWordPress\Core\Notice;

/**
 * Class provided for manage settings admin page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */
class Settings {

	/**
	 * Plugin container.
	 *
	 * @var \IgnicoWordPress\Core\Init $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param \IgnicoWordPress\Core\Init $plugin IgnicoWordPress Plugin container.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Add settings tab
	 *
	 * @param array $tabs Plugins admin tab.
	 *
	 * @return array $tabs Plugins admin tab.
	 */
	public function add_tab( $tabs ) {

		$tabs['settings'] = array(
			'parent_page'     => 'ignico',
			'tab_title'       => 'Settings',
			'tab_slug'        => 'settings',
			'sanitize'        => array( $this, 'sanitize' ),
			'get_controller'  => array( $this, 'get_controller' ),
			'post_controller' => array( $this, 'post_controller' ),
			'view'            => array( $this, 'view' ),
		);

		return $tabs;
	}

	/**
	 * Controller is dispatched when user visit settings page
	 *
	 * @return void
	 */
	public function get_controller() {

	}

	/**
	 * Controller is dispatched when data is saved on settings page
	 *
	 * @param array $data Sanitized data.
	 *
	 * @return array $data Sanitized data or old data if something is wrong.
	 */
	public function post_controller( $data ) {

		$this->plugin['notice']->add_flash_notice( $this->plugin['notification/settings_updated_successfully'], Notice::SUCCESS );

		return $data;
	}

	/**
	 * Settings option tab controller
	 *
	 * @param array $data Data to sanitzie.
	 *
	 * @return array $data Sanitized data.
	 */
	public function sanitize( $data ) {

		/**
		 * Merge new data with old one to prevent saving empty values.
		 */
		$old_data = $this->plugin['admin/settings']->get_settings();
		$data     = array_merge( $old_data, $data );

		/**
		 * Sanitize and trim all data passed to form.
		 */
		$data['cookie_flow']    = trim( filter_var( $data['cookie_flow'], FILTER_SANITIZE_STRING ) );
		$data['cookie_removal'] = trim( filter_var( $data['cookie_removal'], FILTER_SANITIZE_NUMBER_INT ) );

		$data['consent_required'] = filter_var( $data['consent_required'], FILTER_VALIDATE_BOOLEAN );

		/**
		 * If data is not valid prevent from executing post controller.
		 *
		 * Data is passed as reference can be modified in is_valid method.
		 */
		if ( $this->is_valid( $data ) ) {
			$data = $this->post_controller( $data );
		}

		return $data;
	}

	/**
	 * Check if provided data are valid
	 *
	 * Pass data as reference to modify it without returning value.
	 *
	 * @param array $data Sanitized form data.
	 *
	 * @return boolean
	 */
	public function is_valid( &$data ) {

		$old_data = $this->plugin['admin/settings']->get_settings();

		$valid = true;

		$cookie_flow_exist    = isset( $data['cookie_flow'] ) && ! empty( $data['cookie_flow'] );
		$cookie_removal_exist = isset( $data['cookie_removal'] );

		/**
		 * Validate cookie flow existence
		 */
		if ( ! $cookie_flow_exist ) {

			$valid               = false;
			$data['cookie_flow'] = $old_data['cookie_flow'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( 'Cookie flow', 'ignico' ) ) ), Notice::ERROR );
		}

		/**
		 * Validate cookie removal existence
		 */
		if ( ! $cookie_removal_exist ) {

			$valid                  = false;
			$data['cookie_removal'] = $old_data['cookie_removal'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( 'Cookie removal', 'ignico' ) ) ), Notice::ERROR );
		}

		$available_cookie_flow = $this->plugin['admin/settings']->get_available_cookie_flow();

		/**
		 * Validate cookie flow proper format
		 */
		if ( $cookie_flow_exist && ! array_key_exists( $data['cookie_flow'], $available_cookie_flow ) ) {

			$valid               = false;
			$data['cookie_flow'] = $old_data['cookie_flow'];

			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/form/field/cookie_flow'], Notice::ERROR );
		}

		/**
		 * Validate cookie removal proper format
		 */
		if ( $cookie_removal_exist && ! is_bool( (bool) $data['cookie_removal'] ) ) {

			$valid                  = false;
			$data['cookie_removal'] = $old_data['cookie_removal'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/bool'], esc_html( __( 'Cookie removal', 'ignico' ) ) ), Notice::ERROR );
		}

		if ( ! $this->is_create_user_valid( $data ) ) {
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Check if provided data are valid
	 *
	 * Pass data as reference to modify it without returning value.
	 *
	 * @param array $data Sanitized form data.
	 *
	 * @return boolean
	 */
	public function is_create_user_valid( &$data ) {

		$valid = true;

		if (
			! $this->is_valid_exist( $data, 'consent_required', 'Consent' ) ||
			! $this->is_valid_bool( $data, 'consent_required', 'Consent' ) ) {
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Check if posted setting exist
	 *
	 * @param array  $data  Sanitized form data.
	 * @param string $name  Settings option name.
	 * @param array  $label Settings option label.
	 *
	 * @return bool
	 */
	public function is_valid_exist( $data, $name, $label ) {

		$old_data = $this->plugin['admin/settings']->get_settings();

		if ( ! isset( $data[ $name ] ) ) {
			$data[ $name ] = $old_data[ $name ];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( $label, 'ignico' ) ) ), Notice::ERROR );

			return false;
		}

		return true;
	}

	/**
	 * Check if posted setting is bool
	 *
	 * @param array  $data  Sanitized form data.
	 * @param string $name  Settings option name.
	 * @param array  $label Settings option label.
	 *
	 * @return bool
	 */
	public function is_valid_bool( $data, $name, $label ) {

		$old_data = $this->plugin['admin/settings']->get_settings();

		if ( ! is_bool( $data[ $name ] ) ) {
			$data[ $name ] = $old_data[ $name ];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/bool'], esc_html( __( $label, 'ignico' ) ) ), Notice::ERROR );

			return false;
		}

		return true;
	}

	/**
	 * Settings tab view
	 *
	 * @since    1.0.0
	 */
	public function view() {
		include dirname( __FILE__ ) . '/partials/settings.php';
	}

	/**
	 * Add all hooks and execute related code for settings tab.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_filter( 'ignico_admin_tabs', $this, 'add_tab' );
	}
}
