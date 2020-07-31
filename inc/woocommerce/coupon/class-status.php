<?php
/**
 * Class to provide service for coupon status
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

/**
 * Class to provide service for coupon status
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class Status {

	/**
	 * Coupon status name
	 */
	const ID = '_ignico_coupon_status';

	/**
	 * New payout status
	 *
	 * @var int
	 */
	const USED = 'used';

	/**
	 * Pending payout status
	 *
	 * @var int
	 */
	const NOT_USED = 'not_used';

	/**
	 * Friendly names of data source statuses
	 *
	 * @var array
	 */
	const NAMES = [
		self::USED     => 'Used',
		self::NOT_USED => 'Not used',
	];

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
	 * Get available statuses
	 *
	 * @return array
	 */
	public static function get_available_statuses() {
		return [
			self::USED,
			self::NOT_USED,
		];
	}

	/**
	 * Get available status names
	 *
	 * @return array
	 */
	public static function get_available_names() {
		return [
			self::USED     => self::NAMES[ self::USED ],
			self::NOT_USED => self::NAMES[ self::NOT_USED ],
		];
	}

	/**
	 * Get available statuses
	 *
	 * @param string Status
	 *
	 * @return string
	 */
	public static function get_name( $status ) {
		if ( ! isset( self::NAMES[ $status ] ) ) {
			throw new \Exception( 'Provided status do not exist.' );
		}

		return self::NAMES[ $status ];
	}
}
