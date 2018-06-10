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

		$this->mock_path_and_url_functions();

		$init = new Init();

		$this->assertEquals( 'ignico', $init['id'] );
		$this->assertEquals( 'Ignico', $init['name'] );
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

		$this->mock_path_and_url_functions();

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_with_plugins();

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

		$this->mock_path_and_url_functions();

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_without_plugins();

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

		$this->mock_path_and_url_functions();

		$init = new Init();

		$this->mock_load_plugin_textdomain();
		$this->mock_get_option_with_plugins();

		$init->load();

		$init['admin'] = Mockery::mock( '\IgnicoWordPress\Admin\Init' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['ignico'] = Mockery::mock( '\IgnicoWordPress\Ignico\Init' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['woocommerce'] = Mockery::mock( '\IgnicoWordPress\WooCommerce\Init' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['edd'] = Mockery::mock( '\IgnicoWordPress\EasyDigitalDownloads\Init' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['notice'] = Mockery::mock( '\IgnicoWordPress\Core\Notice' )
			->shouldReceive( 'run' )
			->once()
			->getMock();

		$init['loader'] = Mockery::mock( '\IgnicoWordPress\Core\Loader' )
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

	/**
	 * Mock get_option which is called during loading dependecies when woocommerce plugin is installed
	 */
	function mock_get_option_with_plugins() {

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
					'woocommerce/woocommerce.php',
					'easy-digital-downloads/easy-digital-downloads.php'
				)
			)
		);
	}

	/**
	 * Mock get_option which is called during loading dependecies when woocommerce plugin is not installed
	 */
	function mock_get_option_without_plugins() {

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
