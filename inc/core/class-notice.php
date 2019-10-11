<?php
/**
 * Class provided for displaying notices
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */

namespace IgnicoWordPress\Core;

/**
 * Class provided for displaying notices
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */
class Notice {

	/**
	 * Plugin container which store properties, objects, callbacks.
	 *
	 * @var Init $plugin Plugin container.
	 */
	protected $plugin;

	/**
	 * Notice info type.
	 *
	 * @since    1.0.0
	 * @var      int Type of the notice.
	 */
	const INFO = 1;

	/**
	 * Notice success type.
	 *
	 * @since    1.0.0
	 * @var      int Type of the notice.
	 */
	const SUCCESS = 2;

	/**
	 * Notice warning type.
	 *
	 * @since    1.0.0
	 * @var      int Type of the notice.
	 */
	const WARNING = 3;

	/**
	 * Notice error type.
	 *
	 * @since    1.0.0
	 * @var      int Type of the notice.
	 */
	const ERROR = 4;

	/**
	 * Notices
	 *
	 * @since    1.0.0
	 * @var      array Notices
	 */
	private $notices = array();

	/**
	 * Flash notices
	 *
	 * @since    1.0.0
	 * @var      array Flash notices
	 */
	private $flash_notices = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @param Init $plugin Plugin container.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add admin notice.
	 *
	 * @param strnig $message Message of the notice.
	 * @param int    $type Type of the notice. One of class constant.
	 * @param bool   $is_dismissible Define if notice should be dismissible.
	 */
	public function add_notice( $message, $type = self::INFO, $is_dismissible = false ) {

		array_push(
			$this->notices, array(
				'message'        => $message,
				'type'           => $type,
				'is_dismissible' => $is_dismissible,
			)
		);
	}

	/**
	 * Add flash admin notice.
	 *
	 * Flash notice is type of notice that will display after next redirect.
	 * E.g. when you want to save some data and display notice about results after redirect
	 * user to the next page. Usually such a notice is saved to the session. WordPress team
	 * discourage using session so instead we will use user meta.
	 *
	 * @param string $message Message of the notice.
	 * @param int    $type Type of the notice. One of class constant.
	 * @param bool   $is_dismissible Define if notice should be dismissible.
	 */
	public function add_flash_notice( $message, $type = self::INFO, $is_dismissible = false ) {

		array_push(
			$this->notices, array(
				'message'        => $message,
				'type'           => $type,
				'is_dismissible' => $is_dismissible,
			)
		);

		update_user_meta( get_current_user_id(), '_ignico_notices', $this->notices ); // @codingStandardsIgnoreLine We do not care about some VIP rules
	}

	/**
	 * Initialize the flash notices
	 *
	 * Get user meta notices from previous request add to $notices params and
	 * instantly remove notices from user meta to prevent displaying with next request.
	 *
	 * @since    1.0.0
	 */
	public function init_flash_notices() {

		$user_id = get_current_user_id();

		$notices = get_user_meta( $user_id, '_ignico_notices', true ); // @codingStandardsIgnoreLine We do not care about some VIP rules

		if ( $notices && is_array( $notices ) ) {
			$this->notices = array_merge( $this->notices, $notices );
		}
	}

	/**
	 * If any notice exist display it
	 *
	 * @return void
	 */
	public function admin_notice() {

		foreach ( $this->notices as $notice ) {

			$classes   = array( 'notice' );
			$classes[] = $this->get_dismissible_class( $notice['is_dismissible'] );
			$classes[] = $this->get_type_class( $notice['type'] );

			printf( '<div class="%s"><p>%s</p></div>', esc_attr( join( ' ', $classes ) ), esc_html( $notice['message'] ) );
		}

		delete_user_meta( get_current_user_id(), '_ignico_notices' ); // @codingStandardsIgnoreLine We do not care about some VIP rules
	}

	/**
	 * If any notice exist display it
	 *
	 * @return void
	 */
	public function woocommerce_notice() {

		foreach ( $this->notices as $notice ) {

			$classes   = array( 'ignico-rce', 'woocommerce-notice' );
			$classes[] = $this->get_woocommerce_type_class( $notice['type'] );

			printf( '<div class="%s"><p>%s</p></div>', esc_attr( join( ' ', $classes ) ), esc_html( $notice['message'] ) );
		}

		delete_user_meta( get_current_user_id(), '_ignico_notices' ); // @codingStandardsIgnoreLine We do not care about some VIP rules
	}

	/**
	 * Add all hooks related to current subpackage
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_action( 'init', $this, 'init_flash_notices' );
		$this->plugin['loader']->add_action( 'admin_notices', $this, 'admin_notice' );
	}

	/**
	 * Get dismissible class.
	 *
	 * @param bool $is_dismissible Notice dismissible.
	 *
	 * @return string|void
	 */
	private function get_dismissible_class( $is_dismissible ) {

		if ( $is_dismissible ) {
			return 'is-dismissible';
		}
	}

	/**
	 * Get type class.
	 *
	 * @since    1.0.0
	 *
	 * @param int $type Type of the notice.
	 *
	 * @return string
	 */
	private function get_type_class( $type ) {

		$class = '';

		switch ( $type ) {

			case self::ERROR:
				$class = 'notice-error';
				break;

			case self::WARNING:
				$class = 'notice-warning';
				break;

			case self::SUCCESS:
				$class = 'notice-success';
				break;

			case self::INFO:
			default:
				$class = 'notice-info';
				break;
		}

		return $class;
	}

	/**
	 * Get type class.
	 *
	 * @since    1.0.0
	 *
	 * @param int $type Type of the notice.
	 *
	 * @return string
	 */
	private function get_woocommerce_type_class( $type ) {

		switch ( $type ) {

			case self::ERROR:
			case self::WARNING:
				$class = 'woocommerce-error';
				break;

			case self::SUCCESS:
				$class = 'woocommerce-message';
				break;

			case self::INFO:
			default:
				$class = 'woocommerce-info';
				break;
		}

		return $class;
	}
}
