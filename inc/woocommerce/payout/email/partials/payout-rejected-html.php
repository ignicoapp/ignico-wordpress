<?php
/**
 * Payout rejected html email
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

use IgnicoWordPress\WooCommerce\Payout\Status as PayoutStatus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, null ); ?>

<p><?php echo wp_kses_post( $intro ); ?></p>
<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<colgroup>
			<col style="width: 20%;">
			<col style="width: 80%;">
		</colgroup>
		<tbody>
			<tr>
				<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Title', 'ignico' ); ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_html( $payout_title ); ?></td>
			</tr>
			<tr>
				<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Amount', 'ignico' ); ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( wc_price( $payout_amount ) ); ?></td>
			</tr>
			<tr>
				<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Status', 'ignico' ); ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( PayoutStatus::get_name( $payout_status ), 'ignico' ); ?></td>
			</tr>
			<tr>
				<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Created at', 'ignico' ); ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_html( $payout_created_at ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', null );
