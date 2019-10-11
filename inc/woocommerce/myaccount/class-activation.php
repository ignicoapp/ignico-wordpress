<?php
/**
 * Class to provide activation behavior for referral module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */

namespace IgnicoWordPress\WooCommerce\MyAccount;

/**
 * Class to provide activation behavior for referral module
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/myaccount
 */
class Activation {

	/**
	 * Plugin container
	 *
	 * @var Init
	 */
	private $plugin;

	/**
	 * Activation constructor.
	 *
	 * @param Init $plugin IgnicoWordPress/Core plugin container.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function run() {

		$plugin_slug     = str_replace( '_', '-', $this->plugin['id'] );
		$plugin_basename = sprintf( '%s/%s.php', $plugin_slug, $plugin_slug );
		$hook            = sprintf( 'activate_%s', $plugin_basename );

		/**
		 * Simulate register_activation_hook function with our logic
		 */
		$this->plugin['loader']->add_action( $hook, $this, 'activate' );
	}

	/**
	 * Method executed in activation hook
	 *
	 * @return void
	 */
	public function activate() {
		$this->create_pages();
		$this->add_custom_roles();
	}

	private function create_pages() {

		$this->create_rewards_program_page();
		$this->create_consent_page();
		$this->create_coupon_page();
		$this->create_payout_page();
	}

	private function create_rewards_program_page() {

		$id           = 'rewards_program';
		$page_option  = sprintf( 'ignico_%s_page_id', $id );
		$page_name    = _x( 'rewards-program', 'Page slug', 'ignico' );
		$page_title   = _x( 'Rewards program', 'Page title', 'ignico' );
		$page_content = ig_render( __DIR__ . '/partials/tmp/rewards-program.php' );
		$page_parent  = wc_get_page_id( 'myaccount' );
		$page_search  = '[ignico-commission]';

		$this->create_page( esc_sql( $page_name ), $page_option, $page_title, $page_content, $page_parent, $page_search );
	}

	private function create_consent_page() {

		$page_option  = 'ignico_consent_page_id';
		$page_name    = _x( 'welcome-to-rewards-program', 'Page slug', 'ignico' );
		$page_title   = _x( 'Welcome to rewards program', 'Page title', 'ignico' );
		$page_content = ig_render( __DIR__ . '/partials/tmp/consent.php' );
		$page_parent  = ig_get_rewards_program_page_id();
		$page_search  = '[ignico-consent-form]';

		$this->create_page( esc_sql( $page_name ), $page_option, $page_title, $page_content, $page_parent, $page_search );
	}

	private function create_coupon_page() {

		$page_option  = 'ignico_coupon_page_id';
		$page_name    = _x( 'coupon', 'Page slug', 'ignico' );
		$page_title   = _x( 'Coupon', 'Page title', 'ignico' );
		$page_content = ig_render( __DIR__ . '/partials/tmp/coupon.php' );
		$page_parent  = ig_get_rewards_program_page_id();
		$page_search  = '[ignico-coupon-form]';

		$this->create_page( esc_sql( $page_name ), $page_option, $page_title, $page_content, $page_parent, $page_search );
	}

	private function create_payout_page() {

		$page_option  = 'ignico_payout_page_id';
		$page_name    = _x( 'payout', 'Page slug', 'ignico' );
		$page_title   = _x( 'Payout', 'Page title', 'ignico' );
		$page_content = ig_render( __DIR__ . '/partials/tmp/payout.php' );
		$page_parent  = ig_get_rewards_program_page_id();
		$page_search  = '[ignico-payout-form]';

		$this->create_page( esc_sql( $page_name ), $page_option, $page_title, $page_content, $page_parent, $page_search );
	}

	/**
	 * Create Partner role
	 *
	 * Create Partner role which will have the same capabilities as WooCommerce customer
	 *
	 * @return void
	 */
	private function add_custom_roles() {

		// Flush rewrite rules
		flush_rewrite_rules();

		// Get customer role
		$customer = get_role( 'customer' );

		// Add Referred role
		add_role( 'ignico_referred', __( 'Referred', 'ignico' ), $customer->capabilities );
	}



	/**
	 * Create a page and store the ID in an option.
	 *
	 * @param mixed  $slug          Slug for the new page.
	 * @param string $option        Option name to store the page's ID.
	 * @param string $page_title    (default: '') Title for the new page.
	 * @param string $page_content  (default: '') Content for the new page.
	 * @param int    $post_parent   (default: 0) Parent for the new page.
	 *
	 * @return int page ID.
	 */
	public function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0, $page_search ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
				// Valid page is already in place.
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_search}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page.
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_search}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'          => $page_id,
				'post_status' => 'publish',
			);
			wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'page_template'  => 'template-fullwidth.php',
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}
}
