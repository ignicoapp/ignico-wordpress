<?php
/**
 * Service class to save informations to Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */

namespace IgnicoWordPress\Ignico;

use IgnicoWordPress\Core\Init as CoreInit;

use IgnicoWordPress\Admin\Settings;

/**
 * Service class to save informations to Ignico
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/ignico
 */
class Service {

	/**
	 * Plugin container.
	 *
	 * @var CoreInit $plugin Ignico Plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin Ignico Plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add Ignico user when user register.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return void
	 *
	 * @throws \Exception When there is a problem with adding user to Ignico.
	 */
	public function add_user( $user_id ) {

		ig_info( 'Add Ignico user based on WordPress user.' );

		$user_service = $this->plugin['wordpress/user/service'];

		$user_repo = $this->plugin['wordpress/user/repository'];
		$user      = $user_repo->get_user_or_throw_not_found( $user_id );

		try {
			$data = $this->get_add_user_data( $user->ID );

			ig_info( 'Add user to Ignico.', [ 'data' => $data ] );

			$data     = apply_filters( 'ignico_before_user_created', $data, $user->ID );
			$response = $this->plugin['ignico/client']->user()->create( $data );
			$response = apply_filters( 'ignico_after_user_created', $response, $user->ID );

			if ( $response ) {
				ig_info( 'User has been successfully added to Ignico.', [ 'response' => $response ] );
				$user_service->assign( $user->ID, $response->data->attributes->id );
			} else {
				ig_info( 'User could not be added to Ignico.', [ 'response' => $response ] );
			}
		} catch ( \Exception $e ) {
			ig_error( 'There was problem with adding user to Ignico.', [ 'error' => $e ] );
			throw $e;
		}
	}

	/**
	 * Prepare user data for Ignico API call
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array
	 *
	 * @throws \Exception When user is not found.
	 */
	private function get_add_user_data( $user_id ) {

		ig_info( 'Get "add user" data based on WordPress user.' );

		$user_repo = $this->plugin['wordpress/user/repository'];
		$user      = $user_repo->get_user_or_throw_not_found( $user_id );

		$data = [
			'data' => [
				'type'       => 'user',
				'attributes' => [
					'email'    => $user->user_email,
					'country'  => 'PL', // We do not know user country during registration.
					'active'   => 1,
					'password' => ignico_random_password(),
				],
			],
		];

		$referrer = $this->get_add_user_referrer_data( $user_id );

		if ( ! is_null( $referrer ) ) {
			$data['data']['attributes']['referrer']['referralCode'] = $referrer;
		}

		return $data;
	}

	/**
	 * Prepare user referrer data for Ignico API call
	 *
	 * @param int user_id User ID
	 *
	 * @return string|null
	 */
	private function get_add_user_referrer_data( $user_id ) {

		ig_info( 'Get referrer data based on WordPress user.' );

		$user_repo = $this->plugin['wordpress/user/repository'];
		$user      = $user_repo->get_user_or_throw_not_found( $user_id );
		$referrer  = $this->plugin['ignico/referrer']->get_referrer( $user->ID );

		if ( $referrer && ! empty( $referrer ) ) {
			if ( $this->is_valid_referrer( $user->user_email, $referrer ) ) {
				ig_info(
					'Provided referrer is valid.', [
						'referrer' => $referrer,
						'user'     => $user_id,
					]
				);
				return $referrer;
			} else {
				ig_info(
					'Provided referrer is not valid.', [
						'referrer' => $referrer,
						'user'     => $user_id,
					]
				);
				return null;
			}
		} else {
			ig_info( 'There is no referrer.' );
			return null;
		}

		return null;
	}

	/**
	 * Create Ignico based on WooCommerce order
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function add_user_based_on_order( $order_id ) {

		ig_info( 'Add Ignico user based on WooCommerce order.' );

		$user_service = $this->plugin['wordpress/user/service'];
		$order_repo   = $this->plugin['woocommerce/order/repository'];

		$order_repo->get_order_or_throw_not_found( $order_id );

		try {
			$data = $this->get_add_user_data_from_order( $order_id );

			ig_info( 'Add user to Ignico.', [ 'data' => $data ] );

			$data     = apply_filters( 'ignico_before_user_created', $data, $order_id );
			$response = $this->plugin['ignico/client']->user()->create( $data );
			$response = apply_filters( 'ignico_after_user_created', $response, $order_id );

			if ( $response ) {
				ig_info( 'User has been successfully added to Ignico.', [ 'response' => $response ] );

				if ( $order_repo->has_user( $order_id ) ) {

					$user = $order_repo->get_user( $order_id );
					$user_service->assign( $user->ID, $response->data->attributes->id );
				}
			} else {
				ig_info( 'User could not be added to Ignico.', [ 'response' => $response ] );
			}
		} catch ( \Exception $e ) {
			ig_error( 'There was problem with adding user to Ignico.', [ 'error' => $e ] );
			throw $e;
		}
	}

	/**
	 * Prepare user data for Ignico API call
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 */
	private function get_add_user_data_from_order( $order_id ) {

		ig_info( 'Get "add user" data based on WooCommerce order.' );

		$order_repo = $this->plugin['woocommerce/order/repository'];

		$data = [
			'data' => [
				'type'       => 'user',
				'attributes' => [
					'email'    => $order_repo->get_email( $order_id ),
					'country'  => $order_repo->get_country( $order_id ),
					'active'   => 1,
					'password' => \ignico_random_password(),
					'subject'  => [
						'person' => [
							'firstName' => $order_repo->get_first_name( $order_id ),
							'lastName'  => $order_repo->get_last_name( $order_id ),
						],
					],
				],
			],
		];

		$referrer = $this->get_add_user_referrer_data_from_order( $order_id );

		if ( ! is_null( $referrer ) ) {
			$data['data']['attributes']['referrer']['referralCode'] = $referrer;
		}

		return $data;
	}

	/**
	 * Prepare user referrer data for Ignico API call
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return string|null
	 */
	private function get_add_user_referrer_data_from_order( $order_id ) {

		ig_info( 'Get referrer data based on WooCommerce order.' );

		$order_repo = $this->plugin['woocommerce/order/repository'];
		$user       = $order_repo->get_user( $order_id );
		$user_id    = ( $user instanceof \WP_User ) ? $user->ID : null;
		$referrer   = $this->plugin['ignico/referrer']->get_referrer( $user_id );

		if ( $referrer && ! empty( $referrer ) ) {
			if ( $this->is_valid_referrer( $user->user_email, $referrer ) ) {
				ig_info(
					'Provided referrer is valid.', [
						'referrer' => $referrer,
						'user'     => $user_id,
					]
				);
				return $referrer;
			} else {
				ig_info(
					'Provided referrer is not valid.', [
						'referrer' => $referrer,
						'user'     => $user_id,
					]
				);
				return null;
			}
		} else {
			ig_info( 'There is no referrer.' );
			return null;
		}

		return null;
	}

	/**
	 * Check if provided referrer is valid referrer
	 *
	 * Referrer can not refer himself. We need to check if user which will be
	 * added is do not try to pass own referral code.
	 *
	 * @param string $email    User e-mail address.
	 * @param string $referrer User referrer code.
	 *
	 * @return bool
	 */
	private function is_valid_referrer( $email, $referrer ) {
		$ig_user = $this->plugin['ignico/repository']->get_user_by_email( $email );

		if ( $ig_user ) {

			// If provided referrer and current user referrer code are the same
			// referrer code is not valid. User can not refer himself.
			if ( $referrer === $ig_user->attributes->referralCode ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Create Ignico action based on WooCommerce order
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 *
	 * @throws \Exception When there is a problem with adding action to Ignico.
	 */
	public function add_action_based_on_order( $order_id ) {

		ig_info( 'Add action to Ignico based on WooCommerce order.' );

		$order_repo = $this->plugin['woocommerce/order/repository'];
		$data       = $this->get_add_action_data_from_order( $order_id );

		if ( $order_repo->has_referrer( $order_id ) ) {
			$data['data']['attributes']['referrer']['referralCode'] = $order_repo->get_referrer( $order_id );
		}

		try {
			ig_info( 'Add action to Ignico.', $data );

			$data     = apply_filters( 'ignico_before_action_created', $data, $order_id );
			$response = $this->plugin['ignico/client']->action()->create( $data );
			$response = apply_filters( 'ignico_after_action_created', $response, $order_id );

			if ( $response ) {
				ig_info( 'Action has been successfully added to Ignico.', [ 'response' => $response ] );
				$order_repo->save_action_added( $order_id, 1 );
			} else {
				ig_info( 'Action has not been added to Ignico.', [ 'response' => $response ] );
			}
		} catch ( \Exception $e ) {
			ig_error( 'There was problem with adding action to Ignico.', [ 'error' => $e ] );
			throw $e;
		}
	}

	/**
	 * Prepare action data for Ignico API call
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return array
	 */
	public function get_add_action_data_from_order( $order_id ) {

		ig_info( 'Get "add action" data based on WooCommerce order.' );

		return [
			'data' => [
				'type'       => 'action',
				'attributes' => [
					'type'      => $this->get_action_type( $order_id ),
					'title'     => $this->get_action_title( $order_id ),
					'takenAt'   => $this->get_action_date( $order_id ),
					'performer' => [
						'email' => $this->get_action_performer_email( $order_id ),
					],
					'params'    => [
						'value' => $this->get_action_value( $order_id ),
					],
				],
			],
		];
	}

	/**
	 * Get action title
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return string
	 */
	private function get_action_title( $order_id ) {
		/* Translators: %d is WooCommerce order ID */
		$title = sprintf( esc_html__( 'New order %d', 'ignico' ), $order_id );
		return apply_filters( 'ignico_action_title_date', $title, $order_id );
	}

	/**
	 * Get action date
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return string
	 */
	private function get_action_date( $order_id ) {
		return apply_filters( 'ignico_action_date', current_time( 'Y-m-d H:i:s' ), $order_id );
	}

	/**
	 * Get action email
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return string
	 */
	private function get_action_performer_email( $order_id ) {
		$order_repo = $this->plugin['woocommerce/order/repository'];
		return apply_filters( 'ignico_action_performer_email_date', $order_repo->get_email( $order_id ), $order_id );
	}

	/**
	 * Get action value
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return mixed
	 */
	private function get_action_value( $order_id ) {
		$order_repo = $this->plugin['woocommerce/order/repository'];
		return apply_filters( 'ignico_action_value', $order_repo->get_total( $order_id ), $order_id );
	}

	/**
	 * Get action type
	 *
	 * @param int $order_id WooCommerce order ID.
	 *
	 * @return bool
	 */
	private function get_action_type( $order_id ) {
		return apply_filters( 'ignico_action_type', 'transaction', $order_id );
	}
}
