<?php

namespace IgnicoWordPress\Api\Http\Message;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

use Fig\Http\Message\RequestMethodInterface;

/**
 * The class responsible for providing normalized request message object for
 * http client.
 *
 * @package IgnicoWordPress\Api\Http\Message
 */
class Request extends Message implements RequestInterface, RequestMethodInterface {

	/**
	 * Request target
	 *
	 * @var string
	 */
	protected $requestTarget;

	/**
	 * Request method
	 *
	 * @var string
	 */
	protected $method = 'GET';

	/**
	 * Request URI
	 *
	 * @var UriInterface
	 */
	protected $uri;

	/**
	 * Request constructor.
	 *
	 * @param string $method          Request method
	 * @param string $uri             Request uri
	 * @param array  $headers         Request headers
	 * @param string $body            Request body
	 * @param string $protocolVersion Request protocol version
	 */
	public function __construct( $method, $uri, $headers, $body, $protocolVersion = '1.1' ) {
		parent::__construct( $headers, $body, $protocolVersion );

		$this->method = $method;
		$this->uri    = $uri;
	}

	/**
	 * Retrieves the message's request target.
	 *
	 * Retrieves the message's request-target either as it will appear (for
	 * clients), as it appeared at request (for servers), or as it was
	 * specified for the instance (see withRequestTarget()).
	 *
	 * In most cases, this will be the origin-form of the composed URI,
	 * unless a value was provided to the concrete implementation (see
	 * withRequestTarget() below).
	 *
	 * If no URI is available, and no request-target has been specifically
	 * provided, this method MUST return the string "/".
	 *
	 * @return string
	 */
	public function getRequestTarget() {
		if ( $this->requestTarget !== null ) {
			return $this->requestTarget;
		}

		$target = $this->uri->getPath();

		if ( $target == '' ) {
			$target = '/';
		}

		if ( $this->uri->getQuery() != '' ) {
			$target .= '?' . $this->uri->getQuery();
		}

		return $target;
	}

	/**
	 * Return an instance with the specific request-target.
	 *
	 * If the request needs a non-origin-form request-target — e.g., for
	 * specifying an absolute-form, authority-form, or asterisk-form —
	 * this method may be used to create an instance with the specified
	 * request-target, verbatim.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request target.
	 *
	 * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
	 *     request-target forms allowed in request messages)
	 * @param mixed $requestTarget
	 * @return static
	 */
	public function withRequestTarget( $requestTarget ) {
		$this->requestTarget = $requestTarget;

		return $this;
	}

	/**
	 * Retrieves the HTTP method of the request.
	 *
	 * @return string Returns the request method.
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Return an instance with the provided HTTP method.
	 *
	 * While HTTP method names are typically all uppercase characters, HTTP
	 * method names are case-sensitive and thus implementations SHOULD NOT
	 * modify the given string.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * changed request method.
	 *
	 * @param string $method Case-sensitive method.
	 * @return static
	 * @throws \InvalidArgumentException for invalid HTTP methods.
	 */
	public function withMethod( $method ) {
		$this->method = $method;

		return $this;
	}

	/**
	 * Retrieves the URI instance.
	 *
	 * This method MUST return a UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @return UriInterface Returns a UriInterface instance
	 *     representing the URI of the request.
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * This method MUST update the Host header of the returned request by
	 * default if the URI contains a host component. If the URI does not
	 * contain a host component, any pre-existing Host header MUST be carried
	 * over to the returned request.
	 *
	 * You can opt-in to preserving the original state of the Host header by
	 * setting `$preserveHost` to `true`. When `$preserveHost` is set to
	 * `true`, this method interacts with the Host header in the following ways:
	 *
	 * - If the Host header is missing or empty, and the new URI contains
	 *   a host component, this method MUST update the Host header in the returned
	 *   request.
	 * - If the Host header is missing or empty, and the new URI does not contain a
	 *   host component, this method MUST NOT update the Host header in the returned
	 *   request.
	 * - If a Host header is present and non-empty, this method MUST NOT update
	 *   the Host header in the returned request.
	 *
	 * This method MUST be implemented in such a way as to retain the
	 * immutability of the message, and MUST return an instance that has the
	 * new UriInterface instance.
	 *
	 * @link http://tools.ietf.org/html/rfc3986#section-4.3
	 * @param UriInterface $uri New request URI to use.
	 * @param bool         $preserveHost Preserve the original state of the Host header.
	 * @return static
	 */
	public function withUri( UriInterface $uri, $preserveHost = false ) {
		if ( $uri === $this->uri ) {
			return $this;
		}

		$new      = clone $this;
		$new->uri = $uri;

		if ( ! $preserveHost ) {
			$new->updateHostFromUri();
		}

		return $new;
	}

	/**
	 * Update host from URI
	 */
	private function updateHostFromUri() {
		$host = $this->uri->getHost();

		if ( $host == '' ) {
			return;
		}

		if ( ( $port = $this->uri->getPort() ) !== null ) {
			$host .= ':' . $port;
		}

		if ( $this->hasHeader( 'Host' ) ) {
			$header = $this->getHeader( 'Host' );
		} else {
			$header                = 'Host';
			$this->headers['host'] = 'Host';
		}

		// Ensure Host is the first header.
		// See: http://tools.ietf.org/html/rfc7230#section-5.4
		$this->headers = [
			$header => [ $host ],
		] + $this->headers;
	}
}
