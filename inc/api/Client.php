<?php

namespace IgnicoWordPress\Api;

use IgnicoWordPress\Api\Http\ClientInterface;
use IgnicoWordPress\Api\Http\Client as HttpClient;

use IgnicoWordPress\Api\Res\Authorization;
use IgnicoWordPress\Api\Res\Action;

/**
 * The class responsible for authenticate and manage client with oauth2
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api
 */
class Client {

	/**
	 * Base url format
	 *
	 * @var string
	 */
	const BASE_URL_FORMAT = 'https://%s.igni.co/api/v1';

	/**
	 * Base url
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * Http client
	 *
	 * @var ClientInterface
	 */
	private $httpClient;

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
	 * Client constructor
	 *
	 * @param string $workspace     User workspace
	 * @param string $clientId      AIP Client ID
	 * @param string $clientSecret  AIP Client secret
	 */
	public function __construct( $workspace, $clientId, $clientSecret ) {
		$this->httpClient = new HttpClient();

		$this->baseUrl = sprintf( self::BASE_URL_FORMAT, $workspace );

		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
	}

	/**
	 * Get access token
	 */
	public function authorization() {

		return new Authorization(
			$this->httpClient, $this->baseUrl, array(
				'Content-Type: application/x-www-form-urlencoded',
				'Accept: application/json',
			), $this->clientId, $this->clientSecret
		);
	}

	/**
	 * Action resource
	 */
	public function action() {
		return new Action( $this->httpClient, $this->baseUrl, $this->headers() );
	}

	/**
	 * Get ignico specific http headers
	 *
	 * @return array
	 */
	private function headers() {

		$accessToken = $this->authorization()->getAccessToken();

		return array(
			'Content-Type: application/vnd.api+json',
			'Accept: application/vnd.api+json',

			'Authorization: Bearer ' . $accessToken->getAccessToken(),
		);
	}
}
