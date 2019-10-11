<?php
/**
 * Initial class to manage coupon functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use \IgnicoWordPress\Core\Init as CoreInit;

use IgnicoWordPress\WooCommerce\Coupon\PostType;
use IgnicoWordPress\WooCommerce\Coupon\Status;
use IgnicoWordPress\WooCommerce\Coupon\Service;
use IgnicoWordPress\WooCommerce\Coupon\Repository;

/**
 * Initial class to manage coupon functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class Init {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	public function load() {

		$this->plugin['woocommerce/coupon/post_type']  = new PostType( $this->plugin );
		$this->plugin['woocommerce/coupon/status']     = new Status( $this->plugin );
		$this->plugin['woocommerce/coupon/service']    = new Service( $this->plugin );
		$this->plugin['woocommerce/coupon/repository'] = new Repository( $this->plugin );
		$this->plugin['woocommerce/coupon/coupon']     = new Coupon( $this->plugin );
		$this->plugin['woocommerce/coupon/shortcode']  = new Shortcode( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/coupon/post_type']->run();
		$this->plugin['woocommerce/coupon/coupon']->run();
		$this->plugin['woocommerce/coupon/shortcode']->run();
	}
}
