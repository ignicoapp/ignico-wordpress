<?php
/**
 * Class to provide repository for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

use WP_Query;
use InvalidArgumentException;

/**
 * Class to provide repository for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
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
	 * Find payouts by user ID
	 *
	 * @param int $user_id User ID.
	 *
	 * @return WP_Query
	 *
	 * @throws \InvalidArgumentException If $user_id is not integer.
	 */
	public function find_by_user( $user_id ) {

		if ( ! is_int( $user_id ) ) {
			throw new \InvalidArgumentException( sprintf( 'It looks like $user_id parameter is not an integer but "%s".', gettype( $user_id ) ) );
		}

		$args = [
			'post_type'  => PostType::ID,
			'meta_query' => [
				[
					'key'   => '_ignico_payout_user_id',
					'value' => $user_id,
				],
			],
		];

		return new \WP_Query( $args );
	}

	/**
	 * Find payouts by Ignico transaction ID
	 *
	 * @param int $transaction_id Ignico transaction ID.
	 *
	 * @return \WP_Post|null
	 *
	 * @throws \InvalidArgumentException If $transaction_id is not integer.
	 */
	public function find_by_transaction_id( $transaction_id ) {

		if ( ! is_int( $transaction_id ) ) {
			throw new \InvalidArgumentException( sprintf( 'It looks like $transaction_id parameter is not an integer but "%s".', gettype( $transaction_id ) ) );
		}

		$args = [
			'post_type'     => PostType::ID,
			'post_per_page' => 1,
			'meta_query'    => [
				[
					'key'   => '_ignico_payout_transaction_id',
					'value' => $transaction_id,
				],
			],
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			$posts = $query->get_posts();

			return current( $posts );
		}

		return null;
	}

	/**
	 * Find payouts by status
	 *
	 * @param string $status Payout status.
	 *
	 * @return WP_Query
	 *
	 * @throws InvalidArgumentException If $status is not string.
	 * @throws InvalidArgumentException If $status do not exist.
	 */
	public function find_all_by_status( $status ) {

		if ( ! is_string( $status ) ) {
			throw new InvalidArgumentException( sprintf( 'It looks like $status parameter is not an string but "%s".', gettype( $status ) ) );
		}

		$names = Status::NAMES;

		if ( ! isset( $names [ $status ] ) ) {
			throw new InvalidArgumentException( 'Provided status do not exist.' );
		}

		$args = [
			'post_type'      => PostType::ID,
			'posts_per_page' => -1,
			'meta_query'     => [
				[
					'key'   => Status::ID,
					'value' => $status,
				],
			],
		];

		return new WP_Query( $args );
	}

	/**
	 * Save meta status
	 *
	 * @param int $post_id Post ID
	 *
	 * @return void
	 */
	public function save_meta_status( $post_id, $value ) {

		$old_value = get_post_meta( $post_id, Status::ID, true );

		$this->save_meta( $post_id, Status::ID, $value );

		if ( ! $old_value ) {
			do_action( sprintf( 'ignico_payout_%s', $value ), $post_id );
		}

		if ( $old_value && $old_value !== $value ) {
			do_action( sprintf( 'ignico_payout_%s_to_%s', $old_value, $value ), $post_id );
		}
	}

	/**
	 * Save meta field
	 *
	 * @param int    $post_id Post ID
	 * @param string $key     Meta key
	 * @param mixed  $value   Meta value
	 *
	 * @return void
	 */
	public function save_meta( $post_id, $key, $value = null ) {

		$old_value = get_post_meta( $post_id, $key, false );

		if ( $old_value ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value );
		}

		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}
	}
}
