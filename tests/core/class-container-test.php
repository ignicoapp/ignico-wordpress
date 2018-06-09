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

use IgnicoWordPress\Core\Init;

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
class Container_Test extends TestCase {

	/**
	 * Test if we can save to container
	 */
	function test_saving_to_container() {

		$this->mock_path_and_url_functions();

		$container = new Init();

		$container['key_int']    = 1;
		$container['key_string'] = 'String';
		$container['key_object'] = new \stdClass();

		$this->assertEquals( 1, $container['key_int'] );
		$this->assertEquals( 'String', $container['key_string'] );
		$this->assertEquals( new \stdClass(), $container['key_object'] );
	}

	/**
	 * Test if we can check if key exist in container
	 */
	function test_existence_in_container() {

		$this->mock_path_and_url_functions();

		$container = new Init();

		$container['key_int']    = 1;
		$container['key_string'] = 'String';
		$container['key_object'] = new \stdClass();

		$this->assertEquals( true, isset( $container['key_int'] ) );
		$this->assertEquals( true, isset( $container['key_string'] ) );
		$this->assertEquals( true, isset( $container['key_object'] ) );

		$this->assertEquals( false, isset( $container['key_fake'] ) );
	}

	/**
	 * Test if we can delete key
	 */
	function test_deleting_from_container() {

		$this->mock_path_and_url_functions();

		$container = new Init();

		$container['key_int']    = 1;
		$container['key_string'] = 'String';
		$container['key_object'] = new \stdClass();

		unset( $container['key_int'] );
		unset( $container['key_string'] );
		unset( $container['key_object'] );

		$this->assertEquals( false, isset( $container['key_int'] ) );
		$this->assertEquals( false, isset( $container['key_string'] ) );
		$this->assertEquals( false, isset( $container['key_object'] ) );
	}

	/**
	 * Mock get_option which is called during loading dependecies when woocommerce plugin is not installed
	 */
	function mock_path_and_url_functions() {

		/**
		 * Mock plugin_dir_path function
		 */
		\WP_Mock::userFunction(
			'plugin_dir_path', array(
				'times' => 1,
				'return' => '/var/www/wordpress-ignico/core/init'
			)
		);

		/**
		 * Mock plugin_dir_path function
		 */
		\WP_Mock::userFunction(
			'plugin_dir_url', array(
				'times' => 1,
				'return' => 'http://ignico-wordpress.local/wp-content/plugins/ignico/'
			)
		);
	}
}
