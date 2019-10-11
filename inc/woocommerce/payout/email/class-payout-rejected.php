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
class Payout_Rejected extends \WC_Email {

	/**
	 * Plugin container.
	 *
	 * @var Init $plugin Ignico plugin container
	 */
	private $plugin;

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
		$this->id = 'wc_payout_rejected';

		// this is the title in WooCommerce Email settings.
		$this->title = __( 'Payout rejected', 'ignico' );

		// this is the description in WooCommerce email settings.
		$this->description = __( 'E-mails is sent when payout is marked as rejected.', 'ignico' );

		// these are the default heading and subject lines that can be overridden using the settings.
		$this->heading = __( 'Payout rejected', 'ignico' );
		$this->subject = __( 'Payout rejected', 'ignico' );
		$this->intro   = __( 'Your payout request status has been changed to rejected.', 'ignico' );

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar.
		$this->template_html  = '/payout-rejected-html.php';
		$this->template_plain = '/payout-rejected-txt.php';

		// Trigger on when payout is marked as rejected.
		add_action( 'ignico_payout_pending_to_rejected', array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicity defined here.
		parent::__construct();
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

		$this->payout_id = $payout_id;

		$user_id = get_post_meta( $this->payout_id, '_ignico_payout_user_id', true );
		$user    = get_user_by( 'id', $user_id );

		$this->recipient = $user->user_email;

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
	 * Initialise Settings Form Fields - these are generic email options most will use.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'subject'            => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'            => array(
				'title'       => __( 'Email heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'intro'              => array(
				'title'       => __( 'Intro', 'woocommerce' ),
				'type'        => 'textarea',
				'css'         => 'width:400px; height: 75px;',
				'desc_tip'    => true,
				/* translators: %s: list of placeholders */
				'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' ),
				'placeholder' => $this->get_default_intro(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => __( 'Additional content', 'woocommerce' ),
				'description' => __( 'Text to appear below the main email content.', 'woocommerce' ) . ' ' . $placeholder_text,
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_additional_content(),
				'desc_tip'    => true,
			),
			'email_type'         => array(
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
