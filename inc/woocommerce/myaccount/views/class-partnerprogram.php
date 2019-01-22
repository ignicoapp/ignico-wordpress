<?php
/**
 * Class to manage WooCommerce My Account Partner Program view
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount\Views;

/**
 * Class to manage WooCommerce My Account Partner Program view
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */
class PartnerProgram {

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
	 * Add partner program endpoint
	 *
	 * @param array $items WooCommerce "My Account" ednpoints.
	 *
	 * @return array
	 */
	public function add_partner_program_endpoint( $items ) {

		return $items;
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->loader->add_filter( 'woocommerce_account_menu_items', $this, 'add_partner_program_endpoint' );
	}
}
