<?php
/**
 * Initial class to manage ignico functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */

namespace IgnicoWordPress\Ignico;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Initial class to manage ignico functionalities
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */
class Init {

	/**
	 * Plugin container.
	 *
	 * @var CoreInit $plugin Ignico Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin Ignico Plugin container.
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

		require_once __DIR__ . '/functions.php';

		$this->plugin['ignico/client']     = new Client( $this->plugin );
		$this->plugin['ignico/repository'] = new Repository( $this->plugin );
		$this->plugin['ignico/service']    = new Service( $this->plugin );
		$this->plugin['ignico/referrer']   = new Referrer( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['ignico/referrer']->run();
	}
}
