<?php

namespace IgnicoWordPress\Api\Resource;

use IgnicoWordPress\Api\AbstractResource;

/**
 * Contractor module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api/Resource
 */
class Action extends AbstractResource {

	/**
	 * Action add endpoint
	 */
	private $endpoint = '/actions';

	/**
	 * Add action
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function add( $data ) {
		$request  = $this->buildRequest( 'post', $this->endpoint, $data );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}
}
