<?php
/**
 * Class to provide shortcodes for coupon module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use IgnicoWordPress\WooCommerce\Coupon\Form as CouponForm;

/**
 * Class to provide controller for rewards program page
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
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
		add_shortcode( 'ignico-coupon-form', [ $this, 'coupon_form' ] );
	}

	public function coupon_form() {
		if ( ! isset( $this->plugin['woocommerce/myaccount/form/coupon'] ) ) {
			$this->plugin['woocommerce/myaccount/form/coupon'] = new CouponForm( $this->plugin, [ 'amount' => 0 ] );
		}

		$form   = $this->plugin['woocommerce/myaccount/form/coupon'];
		$amount = $form->get_option( 'amount' );

		if ( $form->is_submitted() ) {
			$amount = filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING );
			$amount = str_replace( ' ', '', $amount );
			$amount = str_replace( ',', '.', $amount );
			$amount = (float) $amount;
		}

		return ig_render(
			__DIR__ . '/partials/shortcodes/coupon-form.php', [
				'form'   => $form,
				'amount' => $amount,
			]
		);
	}
}
