<?php
/**
 * Initial class to manage payout functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

use \IgnicoWordPress\Core\Init as CoreInit;

use IgnicoWordPress\WooCommerce\Payout\Email\Init as EmailInit;

/**
 * Initial class to manage payout functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
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

		$this->plugin['woocommerce/payout/post_type']  = new PostType( $this->plugin );
		$this->plugin['woocommerce/payout/status']     = new Status( $this->plugin );
		$this->plugin['woocommerce/payout/service']    = new Service( $this->plugin );
		$this->plugin['woocommerce/payout/repository'] = new Repository( $this->plugin );
		$this->plugin['woocommerce/payout/email']      = new EmailInit( $this->plugin );
		$this->plugin['woocommerce/payout/shortcode']  = new Shortcode( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['woocommerce/payout/post_type']->run();
		$this->plugin['woocommerce/payout/email']->run();
		$this->plugin['woocommerce/payout/shortcode']->run();
	}
}
