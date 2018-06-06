<?php
/**
 * Class provided for manage admin pages the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin\Pages;

use IgnicoWordPress\Core\Notice;

/**
 * Class provided for manage admin pages the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */
class Init {

	/**
	 * Plugin container.
	 *
	 * @var object Google_Analytics_Popular The ID of this plugin.
	 */
	private $plugin;

	/**
	 * Current admin tab.
	 *
	 * @var array $current_tab Current admin tab
	 */
	private $current_tab;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Initialize pages in WordPress admin area
	 *
	 * @return void
	 */
	public function init_pages() {

		/**
		 * Plugin pages.
		 *
		 * @var array $tabs Plugin tab screens.
		 */
		$this->plugin['admin/pages/pages'] = array(
			'options' => array(
				'parent_slug' => false,
				'page_title'  => 'Ignico',
				'menu_title'  => 'Ignico',
				'capability'  => 'manage_options',
				'menu_slug'   => 'ignico',
				'view'        => array( $this, 'display_tab' ),
			),
		);

		$this->plugin['admin/pages/pages'] = $this->plugin['loader']->apply_filters( 'ignico_admin_pages', $this->plugin['admin/pages/pages'] );
	}

	/**
	 * Initialize tabs in WordPress admin area
	 *
	 * @return void
	 */
	public function init_tabs() {

		/**
		 * Plugin tabs screens.
		 *
		 * @since    1.0.0
		 *
		 * @var      array $tabs Plugin tab screens.
		 */
		$this->plugin['admin/pages/tabs'] = array();
		$this->plugin['admin/pages/tabs'] = $this->plugin['loader']->apply_filters( 'ignico_admin_tabs', $this->plugin['admin/pages/tabs'] );
	}

	/**
	 * Initialize current tab in WordPress admin area
	 *
	 * @return void
	 */
	public function init_current_tab() {

		$this->current_tab = $this->plugin['admin/pages/tabs']['settings'];

		if (
			! $this->plugin['admin/settings']->is_configured() ||
			! $this->plugin['admin/settings']->is_authorized()

		) {
			$this->current_tab = $this->plugin['admin/pages/tabs']['authorization'];
		}
	}

	/**
	 * Check if plugin is already authorized.
	 *
	 * Check if plugin is already authorized. If not add notice and redirect user to authorization tab.
	 *
	 * @return void
	 */
	public function check_if_configured() {

		if (
			! $this->plugin['admin/settings']->is_configured() ||
			! $this->plugin['admin/settings']->is_authorized()

		) {
			$this->plugin['notice']->add_notice( $this->plugin['notification/setup'], Notice::INFO, true );
		}
	}

	/**
	 * Create menu for admin area
	 *
	 * @since    1.0.0
	 */
	public function create_menu() {

		/**
		 * Create new top-level menu
		 */

		foreach ( $this->plugin['admin/pages/pages'] as $page ) {

			if ( isset( $page['parent_slug'] ) && ! empty( $page['parent_slug'] ) ) {
				add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['view'] );
			} else {
				add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['view'] );
			}
		}
	}

	/**
	 * Register plugin settings
	 *
	 * Plugin settings are used on each settings page. Function register_settings is part of WP Settings API and take
	 * three params option_group, option_name and sanitize_callback function which is used for sanitize data. Function
	 * must be hook into admin_init hook. Using this function and settings_fields wp automatically handle saving and
	 * sanitizing data. Settings can be accessed with get_option function with param 'ignico_settings'.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {

		register_setting( $this->plugin['settings_id'], $this->plugin['settings_id'], array( $this, 'dispatch_sanitize' ) );
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $data Array of settings.
	 *
	 * @since    1.0.0
	 *
	 * @return array $data
	 */
	public function dispatch_sanitize( $data ) {

		$query          = array();
		$current_screen = get_current_screen();

		if ( $current_screen && 'options' === $current_screen->base ) {

			$referer = wp_parse_url( filter_input( INPUT_POST, '_wp_http_referer', FILTER_SANITIZE_STRING ) );

			if ( $referer['query'] ) {
				parse_str( $referer['query'], $query );
			}
		}

		foreach ( $this->plugin['admin/pages/pages'] as $page ) {

			if ( $current_screen && 'options' === $current_screen->base ) {

				if ( isset( $query['page'] ) && $query['page'] === $page['menu_slug'] ) {

					if ( isset( $page['sanitize'] ) && is_callable( $page['sanitize'] ) ) {
						$data = call_user_func( $page['sanitize'], $data );
					}
				}
			}
		}

		foreach ( $this->plugin['admin/pages/tabs'] as $tab ) {

			if ( $current_screen && 'options' === $current_screen->base ) {

				if ( isset( $query['tab'] ) && $query['tab'] === $tab['tab_slug'] ) {

					if ( isset( $tab['sanitize'] ) && is_callable( $tab['sanitize'] ) ) {
						$data = call_user_func( $tab['sanitize'], $data );
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Display tab
	 *
	 * Get tab name from get request, find it in tabs array and execute callback to display tab.
	 *
	 * @return void
	 *
	 * @throws \Exception If there is no method for displaying provided tab.
	 */
	public function display_tab() {

		$get_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

		foreach ( $this->plugin['admin/pages/tabs'] as $tab ) {
			if ( $get_tab === $tab['tab_slug'] ) {
				$this->current_tab = $tab;
			}
		}

		if ( ! is_callable( $this->current_tab['view'] ) ) {
			throw new \Exception( 'Missing view for tab "' . $this->current_tab['tab_title'] . '"' );
		}

		call_user_func( $this->current_tab['view'] );
	}

	/**
	 * Get plugin admin url
	 *
	 * @since    1.0.0
	 *
	 * @param string $page Page to obtain.
	 * @param string $tab Tab to obtain.
	 *
	 * @throws \Exception When page or tab can not be found.
	 *
	 * @return string $url Url of the plugin admin page.
	 */
	public function get_admin_plugin_url( $page = 'options', $tab = null ) {

		if ( ! isset( $this->plugin['admin/pages/pages'][ $page ] ) ) {
			throw \Exception( 'There is no such a page' );
		}

		$url = menu_page_url( $this->plugin['admin/pages/pages'][ $page ]['menu_slug'], false );

		if ( $tab ) {
			if ( ! isset( $this->plugin['admin/pages/tabs'][ $tab ] ) ) {
				throw \Exception( 'There is no such a tab' );
			}

			$url .= '&tab=' . $this->plugin['admin/pages/tabs'][ $tab ]['tab_slug'];
		}

		return $url;
	}

	/**
	 * Display tab menu
	 *
	 * @since    1.0.0
	 */
	public function display_tabs() {

		?>

		<?php if ( $this->plugin['admin/pages/tabs'] && ! empty( $this->plugin['admin/pages/tabs'] ) ) : ?>

			<h2 class="nav-tab-wrapper">

				<?php foreach ( $this->plugin['admin/pages/tabs'] as $tab_key => $tab ) : ?>

					<?php

					$classes   = array( 'nav-tab', 'nav-tab-' . $tab['tab_slug'] );
					$classes[] = ( $tab === $this->current_tab ) ? 'nav-tab-active' : '';

					$url = $this->get_admin_plugin_url( 'options', $tab_key );

					printf( '<a class="%s" href="%s" >%s</a>', esc_attr( join( ' ', $classes ) ), esc_url( $url ), esc_html( $tab['tab_title'] ) );

					?>

				<?php endforeach; ?>

			</h2>

		<?php endif; ?>

		<?php
	}

	/**
	 * Dispatch admin controllers
	 *
	 * @since    1.0.0
	 */
	public function dispatch_controllers() {

		$get_tab  = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
		$get_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		foreach ( $this->plugin['admin/pages/pages'] as $page ) {

			if ( $get_page === $page['menu_slug'] ) {

				if ( isset( $page['get_controller'] ) && is_callable( $page['get_controller'] ) ) {
					call_user_func( $page['get_controller'] );
				}
			}
		}

		foreach ( $this->plugin['admin/pages/tabs'] as $tab ) {

			if ( $get_tab === $tab['tab_slug'] ) {

				if ( isset( $tab['get_controller'] ) && is_callable( $tab['get_controller'] ) ) {
					call_user_func( $tab['get_controller'] );
				}
			}
		}
	}

	/**
	 * Load the required dependencies for pages subpackage.
	 *
	 * @return void
	 */
	private function load_dependencies() {

		$this->plugin['admin/pages/settings']      = new Settings( $this->plugin );
		$this->plugin['admin/pages/authorization'] = new Authorization( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code for pages subpackage.
	 *
	 * @return void
	 */
	public function run() {

		$this->load_dependencies();

		$this->plugin['admin/pages/settings']->run();
		$this->plugin['admin/pages/authorization']->run();

		$this->plugin['loader']->add_action( 'admin_menu', $this, 'init_pages' );
		$this->plugin['loader']->add_action( 'admin_menu', $this, 'init_tabs' );
		$this->plugin['loader']->add_action( 'admin_menu', $this, 'init_current_tab' );
		$this->plugin['loader']->add_action( 'admin_menu', $this, 'check_if_configured' );
		$this->plugin['loader']->add_action( 'admin_menu', $this, 'create_menu' );

		$this->plugin['loader']->add_action( 'admin_init', $this, 'register_settings' );

		$this->plugin['loader']->add_action( 'current_screen', $this, 'dispatch_controllers' );
	}
}
