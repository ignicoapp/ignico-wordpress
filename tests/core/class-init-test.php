<?php
/**
 * Class to test core init class.
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

use Mockery;

use IgnicoWordPress\Core\Init;

/**
 * Class to test core init class.
 *
 * @link       http://Ignico Sp. z o.o..com
 * @since      0.1.0
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/tests/core
 * @author     Ignico Sp. z o.o. <contact@igni.co>
 */
class Init_Test extends TestCase {

	/**
	 * Test if init is settings variables
	 */
	function test_init_is_settings_variables() {

		$init = new Init();

		$this->assertEquals( 'ignico', $init['id'] );
		$this->assertEquals( 'Ignico for WordPress', $init['name'] );
		$this->assertEquals( '0.1.0', $init['version'] );
	}

	/**
	 * Test if init is loading core dependencies
	 *
	 * @runInSeparateProcess
	 *
	 * Isolate in separate process because we are already mocking
	 * load_plugin_textdomain in other tests and we do not want break them.
	 */
	function test_init_is_loading_dependencies() {

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_with_woocommerce();

		$init->load();

		$this->assertInstanceOf( '\IgnicoWordPress\Core\i18n', $init['i18n'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Core\Loader', $init['loader'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Core\Notice', $init['notice'] );

		$this->assertInstanceOf( '\IgnicoWordPress\Admin\Init', $init['admin'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Ignico\Init', $init['ignico'] );

		$this->assertInstanceOf( '\IgnicoWordPress\WooCommerce\Init', $init['woocommerce'] );
	}

	/**
	 * Test if init is not loading WooCommerce module when WooCommerce is not installed.
	 *
	 * @runInSeparateProcess
	 *
	 * Isolate in separate process because we are already mocking
	 * load_plugin_textdomain in other tests and we do not want break them.
	 */
	function test_init_is_loading_dependencies_without_woocommerce() {

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_without_woocommerce();

		$init->load();

		$this->assertInstanceOf( '\IgnicoWordPress\Core\i18n', $init['i18n'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Core\Loader', $init['loader'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Core\Notice', $init['notice'] );

		$this->assertInstanceOf( '\IgnicoWordPress\Admin\Init', $init['admin'] );
		$this->assertInstanceOf( '\IgnicoWordPress\Ignico\Init', $init['ignico'] );

		$this->assertNull( $init['woocommerce'] );
	}

	/**
	 * Test if init is calling run on dependencies
	 *
	 * @runInSeparateProcess
	 *
	 * Isolate in separate process because we are already mocking
	 * load_plugin_textdomain in other tests and we do not want break them.
	 */
	function test_init_is_calling_run_on_dependencies() {

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_with_woocommerce();

		$init->load();

		$init['loader'] = Mockery::mock( '\IgnicoWordPress\Core\Loader' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['notice'] = Mockery::mock( '\IgnicoWordPress\Core\Notice' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['admin'] = Mockery::mock( '\IgnicoWordPress\Core\Admin' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init->run();
	}

	/**
	 * Mock load_plugin_textdomain which is called during set_locale method
	 */
	function mock_load_plugin_textdomain() {

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
	}

	/**
	 * Mock get_option which is called during loading dependecies when woocommerce plugin is installed
	 */
	function mock_get_option_with_woocommerce() {

		/**
		 * Mock load_plugin_textdomain function
		 */
		\WP_Mock::userFunction(
			'get_option', array(
				'times' => 1,
				'args'  => array(
					'active_plugins',
				),
				'return' => array(
					'woocommerce/woocommerce.php'
				)
			)
		);
	}

	/**
	 * Mock get_option which is called during loading dependecies when woocommerce plugin is not installed
	 */
	function mock_get_option_without_woocommerce() {

		/**
		 * Mock load_plugin_textdomain function
		 */
		\WP_Mock::userFunction(
			'get_option', array(
				'times' => 1,
				'args'  => array(
					'active_plugins',
				),
				'return' => array()
			)
		);
	}
}
