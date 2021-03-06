<?php
/**
 * Class provided for manage admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin;

use IgnicoWordPress\Admin\Form\Init as FormInit;
use IgnicoWordPress\Admin\Pages\Init as PagesInit;

/**
 * Class provided for manage admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
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
	 * @param object $plugin IgnicoWordPress Plugin container.
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

		$this->plugin['admin/settings'] = new Settings( $this->plugin );
		$this->plugin['admin/form']     = new FormInit( $this->plugin );
		$this->plugin['admin/pages']    = new PagesInit( $this->plugin );
		$this->plugin['admin/assets']   = new Assets( $this->plugin );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['admin/settings']->run();
		$this->plugin['admin/pages']->run();
		$this->plugin['admin/assets']->run();
	}
}
