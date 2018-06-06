<?php

namespace IgnicoWordPress\Api\Resource\Exception;

/**
 * Throws when authorization failed and we know why e.g. we have message to show
 * to user.
 */
class AuthorizationException extends \Exception {}
