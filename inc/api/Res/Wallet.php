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
class Wallet extends AbstractRes {

	/**
	 * Main wallet endpoint
	 */
	private $endpoint = '/users/wallets';

	/**
	 * Find wallet path
	 */
	private $path_find_format = '/%d';

	/**
	 * Get all wallets
	 *
	 * @param array $params Request parameters
	 *
	 * @return array
	 */
	public function all( $params = array() ) {
		$request  = $this->buildRequest( Request::METHOD_GET, $this->endpoint, $params );
		$response = $this->getHttpClient()->sendRequest( $request );

		return $this->parseBody( $response );
	}

	/**
	 * Find wallet
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
}
