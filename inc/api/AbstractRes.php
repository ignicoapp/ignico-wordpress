<?php

namespace IgnicoWordPress\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

use IgnicoWordPress\Api\Http\ClientInterface;
use IgnicoWordPress\Api\Http\Message\Request;
use IgnicoWordPress\Api\Http\Message\Uri;

/**
 * Base module class responsible for providing common method for each module
 *
 * @package IgnicoWordPress\Api
 */
abstract class AbstractRes {

	/**
	 * Form media type
	 *
	 * @var string
	 */
	const MEDIA_TYPE_FORM = 'application/x-www-form-urlencoded';

	/**
	 * Form media type
	 *
	 * @var string
	 */
	const MEDIA_TYPE_JSON = 'application/json';

	/**
	 * Form media type
	 *
	 * @var string
	 */
	const MEDIA_TYPE_JSON_API = 'application/vnd.api+json';

	/**
	 * Http client
	 *
	 * @var ClientInterface
	 */
	private $httpClient;

	/**
	 * Base url
	 *
	 * @var string
	 */
	private $baseUrl;

	/**
	 * Http headers
	 *
	 * @var string
	 */
	private $headers;

	/**
	 * Contractor constructor
	 *
	 * @param ClientInterface $httpClient   Http client
	 * @param string          $baseUrl      Base url
	 * @param array           $headers      Http headers
	 */
	public function __construct( $httpClient, $baseUrl, $headers ) {
		$this->httpClient = $httpClient;

		$this->baseUrl = $baseUrl;
		$this->headers = $headers;
	}

	/**
	 * Get http client
	 *
	 * @return ClientInterface
	 */
	public function getHttpClient() {
		return $this->httpClient;
	}

	/**
	 * Build request for client
	 *
	 * @param string $method   Http method
	 * @param string $endpoint Url endpoint
	 * @param array  $data     Array of data
	 *
	 * @return Request
	 */
	protected function buildRequest( $method, $endpoint, $data ) {
		$url  = $this->formatUrl( $endpoint );
		$body = $this->formatBody( $data );

		return new Request( $method, $url, $this->headers, $body );
	}

	/**
	 * Parse body
	 *
	 * @param ResponseInterface $response
	 *
	 * @return \stdClass
	 */
	protected function parseBody( $response ) {
		return $this->parseResponse( $response->getBody()->getContents() );
	}

	/**
	 * Format body to send, based on content type in header
	 *
	 * @param $data
	 *
	 * @return string
	 *
	 * @throws \Exception When unsupported content type header.
	 */
	private function formatBody( $data ) {
		$contentTypeFormat = 'Content-Type: %s';

		$contentTypeJson    = sprintf( $contentTypeFormat, self::MEDIA_TYPE_JSON );
		$contentTypeJsonApi = sprintf( $contentTypeFormat, self::MEDIA_TYPE_JSON_API );

		if ( in_array( $contentTypeJson, $this->headers ) || in_array( $contentTypeJsonApi, $this->headers ) ) {
			return json_encode( $data );
		}

		$contentTypeForm = sprintf( $contentTypeFormat, self::MEDIA_TYPE_FORM );

		if ( in_array( $contentTypeForm, $this->headers ) ) {
			return http_build_query( $data );
		}

		throw new \Exception( 'Can not format request body to send data. Unsupported Content-Type header in headers.' );
	}

	/**
	 * Parse retrieved body, based on content type in header
	 *
	 * @param string $body
	 *
	 * @return array
	 *
	 * @throws \Exception When unsupported accept header.
	 */
	private function parseResponse( $body ) {
		$acceptFormat = 'Accept: %s';

		$acceptJson    = sprintf( $acceptFormat, self::MEDIA_TYPE_JSON );
		$acceptJsonApi = sprintf( $acceptFormat, self::MEDIA_TYPE_JSON_API );

		if ( in_array( $acceptJson, $this->headers ) || in_array( $acceptJsonApi, $this->headers ) ) {
			return json_decode( $body );
		}

		throw new \Exception( 'Can not format response body to retrieve data. Unsupported Accept header in headers.' );
	}

	/**
	 * Format url based on $inputFormat
	 *
	 * @param string $endpoint
	 *
	 * @return UriInterface
	 */
	private function formatUrl( $endpoint ) {
		return new Uri( $this->baseUrl . $endpoint );
	}
}
