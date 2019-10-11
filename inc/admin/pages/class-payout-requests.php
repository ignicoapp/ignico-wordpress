<?php
/**
 * Class provided for manage payout requests admin page
 *
 * Payout requests pages are created automatically from 'ignico_payout' post type. This class is provided for modifying
 * default WordPress views.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */

namespace IgnicoWordPress\Admin\Pages;

use IgnicoWordPress\WooCommerce\Payout\PostType;
use IgnicoWordPress\WooCommerce\Payout\Status;

/**
 * Class provided for manage payout requests admin page
 *
 * Payout requests pages are created automatically from 'ignico_payout' post type. This class is provided for modifying
 * default WordPress views.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin
 */
class Payout_Requests {

	/**
	 * Ignico payout status filter key
	 *
	 * @var string
	 */
	const FILTER_KEY_STATUS = 'ignico_payout_status';

	/**
	 * Plugin container.
	 *
	 * @var \IgnicoWordPress\Core\Init $plugin IgnicoWordPress Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param \IgnicoWordPress\Core\Init $plugin IgnicoWordPress Plugin container.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add all hooks and execute related code for settings tab.
	 *
	 * @return void
	 */
	public function run() {
		$this->plugin['loader']->add_filter( sprintf( 'views_edit-%s', PostType::ID ), $this, 'add_filter_with_statuses' );
		$this->plugin['loader']->add_filter( 'pre_get_posts', $this, 'filter_query_by_statuses' );

		$this->plugin['loader']->add_filter( sprintf( 'manage_edit-%s_columns', PostType::ID ), $this, 'add_status_column' );
		$this->plugin['loader']->add_action( sprintf( 'manage_%s_posts_custom_column', PostType::ID ), $this, 'display_status_row', 10, 2 );

		$this->plugin['loader']->add_filter( sprintf( 'manage_edit-%s_columns', PostType::ID ), $this, 'add_amount_column' );
		$this->plugin['loader']->add_action( sprintf( 'manage_%s_posts_custom_column', PostType::ID ), $this, 'display_amount_row', 10, 2 );
	}

	/**
	 * Add ignico payout statuses
	 *
	 * @param array $views WordPress table views.
	 *
	 * @return array
	 *
	 * @throws \Exception When something is wring with current screen.
	 */
	public function add_filter_with_statuses( $views ) {

		$format = '<a href="%s" %s %s>%s</a>';

		$class_current = '';
		$aria_current  = '';
		$get_status    = filter_input( INPUT_GET, self::FILTER_KEY_STATUS );

		if ( null === $get_status ) {
			$class_current = 'class="current"';
			$aria_current  = 'aria-current="page"';
		}

		$views = [
			'all' => sprintf( $format, esc_url( $this->get_base_url() ), $class_current, $aria_current, __( 'All', 'ignico' ) ),
		];

		$statuses = Status::get_available_names();

		foreach ( $statuses as $status_id => $status_name ) {

			$class_current = '';
			$aria_current  = '';
			$get_status    = filter_input( INPUT_GET, self::FILTER_KEY_STATUS );

			if ( $get_status === $status_id ) {
				$class_current = 'class="current"';
				$aria_current  = 'aria-current="page"';
			}

			$views[ $status_id ] = sprintf( $format, esc_url( $this->get_status_url( $status_id ) ), $class_current, $aria_current, $status_name );
		}

		return $views;
	}

	/**
	 * Get status URL
	 *
	 * @param string $status_id Status ID.
	 *
	 * @return string
	 *
	 * @throws \Exception When something is wring with current screen.
	 */
	private function get_status_url( $status_id ) {
		return sprintf( '%s&%s', $this->get_base_url(), http_build_query( [ self::FILTER_KEY_STATUS => $status_id ] ) );
	}

	/**
	 * Get base url
	 *
	 * @return string
	 *
	 * @throws \Exception When something is wring with current screen.
	 */
	private function get_base_url() {

		$screen = get_current_screen();

		if ( ! isset( $screen->parent_file ) ) {
			throw new \Exception( 'Can not generate base url. Current screen to don provide "parent_file" property.' );
		}

		return sprintf( '%s%s', admin_url(), $screen->parent_file );
	}

	/**
	 * Filter query by statuses
	 *
	 * @param \WP_Query $query WordPress query.
	 *
	 * @return void
	 */
	public function filter_query_by_statuses( $query ) {

		if ( ! is_admin() ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! isset( $screen->base ) || ! isset( $screen->post_type ) ) {
			return;
		}

		$base      = $screen->base;
		$post_type = $screen->post_type;

		if ( 'edit' !== $base || PostType::ID !== $post_type ) {
			return;
		}

		$get_status = filter_input( INPUT_GET, self::FILTER_KEY_STATUS );

		if ( null === $get_status ) {
			return;
		}

		if ( $query->is_main_query() ) {

			$meta_query = $query->get( 'meta_query' );

			if ( ! $meta_query || ! is_array( $meta_query ) ) {
				$meta_query = [];
			}

			$meta_query['status'] = [
				'key'   => Status::ID,
				'value' => $get_status,
			];

			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Payouts request admin columns
	 *
	 * @param array $columns Post table header columns.
	 *
	 * @return array
	 */
	public function add_status_column( $columns ) {

		$first_part  = array_slice( $columns, 0, 2, true );
		$second_part = array_slice( $columns, 2, count( $columns ) - 2, true );

		return $first_part + [ 'status' => __( 'Status', 'ignico' ) ] + $second_part;
	}

	/**
	 * Display payouts request status in the row
	 *
	 * @param string $column  Column string identifier.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 *
	 * @throws \Exception When given status do not exist.
	 */
	public function display_status_row( $column, $post_id ) {

		if ( 'status' === $column ) {
			$status = get_post_meta( $post_id, Status::ID, true );
			echo esc_html( Status::get_name( $status ) );
		}
	}

	/**
	 * Payouts request admin columns
	 *
	 * @param array $columns Post table header columns.
	 *
	 * @return array
	 */
	public function add_amount_column( $columns ) {

		$first_part  = array_slice( $columns, 0, 2, true );
		$second_part = array_slice( $columns, 2, count( $columns ) - 2, true );

		return $first_part + [ 'amount' => __( 'Amount', 'ignico' ) ] + $second_part;
	}

	/**
	 * Display payouts request amount in the row
	 *
	 * @param string $column  Column string identifier.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 *
	 * @throws \Exception When given amount do not exist.
	 */
	public function display_amount_row( $column, $post_id ) {

		if ( 'amount' === $column ) {
			echo wp_kses_post( wc_price( get_post_meta( $post_id, '_ignico_payout_amount', true ) ) );
		}
	}
}
