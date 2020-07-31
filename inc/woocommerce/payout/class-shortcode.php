<?php
/**
 * Class to provide shortcodes for payout module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

use IgnicoWordPress\Core\Notice;

use IgnicoWordPress\Admin\Settings;

use IgnicoWordPress\WooCommerce\Payout\Form as PayoutForm;

/**
 * Class to provide controller for rewards program page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */
class Shortcode {


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
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {
		add_shortcode( 'ignico-payout-form', [ $this, 'payout_form' ] );
	}

	public function payout_form() {
		if ( ! isset( $this->plugin['woocommerce/myaccount/form/payout'] ) ) {
			$this->plugin['woocommerce/myaccount/form/payout'] = new PayoutForm( $this->plugin, [ 'amount' => 0 ] );
		}

		$form   = $this->plugin['woocommerce/myaccount/form/payout'];
		$amount = $form->get_option( 'amount' );

		if ( $form->is_submitted() ) {
			$amount = filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING );
			$amount = str_replace( ' ', '', $amount );
			$amount = str_replace( ',', '.', $amount );
			$amount = (float) $amount;
		}

		return ig_render(
			__DIR__ . '/partials/shortcodes/payout-form.php', [
				'form'   => $form,
				'amount' => $amount,
			]
		);
	}
}
