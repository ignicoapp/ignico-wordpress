<?php
/**
 * Class provided for mock WordPress MO class
 *
 * @link       http://Ignico Sp. z o.o..com
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/tests/sample
 * @author     Ignico Sp. z o.o. <contact@igni.co>
 */

/**
 * Class provided for mock WordPress MO class
 *
 * @link       http://Ignico Sp. z o.o..com
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/tests/sample
 * @author     Ignico Sp. z o.o. <contact@igni.co>
 */
class MO {

	/**
	 * All translations
	 *
	 * @var array
	 */
	public $entries;

	/**
	 * Method for adding translations
	 *
	 * @param string $entry Translation.
	 */
	public function add_entry( $entry ) {

		$this->entries[ $entry ] = $entry;
	}
}
