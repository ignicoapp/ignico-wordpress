<?php
/**
 * Class to provide service for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */

namespace IgnicoWordPress\WooCommerce\Payout;

use IgnicoWordPress\Core\Notice;

/**
 * Class to provide service for payout post type
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/payout
 */
class PostType {

	/**
	 * Payout post type name
	 */
	const ID = 'ignico_payout';

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
	 * Register payout post type
	 *
	 * @return void
	 */
	public function register_post_type() {

		$args = array();

		$labels = array(
			'name'               => __( 'Commission payouts', 'merservice' ),
			'singular_name'      => __( 'Commission payout', 'merservice' ),
			'add_new'            => __( 'Add new', 'merservice' ),
			'add_new_item'       => __( 'Add new', 'merservice' ),
			'edit_item'          => __( 'Edit', 'merservice' ),
			'new_item'           => __( 'New', 'merservice' ),
			'view_item'          => __( 'View', 'merservice' ),
			'search_items'       => __( 'Search', 'merservice' ),
			'not_found'          => __( 'Not found', 'merservice' ),
			'not_found_in_trash' => __( 'Not found payouts in trash', 'merservice' ),
			'parent_item_colon'  => __( 'Parent', 'merservice' ),
			'menu_name'          => __( 'Payout requests', 'merservice' ),

		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
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
	 * Create payout menu for admin area
	 */
	public function create_payout_menu() {
		global $submenu;

		array_unshift( $submenu['ignico'], [ __( 'Payout requests', 'ignico' ), 'manage_options', admin_url( 'edit.php?post_type=ignico_payout' ) ] );
	}

	/**
	 * Add meta boxes to payout view
	 */
	public function add_meta_boxes() {
		add_meta_box( sprintf( '%s_status', self::ID ), __( 'Status', 'ignico' ), array( $this, 'render' ), self::ID, 'normal', 'default' );
	}

	/**
	 * Render meta boxes
	 *
	 * @return void
	 */
	public function render() {

		// Nonce field to validate form request came from current site
		wp_nonce_field( basename( __FILE__ ), 'payout_fields' );

		// Get the location data if it's already been entered
		$status = get_post_meta( get_the_ID(), Status::ID, true );

		echo $this->plugin['admin/form/fields']->select( Status::ID, Status::get_available_names(), $status, array( 'class' => 'widefat' ) );

	}

	/**
	 * Save meta boxes
	 *
	 * @param int      $post_id Post ID
	 * @param \WP_Post $post    WordPress post object
	 *
	 * @return void
	 */
	public function save_metas( $post_id, $post ) {

		// Stop execution when given request is not coming from admin screen
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'payout_fields', FILTER_SANITIZE_STRING ), basename( __FILE__ ) ) ) {
			return;
		}

		// Stop execution when given user do not have permission to do id
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$status = filter_input( INPUT_POST, Status::ID, FILTER_SANITIZE_STRING );

		$transaction_id = get_post_meta( $post_id, '_ignico_payout_transaction_id', true );
		$transaction    = $this->plugin['ignico/repository']->get_transaction_by_id( $transaction_id );

		if ( ( $status === Status::PENDING || $status === Status::COMPLETED ) && $this->transaction_is_cancelled( $transaction ) ) {
			$this->plugin['notice']->add_flash_notice( __( 'You can not update payout status. Ignico transaction status is marked as "Cancelled".', 'ignico' ), Notice::ERROR );
			return;
		}

		if ( ( $status === Status::PENDING || $status === Status::REJECTED ) && $this->transaction_is_realized( $transaction ) ) {
			$this->plugin['notice']->add_flash_notice( __( 'You can not update payout status. Ignico transaction status is marked as "Realized".', 'ignico' ), Notice::ERROR );
			return;
		}

		$this->plugin['woocommerce/payout/repository']->save_meta_status( $post_id, $status );

		$transaction_data = $transaction->attributes;

		if ( $status === Status::REJECTED ) {
			$transaction_data = [ 'cancelled' => true ];
		}

		if ( $status === Status::COMPLETED ) {
			$transaction_data = [ 'realized' => true ];
		}

		if ( $this->can_update_transaction( $transaction, $transaction_data ) ) {
			$this->update_ignico_transaction( $transaction_id, $transaction_data );
		}
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		$this->plugin['loader']->add_action( 'init', $this, 'register_post_type' );
		$this->plugin['loader']->add_action( 'admin_menu', $this, 'create_payout_menu', 20 );
		$this->plugin['loader']->add_action( 'add_meta_boxes', $this, 'add_meta_boxes' );
		$this->plugin['loader']->add_action( 'save_post', $this, 'save_metas', 1, 2 );
	}

	/**
	 * Check if transaction is cancelled
	 *
	 * @param \stdClass $transaction Ignico status
	 *
	 * @return bool
	 */
	private function transaction_is_cancelled( $transaction ) {
		return $transaction->attributes->cancelled === true;
	}

	/**
	 * Check if transaction is realized
	 *
	 * @param \stdClass $transaction Ignico status
	 *
	 * @return bool
	 */
	private function transaction_is_realized( $transaction ) {
		return $transaction->attributes->realized === true;
	}

	/**
	 * Update ignico transaction
	 *
	 * @param int   $transaction_id Transaction ID
	 * @param array $data           Transaction data
	 */
	private function update_ignico_transaction( $transaction_id, $data ) {

		$data = [
			'data' => [
				'type'       => 'transaction',
				'attributes' => $data,
			],
		];

		$response = $this->plugin['ignico/client']->transaction()->put( $transaction_id, $data );

		if ( ! isset( $response->data ) ) {
			throw new \Exception( 'Can not update ignico transation.' );
		}

		return $response->data->attributes->id;
	}

	private function can_update_transaction( $transaction, $transaction_data ) {

		if ( $transaction_data['realized'] === true && $this->transaction_is_realized( $transaction ) ) {
			return false;
		}

		if ( $transaction_data['cancelled'] === true && $this->transaction_is_cancelled( $transaction ) ) {
			return false;
		}

		return true;
	}
}
