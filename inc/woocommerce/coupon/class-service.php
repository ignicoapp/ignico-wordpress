<?php
/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

use IgnicoWordPress\WooCommerce\Coupon\PostType;
use IgnicoWordPress\WooCommerce\Coupon\Status;

/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class Service {

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
	 * Create coupon
	 *
	 * @param array $data Array of data to create coupon
	 *
	 * @return \WP_Post
	 */
	public function add( $data ) {

		// Project string amount to float
		$data['amount'] = floatval( $data['amount'] );

		$user = wp_get_current_user();

		$woo_coupon_id = $this->add_coupon( $data );
		$title         = get_the_title( $woo_coupon_id );

		$coupon = array(
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_author' => 0,
			'post_type'   => PostType::ID,
		);

		$coupon_id = wp_insert_post( $coupon );

		if ( ! $coupon_id || $coupon_id instanceof \WP_Error ) {
			throw new \Exception( 'Can not create coupon. wp_insert_post do not returned post ID.' );
		}

		$transaction = array(
			'user'        => [
				'email' => $user->user_email,
			],
			'type'        => 'sub',
			'amount'      => $data['amount'],
			'realized'    => true,
			'description' => sprintf( __( 'Coupon generated for WooCommerce user %s based on commission.', 'ignico' ), $user->user_email ),
		);

		// Create Ignico transaction
		$transaction_id = $this->add_ignico_transaction( $transaction );

		update_post_meta( $coupon_id, Status::ID, Status::NOT_USED );
		update_post_meta( $coupon_id, '_ignico_coupon_id', $woo_coupon_id );
		update_post_meta( $coupon_id, '_ignico_coupon_user_id', $user->ID );
		update_post_meta( $coupon_id, '_ignico_coupon_transaction_id', $transaction_id );

		return $coupon_id;
	}

	/**
	 * Create WooCommerce coupon
	 *
	 * @param array $data Coupon data
	 *
	 * @return int
	 */
	private function add_coupon( $data ) {

		if ( ! isset( $data['amount'] ) ) {
			throw new \Exception( 'Can not create WooCommerce coupon. You have to provide amount of the coupon.' );
		}

		// Project string amount to float
		$data['amount'] = floatval( $data['amount'] );

		if ( ! is_float( $data['amount'] ) || $data['amount'] === 0 ) {
			throw new \Exception( 'Can not create WooCommerce coupon. Amount have to be numeric value.' );
		}

		$user = wp_get_current_user();
		$code = $this->generate_hash( array( $user->ID, time() ) );

		$coupon = array(
			'post_title'   => $code,
			'post_excerpt' => sprintf( __( 'Coupon generated for user %s based on Ignico commission.', 'ignico' ), $user->user_email ),
			'post_status'  => 'publish',
			'post_author'  => 0,
			'post_type'    => 'shop_coupon',
		);

		$coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $coupon_id, 'discount_type', 'fixed_cart' );
		update_post_meta( $coupon_id, 'coupon_amount', $data['amount'] );
		update_post_meta( $coupon_id, 'individual_use', 'no' );
		update_post_meta( $coupon_id, 'usage_limit', 1 );
		update_post_meta( $coupon_id, 'apply_before_tax', 'no' );
		update_post_meta( $coupon_id, 'free_shipping', 'no' );
		update_post_meta( $coupon_id, 'product_ids', '' );
		update_post_meta( $coupon_id, 'exclude_product_ids', '' );
		update_post_meta( $coupon_id, 'usage_limit_per_user', '0' );
		update_post_meta( $coupon_id, 'limit_usage_to_x_items', '0' );
		update_post_meta( $coupon_id, 'usage_count', '0' );
		update_post_meta( $coupon_id, 'date_expires', null );
		update_post_meta( $coupon_id, 'expiry_date', null );
		update_post_meta( $coupon_id, 'product_categories', array() );
		update_post_meta( $coupon_id, 'exclude_product_categories', array() );
		update_post_meta( $coupon_id, 'exclude_sale_items', 'no' );
		update_post_meta( $coupon_id, 'minimum_amount', null );
		update_post_meta( $coupon_id, 'maximum_amount', null );
		update_post_meta( $coupon_id, 'customer_email', array( $user->user_email ) );

		return $coupon_id;
	}

	/**
	 * Generate hash based on arguments
	 *
	 * @param array $settings Array of block settings.
	 *
	 * @return string
	 */
	private function generate_hash( $params ) {

		// @codingStandardsIgnoreStart because of serialization
		return base_convert( sprintf( '%u', crc32( serialize( $params ) ) ), 10, 36 );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Create ignico transaction
	 *
	 * @param array $data Transaction data
	 */
	private function add_ignico_transaction( $data ) {

		$data = [
			'data' => [
				'type'       => 'transaction',
				'attributes' => $data,
			],
		];

		$response = $this->plugin['ignico/client']->transaction()->create( $data );

		if ( ! isset( $response->data ) ) {
			throw new \Exception( 'Can not create ignico transation.' );
		}

		return $response->data->attributes->id;
	}
}
