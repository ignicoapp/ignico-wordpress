<?php
/**
 * Initial class to manage Easy Digital Downloads functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easy-digital-downloads
 */

namespace IgnicoWordPress\EasyDigitalDownloads;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage Easy Digital Downloads functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/easy-digital-downloads
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

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for plugin admin subpackage.
	 *
	 * @return void
	 */
	private function load_dependencies() {

		$this->plugin['edd/referral'] = new Referral( $this->plugin );
		$this->plugin['edd/ignico']   = new Ignico( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['edd/referral']->run();
		$this->plugin['edd/ignico']->run();
	}
}
