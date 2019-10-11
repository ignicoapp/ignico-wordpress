<?php
/**
 * Ignico class provided to save referral when order is placed in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/checkout
 */

namespace IgnicoWordPress\WooCommerce\Checkout;

use IgnicoWordPress\Core\Init as CoreInit;
use IgnicoWordPress\Admin\Settings;

/**
 * Ignico class provided to save referral when order is placed in WooCommerce
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce
 */
class Consent {

	/**
	 * Plugin container.
	 *
	 * @var CoreInit $theme IgnicoWordPress theme container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin IgnicoWordPress theme container.
	 *
	 * @return Consent
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Show terms and conditions checkbox after
	 *
	 * @return void
	 */
	public function add_consent_checkout() {
		$ignico_repo = $this->plugin['ignico/repository'];
		$settings    = $this->plugin['admin/settings'];

		$checkout       = \WC_Checkout::instance();
		$checkout_email = $checkout->get_value( 'billing_email' );

		if ( ig_consent_required() && ! $ignico_repo->user_exists_by_email( $checkout_email ) ) : ?>
			<div class="woocommerce-terms-and-conditions-wrapper">

				<p class="form-row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
						<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="ignico_consent" <?php checked( isset( $_POST['ignico_consent'] ) ); // WPCS: input var ok, csrf ok. ?> id="ignico_consent" value="1" />
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo $settings->get_consent_text(); ?></span>
					</label>
				</p>

			</div>
		<?php
		endif;
	}

	public function is_valid() {
		return filter_input( INPUT_POST, 'ignico_consent', FILTER_VALIDATE_BOOLEAN );
	}

	function consent_validation( $fields, $errors ) {

		if ( ig_consent_required() && !$this->plugin['woocommerce/registration/consent']->is_valid() ) {
			$errors->add( 'ignico_consent', __( 'Consent acceptance is required.', 'ignico' ) );
		}
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		// 20 to show terms and conditions checkbox after policy privacy information.
		$this->plugin['loader']->add_action( 'woocommerce_checkout_after_terms_and_conditions', $this, 'add_consent_checkout', 40 );
		$this->plugin['loader']->add_action( 'woocommerce_after_checkout_validation', $this, 'consent_validation', 10, 2);


	}
}
