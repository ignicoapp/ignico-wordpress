<?php
/**
 * New payout text email
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/woocommerce/payout/email/partials
 *
 * @var string $email_heading Email heading.
 * @var string $intro         Email intro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '= ' . esc_html( $email_heading ) . " =\n\n";

/* translators: %s: Customer billing full name */
echo esc_html( $intro, 'ignico' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
