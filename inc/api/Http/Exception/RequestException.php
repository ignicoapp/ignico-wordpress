<?php

namespace IgnicoWordPress\Api\Http\Exception;

/**
 * Exception for when a request failed.
 *
 * Examples:
 *      - Request is invalid (eg. method is missing)
 *      - Runtime request errors (like the body stream is not seekable)
 */
class RequestException extends ClientException { }
