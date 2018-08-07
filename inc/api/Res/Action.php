<?php

namespace IgnicoWordPress\Api\Res;

use IgnicoWordPress\Api\AbstractRes;

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
