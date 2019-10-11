<?php

namespace IgnicoWordPress\Api\Res;

use IgnicoWordPress\Api\AbstractRes;

use IgnicoWordPress\Api\Http\Message\Request;

/**
 * Contractor module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api/Res
 */
class Action extends AbstractRes {

	/**
	 * Action add endpoint
	 */
	private $endpoint = '/actions';

	/**
	 * Create action
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function create( $params ) {
		$request  = $this->buildRequest( Request::METHOD_POST, $this->endpoint, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * List actions
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function all( $params = array() ) {
		$request  = $this->buildRequest( Request::METHOD_GET, $this->endpoint, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}
}
