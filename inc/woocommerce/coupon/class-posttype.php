<?php
/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */

namespace IgnicoWordPress\WooCommerce\Coupon;

/**
 * Class to provide service for coupon post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon
 */
class PostType {

	/**
	 * Voicher post type name
	 */
	const ID = 'ignico_coupon';

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
	 * Register coupon post type
	 *
	 * @return void
	 */
	public function register_post_type() {

		$args = array();

		$labels = array(
			'name'               => __( 'Coupons', 'ignico' ),
			'singular_name'      => __( 'Coupon', 'ignico' ),
			'add_new'            => __( 'Add new', 'ignico' ),
			'add_new_item'       => __( 'Add new', 'ignico' ),
			'edit_item'          => __( 'Edit', 'ignico' ),
			'new_item'           => __( 'New', 'ignico' ),
			'view_item'          => __( 'View', 'ignico' ),
			'search_items'       => __( 'Search', 'ignico' ),
			'not_found'          => __( 'Not found', 'ignico' ),
			'not_found_in_trash' => __( 'Not found coupons in trash', 'ignico' ),
			'parent_item_colon'  => __( 'Parent', 'ignico' ),
			'menu_name'          => __( 'Coupons', 'ignico' ),

		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'menu_icon'           => null,
			'capability_type'     => 'post',
		);

		register_post_type( self::ID, $args );
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_action( 'init', $this, 'register_post_type' );
	}
}
