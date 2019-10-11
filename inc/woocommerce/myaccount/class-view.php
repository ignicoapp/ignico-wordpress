<?php
/**
 * Class to manage WooCommerce My Account Referral pogram view
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

/**
 * Class to manage WooCommerce My Account Rewards program view
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */
class View {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		// $this->plugin['loader']->add_action( 'init', $this, 'add_endpoints' );
		// $this->plugin['loader']->add_filter( 'query_vars', $this, 'add_endpoints_query_vars', 0 );
		$this->plugin['loader']->add_filter( 'woocommerce_account_menu_items', $this, 'add_link' );
		// $this->plugin['loader']->add_action( 'woocommerce_account_rewards-program_endpoint', $this, 'render' );
		$this->plugin['loader']->add_filter( 'template_include', $this, 'include_my_account_template' );
		$this->plugin['loader']->add_filter( 'body_class', $this, 'body_class' );
	}

	/**
	 * Add rewards program endpoint
	 *
	 * @return void
	 */
	public function add_endpoints() {

		add_rewrite_endpoint( 'rewards-program', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'rewards-program/dashboard', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'rewards-program/generate-coupon', EP_ROOT | EP_PAGES );
		add_rewrite_endpoint( 'rewards-program/payout-funds', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add rewards program query variable
	 *
	 * @param array $vars Query variables.
	 *
	 * @return array
	 */
	public function add_endpoints_query_vars( $vars ) {

		$vars[] = 'rewards-program';
		$vars[] = 'rewards-program-dashboard';
		$vars[] = 'rewards-program-generate-coupon';
		$vars[] = 'rewards-program-payout-funds';

		return $vars;
	}

	/**
	 * Add rewards program link
	 *
	 * @param array $items WooCommerce "My Account" ednpoints.
	 *
	 * @return array
	 */
	public function add_link( $items ) {

		$endpoint = array(
			'rewards-program' => get_the_title( ig_get_rewards_program_page_id() ),
		);

		// Get index of edit-account to insert our link after this item
		$index = array_search( 'edit-account', array_keys( $items ) ) + 1;

		if ( $index !== false ) {
			$items = array_slice( $items, 0, $index, true ) + $endpoint + array_slice( $items, $index, count( $items ) - $index, true );
		}

		return $items;
	}

	public function include_my_account_template( $template ) {

		if (
			ig_is_rewards_program_page() ||
			ig_is_consent_page() ||
			ig_is_coupon_page() ||
			ig_is_payout_page()
		) {
			return __DIR__ . '/partials/rewards-program-template.php';
		}

		return $template;
	}

	public function body_class( $classes ) {
		$classes = (array) $classes;

		if (
			ig_is_rewards_program_page() ||
			ig_is_consent_page() ||
			ig_is_coupon_page() ||
			ig_is_payout_page()
		) {
			$classes[] = 'woocommerce-account';
			$classes[] = 'woocommerce-page';
		}

		return $classes;
	}

	/**
	 * Set rewards program title.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function set_title( $title ) {

		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars['rewards-program'] );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title
			$title = __( 'Rewards program', 'ignico' );

			// Remove filter to not conflict with other queries
			remove_filter( 'the_title', array( $this, 'set_title' ) );
		}

		return $title;
	}

	/**
	 * Render rewards program page
	 */
	public function render() {

		global $wp_query;

		$query = get_query_var( 'rewards-program' );

		$file = null;

		if ( isset( $wp_query->query_vars['rewards-program'] ) && $query === '' ) {
			throw new \Exception( 'There is not view for rewards program module when query variable is empty.' );
		}

		if ( $query === 'dashboard' ) {
			$file = $this->plugin['path'] . '/inc/woocommerce/myaccount/partials/dashboard.php';
		}

		if ( $query === 'generate-coupon' ) {
			$file = $this->plugin['path'] . '/inc/woocommerce/myaccount/partials/generate-coupon.php';
		}

		if ( $query === 'payout-funds' ) {
			$file = $this->plugin['path'] . '/inc/woocommerce/myaccount/partials/payout-funds.php';
		}

		if ( is_null( $file ) ) {
			throw new \Exception( sprintf( 'You are trying to render view which do not exist "%s".', $query ) );
		}

		if ( ! file_exists( $file ) ) {
			throw new \Exception( sprintf( 'You are trying to render file which do not exist "%s".', $file ) );
		}

		include $file;
	}
}
