<?php
/**
 * Client for WordPress calls to Ignico service.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */

namespace IgnicoWordPress\Ignico;

use IgnicoWordPress\Api\Client as ApiClient;
use IgnicoWordPress\Api\Resource\Authorization\AccessToken;

/**
 * Client for WordPress calls to Ignico service.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */
class Client {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * API client.
	 *
	 * @var ApiClient $client Ignico API client
	 */
	private $client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Client
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Map all methods to API client
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Methods arguments.
	 *
	 * @return mixed Result from the callback function
	 *
	 * @throws \Exception When API do not provide desirable method.
	 */
	public function __call( $name, $arguments ) {
		/**
		 * Check if client exist, if not create it.
		 */
		if ( ! ( $this->client instanceof ApiClient ) ) {
			$this->client = $this->client();
		}

		/**
		 * Check if client has a method called from magic method __call.
		 */
		if ( ! method_exists( $this->client, $name ) ) {
			throw new \Exception( 'API client do not provide method "' . $name . '"' );
		}

		/**
		 * Check if client is authorized to access API, if not create access token
		 * and save it to database.
		 */
		if ( ! $this->is_authorized() ) {
			$this->authorize();
		}

		return call_user_func( array( $this->client, $name ), $arguments );
	}

	/**
	 * Authorize client
	 *
	 * @return void
	 */
	public function authorize() {

		/**
		 * Check if client exist, if not create it.
		 */
		if ( ! ( $this->client instanceof ApiClient ) ) {
			$this->client = $this->client();
		}

		$access_token = $this->client->authorization()->getAccessToken();

		update_option( 'ignico_access_token', $access_token );

		$this->client->authorization()->setAccessToken( $access_token );

		$settings                 = $this->plugin['settings'];
		$settings['access_token'] = $access_token;

		$this->plugin['settings'] = $settings;
	}

	/**
	 * Create API client instance
	 *
	 * @return ApiClient
	 *
	 * @throws \Exception When we are trying to get client but plugin is not configured.
	 */
	private function client() {

		$settings = $this->plugin['settings'];

		if (
			empty( $settings['workspace'] ) ||
			empty( $settings['client_id'] ) ||
			empty( $settings['client_secret'] )
		) {
			throw new \Exception( 'You can not use API yet. Workspace, Client ID or Client secret are not configured or empty.' );
		}

		return new ApiClient( $settings['workspace'], $settings['client_id'], $settings['client_secret'] );
	}

	/**
	 * Check if client is authorized
	 *
	 * @return boolean
	 */
	private function is_authorized() {

		$access_token = $this->plugin['settings']['access_token'];

		return $access_token instanceof AccessToken && ! $access_token->hasExpired();
	}
}
