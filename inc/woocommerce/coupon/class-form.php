<?php
/**
 * Class provided to display and handle payout form
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use \IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class provided to display and handle payout form
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

class Form {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Form option.
	 *
	 * @var [] $options Form options
	 */
	private $options;

	/**
	 * Form errors.
	 *
	 * @var [] $errors Form errors
	 */
	private $errors;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin  IgnicoWordPress Plugin container.
	 * @param array    $options Form options
	 *
	 * @return Init
	 *
	 * @throws \Exception When form is created without amount option
	 */
	public function __construct( $plugin, $options ) {
		$this->plugin  = $plugin;
		$this->options = $options;

		if ( ! isset( $this->options['amount'] ) ) {
			throw new \Exception( 'To create coupon form you have to provide value for the "amount" validation.' );
		}
	}

	public function get_option( $name ) {
		return ( isset( $this->options[ $name ] ) ) ? $this->options[ $name ] : null;
	}

	public function is_submitted() {
		return isset( $_POST['submit_payout'] );
	}

	public function is_valid() {
		$amount = filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING );
		$amount = str_replace( ' ', '', $amount );
		$amount = str_replace( ',', '.', $amount );
		$amount = (float) $amount;

		$this->errors['amount'] = [];

		if ( ! $amount || empty( $amount ) ) {
			$this->errors['amount'][] = __( 'This value is required and can not be empty.', 'ignico' );
			return false;
		}

		if ( ! is_numeric( $amount ) ) {
			$this->errors['amount'][] = __( 'This value is not type of number.' );
			return false;
		}

		if ( $amount < 1 ) {
			$this->errors['amount'][] = sprintf( __( 'Value of this field should be %s or more', 'ignico' ), 1 );
			return false;
		}

		if ( $amount > (float) $this->options['amount'] ) {
			$this->errors['amount'][] = sprintf( __( 'Value of this field should be %s or less', 'ignico' ), $this->options['amount'] );
			return false;
		}

		return true;
	}

	public function get_data() {
		$amount = filter_input( INPUT_POST, 'amount', FILTER_SANITIZE_STRING );
		$amount = str_replace( ' ', '', $amount );
		$amount = str_replace( ',', '.', $amount );
		$amount = (float) $amount;

		return [
			'amount' => $amount,
		];
	}

	public function get_errors( $name ) {

		if ( isset( $this->errors[ $name ] ) && ! empty( $this->errors[ $name ] ) ) {
			return $this->errors[ $name ];
		}

		return [];
	}
}
