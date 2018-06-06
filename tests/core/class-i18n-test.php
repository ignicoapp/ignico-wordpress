<?php
/**
 * Class to test core container class.
 *
 * @link       http://Ignico Sp. z o.o..com
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/tests/core
 * @author     Ignico Sp. z o.o. <contact@igni.co>
 */

namespace IgnicoWordPressTests\Core;

use PHPUnit\Framework\TestCase;

use IgnicoWordPress\Core\I18n;

/**
 * Class to test core container class.
 *
 * @link       http://Ignico Sp. z o.o..com
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/tests/core
 * @author     Ignico Sp. z o.o. <contact@igni.co>
 */
class I18n_Test extends TestCase {

	/**
	 * Test if we can save to container
	 */
	function test_i18n_is_calling_method() {

		/**
		 * Mock load_plugin_textdomain function
		 */
		\WP_Mock::userFunction(
			'load_plugin_textdomain', array(
				'times' => 1,
				'args'  => array(
					'ignico',
					false,
					'ignico/languages',
				),
			)
		);

		$i18n = new I18n(
			array(
				'id' => 'ignico',
			)
		);
		$i18n->load_plugin_textdomain();
	}
}
