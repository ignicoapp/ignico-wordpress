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
class User extends AbstractRes {

	/**
	 * User resoure endpoint
	 */
	private $endpoint = '/users';

	/**
	 * Find user path
	 */
	private $path_find_format = '/%d';

	/**
	 * Get user enroller path
	 */
	private $path_get_enroller_format = '/%d/enroller';

	/**
	 * List all users
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

	/**
	 * Find user
	 *
	 * @param int   $id Wallet ID
	 * @param array $params Request parameters
	 *
	 * @return array
	 */
	public function find( $id, $params = array() ) {

		$url = $this->endpoint . sprintf( $this->path_find_format, $id );

		$request  = $this->buildRequest( Request::METHOD_GET, $url, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * Get user enroller
	 *
	 * @param int   $id     User id
	 * @param array $params Request parameters
	 *
	 * @return array
	 */
	public function enroller( $id, $params = array() ) {

		$url = $this->endpoint . sprintf( $this->path_get_enroller_format, $id );

		$request  = $this->buildRequest( Request::METHOD_GET, $url, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * Create user
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
}
