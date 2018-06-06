<?php
/**
 * Class provided for manage settings admin page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin\Pages;

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

		/**
		 * Plugin tabs screens.
		 *
		 * @since    1.0.0
		 *
		 * @var      array $tabs Plugin tab screens.
		 */
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

		if ( ! $this->plugin['admin/settings']->is_authorized() ) {

			$this->plugin['notice']->add_flash_notice( $this->plugin['notification/lock'], Notice::ERROR );

			$url = $this->plugin['admin/pages']->get_admin_plugin_url( 'options', 'authorization' );

			wp_safe_redirect( $url );
			wp_die( 'Redirect', 302 );
		}
	}

	/**
	 * Controller is dispatched when save data on authorization page
	 *
	 * @param array $data Sanitized data.
	 *
	 * @return array $data Sanitized data or old data if something is wrong.
	 */
	public function post_controller( $data ) {

		// Return old data when something is wrong with data.
		$old_data = $this->plugin['admin/settings']->get_settings();

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

		$data = $this->post_controller( $data );

		return $data;
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
