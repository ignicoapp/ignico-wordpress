<?php

namespace IgnicoWordPress\Api\Http\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Exception for when for some reason request fail
 */
abstract class ClientException extends \Exception implements ClientExceptionInterface {

	/**
	 * Returns the request.
	 *
	 * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
	 *
	 * @return RequestInterface
	 */
	public function getRequest(): RequestInterface {}
}
