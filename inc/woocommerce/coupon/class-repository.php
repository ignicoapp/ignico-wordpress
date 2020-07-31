<?php
/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use IgnicoWordPress\WooCommerce\Coupon\PostType;

/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class Repository {

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
	 * Find coupons by user ID
	 *
	 * @param int $user_id User ID.
	 *
	 * @return \WP_Query
	 *
	 * @throws \InvalidArgumentException If $user_id is not integer.
	 */
	public function find_by_user( $user_id ) {

		if ( ! is_int( $user_id ) ) {
			throw new \InvalidArgumentException( sprintf( 'It looks like $user_id parameter is not an integer but "%s".', gettype( $user_id ) ) );
		}

		$args = array(
			'post_type'  => PostType::ID,
			'meta_query' => array(
				array(
					'key'   => '_ignico_coupon_user_id',
					'value' => $user_id,
				),
			),
		);

		return new \WP_Query( $args );
	}

	/**
	 * Find one coupon by coupon code
	 *
	 * @param string $coupon_code Coupon code
	 *
	 * @return \WP_Post|null
	 *
	 * @throws \InvalidArgumentException If $user_id is not integer.
	 */
	public function find_one_by_coupon( $coupon_code ) {

		if ( ! is_string( $coupon_code ) ) {
			throw new \InvalidArgumentException( sprintf( 'It looks like $coupon_code parameter is not an string but "%s".', gettype( $coupon_code ) ) );
		}

		$coupon_id = wc_get_coupon_id_by_code( $coupon_code );

		$args = array(
			'post_type'  => PostType::ID,
			'meta_query' => array(
				array(
					'key'   => '_ignico_coupon_id',
					'value' => $coupon_id,
				),
			),
		);

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			return current( $query->posts );
		}

		return null;
	}
}
