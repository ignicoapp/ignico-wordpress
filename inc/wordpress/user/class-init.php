<?php
/**
 * Initial class to manage WordPress functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress
 */

namespace IgnicoWordPress\WordPress\User;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage WordPress functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/wordpress
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
		$this->plugin['wordpress/user/repository'] = new Repository( $this->plugin );
		$this->plugin['wordpress/user/service']    = new Service( $this->plugin );
	}
}
