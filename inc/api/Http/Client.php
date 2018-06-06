<?php

namespace IgnicoWordPress\Api\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use IgnicoWordPress\Api\Http\Exception\RequestException;
use IgnicoWordPress\Api\Http\Message\Response;

class Client implements ClientInterface {

	/**
	 * Https scheme constant
	 */
	const SCHEME_HTTPS = 'https';

	/**
	 * Sends a PSR-7 request and returns a PSR-7 response.
	 *
	 * Every technically correct HTTP response MUST be returned as is, even if it represents an HTTP
	 * error response or a redirect instruction. The only special case is 1xx responses, which MUST
	 * be assembled in the HTTP client.
	 *
	 * The client MAY do modifications to the Request before sending it. Because PSR-7 objects are
	 * immutable, one cannot assume that the object passed to ClientInterface::sendRequest() will be the same
	 * object that is actually sent. For example the Request object that is returned by an exception MAY
	 * be a different object than the one passed to sendRequest, so comparison by reference (===) is not possible.
	 *
	 * {@link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message-meta.md#why-value-objects}
	 *
	 * @param RequestInterface $request
	 *
	 * @return ResponseInterface
	 *
	 * @throws \IgnicoWordPress\Api\Http\Exception\ClientException If an error happens during processing the request.
	 */
	public function sendRequest( RequestInterface $request ) {
		$responseHeaders = [];
		$reasonPhase     = '';

		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_URL, $request->getUri()->getUri() );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $request->getHeaders() );

		curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );

		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $request->getBody()->getContents() );

		// Parse headers
		curl_setopt(
			$ch, CURLOPT_HEADERFUNCTION, function( $curl, $header ) use ( &$responseHeaders ) {

				// Get header length.
				$len    = strlen( $header );
				$header = explode( ':', $header, 2 );

				// Ignore invalid headers
				if ( count( $header ) < 2 ) {
					return $len;
				}

				// Normalize header name
				$name = strtolower( trim( $header[0] ) );

				if ( ! array_key_exists( $name, $responseHeaders ) ) {
					$responseHeaders[ $name ] = [ trim( $header[1] ) ];
				} else {
					$responseHeaders[ $name ][] = trim( $header[1] );
				}

				return $len;
			}
		);

		// Execute curl request
		$response = curl_exec( $ch );

		if ( $response === false ) {
			throw new RequestException( sprintf( 'Failed to send curl request. Curl error "%s".', curl_error( $ch ) ) );
		}

		$statusCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ( preg_match( '#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $response, $match ) ) {
			$reasonPhase = trim( $match[1] );
		}

		curl_close( $ch );

		return new Response( $responseHeaders, $response, '1.1', $statusCode, $reasonPhase );

	}
}
