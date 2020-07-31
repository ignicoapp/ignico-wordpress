<?php
/**
 * File provided for custom plugin ignico functions
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/ignico
 */

if ( ! function_exists( 'ignico_random_password' ) ) {

	/**
	 * Generate a random string, using a cryptographically secure
	 * pseudorandom number generator (random_int)
	 *
	 * @return string
	 *
	 * @throws Exception When set is to short.
	 */
	function ignico_random_password() {

		$length   = 6;
		$password = '';

		$sets = [ '0123456789', 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ];

		foreach ( $sets as $set ) {
			$max = mb_strlen( $set, '8bit' ) - 1;

			if ( $max < 1 ) {
				throw new Exception( '$set must be at least two characters long' );
			}

			for ( $i = 0; $i < $length; ++$i ) {
				$password .= $set[ random_int( 0, $max ) ];
			}
		}

		return str_shuffle( $password );
	}
}
