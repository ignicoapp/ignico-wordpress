<?php
/**
 * Payout rejected text email
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/woocommerce/payout/email/partials
 *
 * @var string $email_heading Email heading.
 * @var string $intro         Email intro.
 *
 * @var string $payout_title      Payout title.
 * @var string $payout_amount     Payout amount.
 * @var string $payout_status     Payout status.
 * @var string $payout_created_at Payout creation date.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

echo esc_html( $intro ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
