<?php
/**
 * Class to provide new email template for new payouts
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon/email
 */

namespace IgnicoWordPress\WooCommerce\Payout\Email;

use IgnicoWordPress\Core\Init as CoreInit;

/**
 * Class to provide new email template for new payouts
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/woocommerce/coupon/email
 */
class Payout_New extends \WC_Email {

	/**
	 * Plugin container.
	 *
	 * @var Init $plugin Ignico plugin container
	 */
	private $plugin;

	/**
	 * Email placeholders
	 *
	 * @var array
	 */
	protected $placeholders;

	/**
	 * Email content
	 *
	 * @var array
	 */
	protected $intro;

	/**
	 * Email payout ID
	 *
	 * @var int
	 */
	protected $payout_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Referral
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

		// set ID, this simply needs to be a unique name.
		$this->id = 'wc_payout_new';

		// this is the title in WooCommerce Email settings.
		$this->title = __( 'New payout', 'ignico' );

		// this is the description in WooCommerce email settings.
		$this->description = __( 'E-mails is sent when new payout request is made.', 'ignico' );

		// these are the default heading and subject lines that can be overridden using the settings.
		$this->heading = __( 'New payout request', 'ignico' );
		$this->subject = __( 'New payout request', 'ignico' );
		$this->intro   = __( 'You’ve received the payout request from {user_first_name} {user_last_name} {user_email}. <br><br> Please contact directly this user to determine the form of payout.', 'ignico' );

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_html  = '/payout-new-html.php';
		$this->template_plain = '/payout-new-txt.php';

		$this->placeholders = [
			'{user_first_name}' => '',
			'{user_last_name}'  => '',
			'{user_email}'      => '',
		];

		// Trigger on when payout is marked as pending.
		add_action( 'ignico_payout_pending', array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicity defined here.
		parent::__construct();

		// this sets the recipient to the settings defined below in init_form_fields().
		$this->recipient = $this->get_option( 'recipient' );

		// if none was entered, just use the WP admin email as a fallback.
		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * Return content from the content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_into() {
		return apply_filters( 'woocommerce_email_intro_' . $this->id, $this->format_string( $this->get_option( 'intro', $this->get_default_intro() ) ), $this->object, $this );
	}

	/**
	 * Return default intro from the intro field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_intro() {
		return $this->intro;
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $payout_id The order ID.
	 *
	 * @return void
	 */
	public function trigger( $payout_id ) {
		$this->setup_locale();

		if ( is_int( $payout_id ) ) {
			$this->payout_id = $payout_id;

			$user_id = get_post_meta( $this->payout_id, '_ignico_payout_user_id', true );
			$user    = get_user_by( 'id', $user_id );

			$this->placeholders['{user_first_name}'] = $user->first_name;
			$this->placeholders['{user_last_name}']  = $user->last_name;
			$this->placeholders['{user_email}']      = $user->user_email;
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Get email HTML part
	 *
	 * @return string
	 */
	public function get_content_html() {

		$title  = get_the_title( $this->payout_id );
		$amount = get_post_meta( $this->payout_id, '_ignico_payout_amount', true );
		$status = get_post_meta( $this->payout_id, '_ignico_payout_status', true );

		ob_start();
		wc_get_template(
			$this->template_html, array(
				'email_heading'     => $this->get_heading(),
				'intro'             => $this->get_into(),
				'payout_title'      => $title,
				'payout_amount'     => $amount,
				'payout_status'     => $status,
				'payout_created_at' => get_the_date( __( 'M j, Y g:i a', ' ignico' ), $this->payout_id ),
			), null, $this->plugin['path'] . '/inc/woocommerce/payout/email/partials'
		);
		return ob_get_clean();
	}


	/**
	 * Get email txt part
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain, array(
				'email_heading' => $this->get_heading(),
			), null, $this->plugin['path'] . '/inc/woocommerce/payout/email/partials'
		);
		return ob_get_clean();
	}

	/**
	 * Initialize Settings Form Fields
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				/* translators: %s: WP admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'placeholder' => '',
				'default'     => '',
				'desc_tip'    => true,
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'intro'      => array(
				'title'       => __( 'Intro', 'woocommerce' ),
				'type'        => 'textarea',
				'css'         => 'width:400px; height: 75px;',
				'desc_tip'    => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				'placeholder' => $this->get_default_intro(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}
