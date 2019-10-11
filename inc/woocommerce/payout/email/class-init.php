<?php
/**
 * Class to provide new email template for new payouts
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon/email
 */

namespace IgnicoWordPress\WooCommerce\Payout\Email;

use IgnicoWordPress\Core\Init as CoreInit;

use IgnicoWordPress\WooCommerce\Payout\PostType;

/**
 * Class to provide new email template for new payouts
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon/email
 */
class Init {

	/**
	 * Plugin container.
	 *
	 * @var Init $plugin Ignico plugin container
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Referral
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Add payout new email class
	 *
	 * @param array $email_classes available email classes.
	 *
	 * @return array
	 */
	public function add_email_classes( $email_classes ) {

		$this->plugin['woocommerce/payout/email/payout-new']       = new Payout_New( $this->plugin );
		$this->plugin['woocommerce/payout/email/payout-completed'] = new Payout_Completed( $this->plugin );
		$this->plugin['woocommerce/payout/email/payout-rejected']  = new Payout_Rejected( $this->plugin );

		$email_classes['Payout_New']       = $this->plugin['woocommerce/payout/email/payout-new'];
		$email_classes['Payout_Completed'] = $this->plugin['woocommerce/payout/email/payout-completed'];
		$email_classes['Payout_Rejected']  = $this->plugin['woocommerce/payout/email/payout-rejected'];

		return $email_classes;
	}

	/**
	 * Init mailer when payout will be save
	 *
	 * To be able to send email when payout will be saved we need to
	 * initialize WooCommerce mailer before payout saving.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    WordPress post object.
	 *
	 * @return int
	 */
	public function init_mailer( $post_id, $post ) {

		// Only initialize mailer when payout status is saving.
		if ( PostType::ID === $post->post_type ) {

			WC()->mailer();

			return $post_id;
		}

		return $post_id;
	}

	/**
	 * Add all hooks and execute related code here at single place.
	 *
	 * @return void
	 */
	public function run() {

		/**
		 * Change coupon status to realized when coupon is applied
		 */
		$this->plugin['loader']->add_action( 'woocommerce_email_classes', $this, 'add_email_classes' );

		/**
		 * Init mailer when payout will be save
		 *
		 * To be able to send email when payout will be saved we need to
		 * initialize WooCommerce mailer before payout saving.
		 */
		$this->plugin['loader']->add_action( 'save_post', $this, 'init_mailer', 0, 2 );
	}
}
