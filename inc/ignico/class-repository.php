<?php
/**
 * Repository class to retrive data from Ignico.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */

namespace IgnicoWordPress\Ignico;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Repository class to retrive data from Ignico.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */
class Repository {

	/**
	 * Plugin container
	 *
	 * @var object $plugin IgnicoWordPress Plugin container.
	 */
	private $plugin;

	/**
	 * Constructor
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Repository
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get Ignico user by id
	 *
	 * @param int $id Ignico user ID.
	 *
	 * @return \stdClass|null
	 */
	public function get_user_by_id( $id ) {

		$transient_key = sprintf( '_ignico_user_by_id_%u', crc32( $id ) );
		$user          = get_transient( $transient_key );

		// If Ignico user exist in transient, do not call Ignico API but return
		// cached version.
		if ( false !== $user ) {
			return $user;
		}

		// Get user from Ignico API.
		$response = $this->plugin['ignico/client']->user()->find( $id );

		// If there is no data in response return null.
		if ( ! $response || ! isset( $response->data ) || ! $response->data ) {
			return null;
		}

		// Get single user from array of resources.
		$user = $response->data;

		// If there is not user or given resource is not type of user return null.
		if ( ! $user || 'user' !== $user->type ) {
			return null;
		}

		// Put the Ignico user in a transient. Expire after 12 hours.
		set_transient( $transient_key, $user, 12 * HOUR_IN_SECONDS );

		return $user;
	}

	/**
	 * Get Ignico user by email address
	 *
	 * @param string $email Ignico user email address.
	 *
	 * @return \stdClass|null
	 */
	public function get_user_by_email( $email ) {

		if ( ! is_string( $email ) ) {
			return null;
		}

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return null;
		}

		$transient_key = sprintf( '_ignico_user_by_email_%u', crc32( $email ) );
		$user          = get_transient( $transient_key );

		// If Ignico user exist in transient, do not call Ignico API but return
		// cached version.
		if ( false !== $user ) {
			return $user;
		}

		// Get user from Ignico API.
		$response = $this->plugin['ignico/client']->user()->all( [ 'filter[email]' => $email ] );

		// If there is no data in response return null.
		if ( ! $response || ! isset( $response->data ) || ! $response->data || count( $response->data ) <= 0 ) {
			return null;
		}

		// Get single user from array of resources.
		$user = current( $response->data );

		// If there is not user or given resource is not type of user return null.
		if ( ! $user || 'user' !== $user->type ) {
			return null;
		}

		// Put the Ignico user in a transient. Expire after 12 hours.
		set_transient( $transient_key, $user, 12 * HOUR_IN_SECONDS );

		return $user;
	}

	/**
	 * Check if user with given email address exists in Ignico
	 *
	 * @param string $email E-mail address.
	 *
	 * @return bool
	 */
	public function user_exists_by_email( $email ) {
		return ! is_null( $this->get_user_by_email( $email ) ) ? true : false;
	}

	/**
	 * Get Ignico transaction by id
	 *
	 * @param int $id Ignico user ID.
	 *
	 * @return \stdClass|null
	 */
	public function get_transaction_by_id( $id ) {

		// Get user from Ignico API.
		$response = $this->plugin['ignico/client']->transaction()->find( $id );

		// If there is no data in response return null.
		if ( ! $response || ! isset( $response->data ) || ! $response->data ) {
			return null;
		}

		// Get single transaction from array of resources.
		$transaction = $response->data;

		// If there is not transaction or given resource is not type of transaction return null.
		if ( ! $transaction || 'transaction' !== $transaction->type ) {
			return null;
		}

		return $transaction;
	}
}
