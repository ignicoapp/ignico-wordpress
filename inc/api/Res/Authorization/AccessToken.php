<?php

namespace IgnicoWordPress\Api\Res\Authorization;

/**
 * The class responsible for wrapping response with access token.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api/Res
 */
class AccessToken {

	/**
	 * Access token.
	 *
	 * @var string
	 */
	private $accessToken;

	/**
	 * Token type.
	 *
	 * @var string
	 */
	private $tokenType;

	/**
	 * Define after what time access token expires.
	 *
	 * @var int
	 */
	private $expiresIn;

	/**
	 * Define when access token expires.
	 *
	 * @var int
	 */
	private $expires;

	/**
	 * Authorization constructor
	 *
	 * @param \stdClass $response Access token response
	 */
	public function __construct( $response ) {
		if ( isset( $response->access_token ) && ! empty( $response->access_token ) ) {
			$this->accessToken = $response->access_token;
		}

		if ( isset( $response->token_type ) && ! empty( $response->token_type ) ) {
			$this->tokenType = $response->token_type;
		}

		if ( isset( $response->expires_in ) && ! empty( $response->expires_in ) ) {
			$this->expiresIn = $response->expires_in;

			$this->expires = time() + $this->expiresIn;
		}
	}

	/**
	 * Get access token
	 *
	 * @return int
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Get token type
	 *
	 * @return int
	 */
	public function getTokenType() {
		return $this->tokenType;
	}

	/**
	 * Get expires in
	 *
	 * @return int
	 */
	public function getExpiresIn() {
		return $this->expiresIn;
	}

	/**
	 * Get expiration time
	 *
	 * @return int
	 */
	public function getExpires() {
		return $this->expires;
	}

	/**
	 * Checks if access token has expired.
	 */
	public function hasExpired() {

		return $this->expires < time();
	}
}
