<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 */

namespace IgnicoWordPress\Core;

use IgnicoWordPress\Admin\Init as AdminInit;
use IgnicoWordPress\Ignico\Init as IgnicoInit;

use IgnicoWordPress\WooCommerce\Init as WooCommerceInit;
use IgnicoWordPress\EasyDigitalDownloads\Init as EasyDigitalDownloadsInit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 */
class Init extends Container {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {

		/**
		 * The unique identifier of this plugin.
		 *
		 * @var      string $id The string used to uniquely identify this theme.
		 */
		$this['id'] = 'ignico';

		/**
		 * The unique name of this plugin.
		 *
		 * @var      string $name The string used to display theme name.
		 */
		$this['name'] = 'Ignico for WordPress';

		/**
		 * The current version of the plugin.
		 *
		 * @var      string    $version    The current version of the plugin.
		 */
		$this['version'] = '0.1.0';

		/**
		 * Settings slug
		 *
		 * @var string $settings_id Settings key used in plugin
		 */
		$this['settings_id'] = 'ignico_settings';

		/**
		 * Notification to setup Ignico
		 *
		 * @var string $notification_setup
		 */
		$this['notification/setup'] = __( 'Ignico for WordPress plugin require configuration to work properly. Please provide your Workspace, Client ID and Client Secret in Authorization tab to authorize plugin.', 'ignico' );

		/**
		 * Notification to setup Ignico before go to settigns
		 *
		 * @var string $notification_lock
		 */
		$this['notification/lock'] = __( 'Before setting up Ignico for WordPress authorize plugin to Ignico service.', 'ignico' );

		/**
		 * Notification informing user that authentication for some reason failed
		 *
		 * @var string $notification_authentication_failed
		 */
		$this['notification/authorization_failed'] = __( 'Authorization failed. Check if you provide correct Workspace, Client ID and Client Secret.', 'ignico' );

	}

	/**
	 * Define the locale for this theme for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {

		$this['i18n'] = new I18n( $this );
		$this['i18n']->load_plugin_textdomain();
	}

	/**
	 * Load the required core dependencies for this theme.
	 *
	 * Include the following files that make up the theme:
	 *
	 * - Loader. Orchestrates the hooks of the theme.
	 */
	private function load_core_dependencies() {

		$this['loader'] = new Loader( $this );
		$this['notice'] = new Notice( $this );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - YourClass. What this class is doing?
	 */
	private function load_dependencies() {

		$this['admin']  = new AdminInit( $this );
		$this['ignico'] = new IgnicoInit( $this );

		/**
		 * Initialize WooCommerce module only when WooCommerce plugin is installed
		 */

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			$this['woocommerce'] = new WooCommerceInit( $this );
		}

		/**
		 * Initialize Easy Digital Downloads module only when Easy Digital Downloads plugin is installed
		 */
		if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			$this['edd'] = new EasyDigitalDownloadsInit( $this );
		}
	}

	/**
	 * Load and init all dependencies
	 *
	 * This method is public because we need it during tests without running
	 * run method.
	 */
	public function load() {

		/**
		 * Load translations before anything. Some plugins like acf do not attach
		 * into any hook so translations does not has a chance to work.
		 */
		$this->set_locale();

		$this->load_core_dependencies();
		$this->load_dependencies();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		$this['admin']->run();
		$this['ignico']->run();

		/**
		 * Initialize WooCommerce module only when WooCommerce plugin is installed
		 */
		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
			$this['woocommerce']->run();
		}

		/**
		 * Initialize Easy Digital Downloads module only when Easy Digital Downloads plugin is installed
		 */
		if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', $active_plugins, true ) ) {
			$this['edd']->run();
		}

		$this['notice']->run();

		/**
		 * Loader must bu executed as the last because it is loading all hooks.
		 */
		$this['loader']->run();
	}
}
