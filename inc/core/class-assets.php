<?php
/**
 * Class provided for manage admin assets
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */

namespace IgnicoWordPress\Core;

/**
 * Class provided for manage admin assets
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */
class Assets {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Assets
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Register scripts.
	 *
	 * Register all scripts for theme. This method do not attach scripts to html.
	 * Scripts are only registered. If you want to enqueue script from this method
	 * you must to use wp_enqueue_script function.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {

		wp_register_script( $this->plugin['id'], $this->plugin['url'] . '/js/scripts.bundle.js', [], IGNICO_VERSION, true );
	}

	/**
	 * Register production scripts.
	 *
	 * Register all scripts for theme. This method do not attach scripts to html.
	 * Scripts are only registered. If you want to enqueue script from this method
	 * you must to use wp_enqueue_script function.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script
	 *
	 * @since    1.0.0
	 */
	public function register_min_scripts() {

		wp_register_script( $this->plugin['id'], $this->plugin['url'] . '/js/scripts.min.js', [], IGNICO_VERSION, true );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue scripts for theme. In this method scripts previously declared with
	 * wp_register_scripts are actually attached to site html.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin['id'] );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue scripts for theme. In this method scripts previously declared with
	 * wp_register_scripts are actually attached to site html.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script
	 *
	 * @since    1.0.0
	 */
	public function enqueue_min_scripts() {

		$this->enqueue_scripts();
	}

	/**
	 * Register styles.
	 *
	 * Register all styles for theme. This method do not attach styles to html.
	 * Styles are only registered. If you want to enqueue styles from this method
	 * you must to use wp_enqueue_style function.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style
	 */
	public function register_styles() {

		/**
		 * Main plugin style
		 */
		wp_register_style( $this->plugin['id'], $this->plugin['url'] . '/css/style.css', [], IGNICO_VERSION, 'all' );
	}

	/**
	 * Register styles.
	 *
	 * Register all styles for theme. This method do not attach styles to html.
	 * Styles are only registered. If you want to enqueue styles from this method
	 * you must to use wp_enqueue_style function.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style
	 */
	public function register_min_styles() {

		/**
		 * Main plugin style
		 */
		wp_register_style( $this->plugin['id'], $this->plugin['url'] . '/css/style.min.css', [], IGNICO_VERSION, 'all' );
	}

	/**
	 * Enqueue styles.
	 *
	 * Enqueue styles for theme. In this method styles previously declared with
	 * wp_register_style are actually attached to site html.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style
	 */
	public function enqueue_styles() {

		/**
		 * Main plugin style
		 */
		wp_enqueue_style( $this->plugin['id'] );
	}

	/**
	 * Enqueue styles.
	 *
	 * Enqueue styles for theme. In this method styles previously declared with
	 * wp_register_style are actually attached to site html.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style
	 */
	public function enqueue_min_styles() {

		$this->enqueue_styles();
	}

	/**
	 * Register all of the hooks related to the theme assets
	 */
	public function run() {

		if ( defined( 'IGNICO_DEBUG_STYLES' ) && IGNICO_DEBUG_STYLES ) {
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'register_styles' );
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
		} else {

			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'register_min_styles' );
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'enqueue_min_styles' );
		}

		if ( defined( 'IGNICO_DEBUG_SCRIPTS' ) && IGNICO_DEBUG_SCRIPTS ) {
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'register_scripts' );
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
		} else {
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'register_min_scripts' );
			$this->plugin['loader']->add_action( 'wp_enqueue_scripts', $this, 'enqueue_min_scripts' );
		}
	}
}
