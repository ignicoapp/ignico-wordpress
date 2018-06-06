<?php

namespace IgnicoWordPress\Api\Http\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Every HTTP client related Exception MUST implement this interface.
 */
interface ClientExceptionInterface extends \Throwable {

	/**
	 * Returns the request.
	 *
	 * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
	 *
	 * @return RequestInterface
	 */
	public function getRequest(): RequestInterface;
}
