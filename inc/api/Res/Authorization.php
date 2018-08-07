<?php

namespace IgnicoWordPress\Api\Res;

use IgnicoWordPress\Api\Http\ClientInterface;

use IgnicoWordPress\Api\AbstractRes;
use IgnicoWordPress\Api\Res\Authorization\AccessToken;

use IgnicoWordPress\Api\Res\Exception\AuthorizationException;

/**
 * The class responsible for authorize and manage client with oauth2
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api
 */
class Authorization extends AbstractRes {

	/**
	 * Authorization grand type
	 *
	 * @var string
	 */
	const GRAND_TYPE = 'client_credentials';

	/**
	 * Action add endpoint
	 */
	private $endpoint = '/oauth2/token';

	/**
	 * API client id
	 *
	 * @var string
	 */
	private $clientId;

	/**
	 * API client secret
	 *
	 * @var string
	 */
	private $clientSecret;

	/**
	 * Access token
	 *
	 * @var AccessToken
	 */
	private $accessToken;

	/**
	 * Authorization constructor
	 *
	 * @param ClientInterface $httpClient   Http client
	 * @param string          $baseUrl      Base url
	 * @param array           $headers      Http headers
	 * @param string          $clientId     API client id
	 * @param string          $clientSecret API client secret
	 */
	public function __construct( $httpClient, $baseUrl, $headers, $clientId, $clientSecret ) {
		parent::__construct( $httpClient, $baseUrl, $headers );

		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
	}

	/**
	 * Get access token
	 *
	 * @return AccessToken
	 */
	public function getAccessToken() {
		if ( ! $this->isAuthorized() ) {
			$this->authorize();
		}

		return $this->accessToken;
	}

	/**
	 * Set access token
	 *
	 * @param AccessToken $accessToken Access token
	 *
	 * @return AccessToken
	 *
	 * @throws \InvalidArgumentException When access token is not type of AccessToken
	 */
	public function setAccessToken( $accessToken ) {
		if ( ! ( $accessToken instanceof AccessToken ) ) {
			throw new \InvalidArgumentException( 'Provided access token is not type of "AccessToken"' );
		}

		$this->accessToken = $accessToken;
	}

	/**
	 * Authorize user with credentials and set access token
	 *
	 * @return void
	 *
	 * @throws AuthorizationException When authorization failed and we know why.
	 * @throws \Exception             When authorization failed and we do not know why.
	 */
	private function authorize() {

		try {

			$data = array(
				'grant_type'    => self::GRAND_TYPE,
				'client_id'     => $this->clientId,
				'client_secret' => $this->clientSecret,
			);

			$request  = $this->buildRequest( 'post', $this->endpoint, $data );
			$response = $this->getHttpClient()->sendRequest( $request );

			$body = $this->parseBody( $response );

			if ( $response->getStatusCode() !== 200 ) {

				/**
				 * If response body has message as it should we can provide
				 * useful information to user.
				 */
				if ( isset( $body->message ) && ! empty( $body->message ) ) {
					throw new AuthorizationException( $body->message );
				}

				/**
				 * At this stage we know that response must be \stdClass
				 * from json_decode. We can convert it to json again.
				 */
				throw new AuthorizationException( json_encode( (array) $body ) );
			}
		} catch ( \Exception $e ) {
			throw $e;
		}

		$this->accessToken = new AccessToken( $body );
	}

	/**
	 * Check if client is authorized
	 *
	 * @return boolean
	 */
	private function isAuthorized() {

		return $this->accessToken instanceof AccessToken && ! $this->accessToken->hasExpired();
	}
}
