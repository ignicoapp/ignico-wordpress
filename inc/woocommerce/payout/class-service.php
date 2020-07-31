<?php
/**
 * Class to provide service for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

use IgnicoWordPress\WooCommerce\Payout\PostType;
use IgnicoWordPress\WooCommerce\Payout\Status;

/**
 * Class to provide service for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
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
	 * Create payout
	 *
	 * @param array $data Array of data to create payout
	 *
	 * @return \WP_Post
	 */
	public function add( $data ) {

		// Project string amount to float
		$data['amount'] = floatval( $data['amount'] );

		$user = wp_get_current_user();

		$payout = array(
			'post_title'  => sprintf( __( 'Payout request for %s.', 'ignico' ), $user->user_email ),
			'post_status' => 'publish',
			'post_author' => 0,
			'post_type'   => PostType::ID,
		);

		$payout_id = wp_insert_post( $payout );

		if ( ! $payout_id || $payout_id instanceof \WP_Error ) {
			throw new \Exception( 'Can not create payout. wp_insert_post do not returned post ID.' );
		}

		$transaction = array(
			'user'        => [
				'email' => $user->user_email,
			],
			'type'        => 'sub',
			'amount'      => $data['amount'],
			'realized'    => false,
			'description' => sprintf( __( 'Payout request for WooCommerce user %s based on commission.', 'ignico' ), $user->user_email ),
		);

		// Create Ignico transaction
		$transaction_id = $this->add_ignico_transaction( $transaction );

		update_post_meta( $payout_id, '_ignico_payout_amount', $data['amount'] );
		update_post_meta( $payout_id, '_ignico_payout_user_id', $user->ID );
		update_post_meta( $payout_id, '_ignico_payout_transaction_id', $transaction_id );

		// Need to be last
		$this->plugin['woocommerce/payout/repository']->save_meta_status( $payout_id, Status::PENDING );

		return $payout_id;
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
