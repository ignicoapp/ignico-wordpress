<?php
/**
 * Class provided to display and handle coupon form
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class provided to display and handle coupon form
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

class Consent_Form {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Form errors.
	 *
	 * @var [] $errors Form errors
	 */
	private $errors;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Init
	 *
	 * @
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Display form
	 */
	public function display_form() {

		return ig_render( __DIR__ . '/partials/consent-form.php' );
	}

	public function is_submitted() {
		return isset( $_POST['submit_consent'] );
	}

	public function is_valid() {
		return filter_input( INPUT_POST, 'ignico_consent', FILTER_VALIDATE_BOOLEAN );
	}

	public function run() {
		add_shortcode( 'ignico-consent-form', [ $this, 'display_form' ] );
	}
}
