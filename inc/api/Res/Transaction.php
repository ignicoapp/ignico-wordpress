<?php

namespace IgnicoWordPress\Api\Res;

use IgnicoWordPress\Api\AbstractRes;

use IgnicoWordPress\Api\Http\Message\Request;

/**
 * Transaction resource
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/api/Res
 */
class Transaction extends AbstractRes {

	/**
	 * Transaction add endpoint
	 */
	private $endpoint = '/users/wallets/transactions';

	/**
	 * Find transaction path
	 */
	private $path_put_format = '/%d';

	/**
	 * Find transaction path
	 */
	private $path_find_format = '/%d';

	/**
	 * Create transaction
	 *
	 * @param array $params
	 *
	 * @return \stdClass
	 *
	 * @throws \Exception When unsupported accept header.
	 */
	public function create( $params ) {
		$request  = $this->buildRequest( Request::METHOD_POST, $this->endpoint, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * Create transaction
	 *
	 * @param int   $id Transaction ID
	 * @param array $params
	 *
	 * @return \stdClass
	 *
	 * @throws \Exception When unsupported accept header.
	 */
	public function put( $id, $params ) {
		$url = $this->endpoint . sprintf( $this->path_put_format, $id );

		$request  = $this->buildRequest( Request::METHOD_PUT, $url, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * List transactions
	 *
	 * @param array $params
	 *
	 * @return \stdClass
	 *
	 * @throws \Exception When unsupported accept header.
	 */
	public function all( $params = array() ) {
		$request  = $this->buildRequest( Request::METHOD_GET, $this->endpoint, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * Find user
	 *
	 * @param int   $id Transaction ID
	 * @param array $params Request parameters
	 *
	 * @return \stdClass
	 *
	 * @throws \Exception When unsupported accept header.
	 */
	public function find( $id, $params = array() ) {

		$url = $this->endpoint . sprintf( $this->path_find_format, $id );

		$request  = $this->buildRequest( Request::METHOD_GET, $url, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}
}
