<?php
/**
 * Container class provided for keeping all plugin variables, classes in one
 * place.
 *
 * @link       http://igni.co/
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 * @author     Ignico <contact@igni.co>
 */

namespace IgnicoWordPress\Core;

/**
 * Container class provided for keeping all plugin variables, classes in one
 * place.
 *
 * Using container allow us to access all plugin dependency in more convenient
 * way. Instead of creating global variables we have one container which we can
 * access from anywhere.
 *
 * To use this class simply extend your main plugin class.
 *
 * @since      0.1.0
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 * @author     Ignico <contact@igni.co>
 */
abstract class Container implements \ArrayAccess {


	/**
	 * Plugin container which store properties, objects, callbacks.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array $container Plugin container.
	 */
	protected $container = array();

	/**
	 * Sets the value at the specified index to new value to the container
	 *
	 * @param mixed $offset Offset at the container.
	 * @param mixed $value Value to be set.
	 */
	public function offsetSet( $offset, $value ) {

		$this->container[ $offset ] = $value;
	}

	/**
	 * Returns whether the requested index exists in the container
	 *
	 * @param mixed $offset Offset at the container.
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {

		return isset( $this->container[ $offset ] );
	}

	/**
	 * Unset the value at the specified index at the container
	 *
	 * @param mixed $offset Offset at the container.
	 */
	public function offsetUnset( $offset ) {

		unset( $this->container[ $offset ] );
	}

	/**
	 * Returns the value at the specified index from the container
	 *
	 * @param mixed $offset Offset at the container.
	 *
	 * @return mixed|null
	 */
	public function offsetGet( $offset ) {

		if ( isset( $this->container[ $offset ] ) ) {

			/**
			 * Container property can be anonymous function
			 */
			if ( ! is_string( $this->container[ $offset ] ) && is_callable( $this->container[ $offset ] ) ) {
				return call_user_func( $this->container[ $offset ] );
			}

			return $this->container[ $offset ];
		}

		return null;

	}
}
