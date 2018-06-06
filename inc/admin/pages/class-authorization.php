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

		$data['workspace']     = trim( filter_var( $data['workspace'], FILTER_SANITIZE_STRING ) );
		$data['client_id']     = trim( filter_var( $data['client_id'], FILTER_SANITIZE_STRING ) );
		$data['client_secret'] = trim( filter_var( $data['client_secret'], FILTER_SANITIZE_STRING ) );

		$data = $this->post_controller( $data );

		return $data;
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
