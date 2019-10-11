<?php
/**
 * Class to provide service for payout status
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

/**
 * Class to provide service for payout status
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */
class Status {

	/**
	 * Payout status name
	 */
	const ID = '_ignico_payout_status';

	/**
	 * Pending payout status
	 *
	 * @var int
	 */
	const PENDING = 'pending';

	/**
	 * Completed payout status
	 *
	 * @var int
	 */
	const COMPLETED = 'completed';

	/**
	 * Completed payout status
	 *
	 * @var int
	 */
	const REJECTED = 'rejected';

	/**
	 * Friendly names of data source statuses
	 *
	 * @var array
	 */
	const NAMES = [
		self::PENDING   => 'Pending',
		self::COMPLETED => 'Completed',
		self::REJECTED  => 'Rejected',
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
	public static function get_available() {
		return [
			self::PENDING,
			self::COMPLETED,
			self::REJECTED,
		];
	}

	/**
	 * Get available status names
	 *
	 * @return array
	 */
	public static function get_available_names() {
		return [
			self::PENDING   => __( self::NAMES[ self::PENDING ], 'ignico' ),
			self::COMPLETED => __( self::NAMES[ self::COMPLETED ], 'ignico' ),
			self::REJECTED  => __( self::NAMES[ self::REJECTED ], 'ignico' ),
		];
	}

	/**
	 * Get available statuses
	 *
	 * @param string Status
	 *
	 * @return string
	 *
	 * @throws \Exception When given status do not exist.
	 */
	public static function get_name( $status ) {
		$names = self::NAMES;

		if ( ! isset( $names [ $status ] ) ) {
			throw new \Exception( 'Provided status do not exist.' );
		}

		return $names[ $status ];
	}
}
