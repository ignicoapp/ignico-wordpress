<?php
/**
 * Class provided to manage forms in admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin/form
 */

namespace IgnicoWordPress\Admin\Form;

/**
 * Class provided to manage forms in admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin/form
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
	 * Load the required dependencies for plugin form subpackage.
	 *
	 * @return void
	 */
	private function load_dependencies() {

		$this->plugin['admin/form/fields'] = new Fields( $this->plugin );
	}
}
