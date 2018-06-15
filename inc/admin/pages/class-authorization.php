<?php
/**
 * Class provided for manage authorization admin page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin\Pages;

use IgnicoWordPress\Api\Resource\Authorization\AccessToken;
use IgnicoWordPress\Api\Resource\Exception\AuthorizationException;

use IgnicoWordPress\Core\Notice;

/**
 * Class provided for manage authorization admin page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */
class Authorization {

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
	 * Add authorization tab
	 *
	 * @param array $tabs Plugins admin tab.
	 *
	 * @return array $tabs Plugins admin tab.
	 */
	public function add_tab( $tabs ) {

		$tabs['authorization'] = array(
			'parent_page'     => 'ignico',
			'tab_title'       => 'Authorization',
			'tab_slug'        => 'authorization',
			'sanitize'        => array( $this, 'sanitize' ),
			'get_controller'  => array( $this, 'get_controller' ),
			'post_controller' => array( $this, 'post_controller' ),
			'view'            => array( $this, 'view' ),
		);

		return $tabs;
	}

	/**
	 * Controller is dispatched when user visit authorization page
	 *
	 * @return void
	 */
	public function get_controller() {

	}

	/**
	 * Controller is dispatched when data is saved on authorization page
	 *
	 * @param array $data Sanitized data.
	 *
	 * @return array $data Sanitized data or old data if something is wrong.
	 */
	public function post_controller( $data ) {

		/**
		 * Overwrite initialized settings to check if data from form are good
		 */
		$settings = $this->plugin['settings'];

		$settings['workspace']     = $data['workspace'];
		$settings['client_id']     = $data['client_id'];
		$settings['client_secret'] = $data['client_secret'];

		$this->plugin['settings'] = $settings;

		try {

			/**
			 * Authorization will use settings like 'workspace', 'client_id',
			 * 'client_secret' from plugin container.
			 */
			$this->plugin['ignico/client']->authorize();

			/**
			 * If authorization would not throw exception we can show successful
			 * notification.
			 */
			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/authorization_successful'], Notice::SUCCESS );

		} catch ( AuthorizationException $e ) {

			$message = $e->getMessage();

			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/authorization_failed'] . ' API response: "' . $message . '"', Notice::ERROR );

			// Return old data when something is wrong with data.
			return $this->plugin['admin/settings']->get_settings();

		} catch ( \Exception $e ) {

			$message = $e->getMessage();

			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/authorization_failed'] . ' Exception: "' . $message . '"', Notice::ERROR );

			// Return old data when something is wrong with data.
			return $this->plugin['admin/settings']->get_settings();
		}

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
		 *
		 * We allow passing workspace with capital letters but we will convert
		 * to lowercase to normalize it.
		 */
		$data['workspace']     = strtolower( trim( filter_var( $data['workspace'], FILTER_SANITIZE_STRING ) ) );
		$data['client_id']     = trim( filter_var( $data['client_id'], FILTER_SANITIZE_STRING ) );
		$data['client_secret'] = trim( filter_var( $data['client_secret'], FILTER_SANITIZE_STRING ) );

		/**
		 * If data is not valid prevent from executing post controller which
		 * will try to authenticate user and show validation messages.
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

		$workspace_exist     = isset( $data['workspace'] ) && ! empty( $data['workspace'] );
		$client_id_exist     = isset( $data['client_id'] ) && ! empty( $data['client_id'] );
		$client_secret_exist = isset( $data['client_secret'] ) && ! empty( $data['client_secret'] );

		/**
		 * Validate workspace existence
		 */
		if ( ! $workspace_exist ) {

			$valid             = false;
			$data['workspace'] = $old_data['workspace'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( 'Workspace', 'ignico' ) ) ), Notice::ERROR );
		}

		/**
		 * Validate workspace proper format
		 *
		 * Workspace is part of igni.co subdomain e.g test.igni.co. Test prefix
		 * must be validated as part of the domain.
		 */
		if ( $workspace_exist && ! preg_match( '/^[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?$/i', $data['workspace'] ) ) {

			$valid             = false;
			$data['workspace'] = $old_data['workspace'];

			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/form/field/workspace'], Notice::ERROR );
		}

		/**
		 * Validate client id existence
		 */
		if ( ! $client_id_exist ) {

			$valid             = false;
			$data['client_id'] = $old_data['client_id'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( 'Client ID', 'ignico' ) ) ), Notice::ERROR );
		}

		/**
		 * Validate client secret existence
		 */
		if ( ! $client_secret_exist ) {

			$valid                 = false;
			$data['client_secret'] = $old_data['client_secret'];

			$this->plugin['notice']->add_flash_notice( sprintf( $this->plugin['notification/form/field/required'], esc_html( __( 'Client secret', 'ignico' ) ) ), Notice::ERROR );
		}

		return $valid;
	}

	/**
	 * Authorization tab view
	 *
	 * @since    1.0.0
	 */
	public function view() {
		include dirname( __FILE__ ) . '/partials/authorization.php';
	}

	/**
	 * Add all hooks and execute related code for authorization tab.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_filter( 'ignico_admin_tabs', $this, 'add_tab' );
	}
}
