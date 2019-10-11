<?php
/**
 * Shortcode for display commission
 */

use IgnicoWordPress\WooCommerce\Payout\Status as PayoutStatus;
use IgnicoWordPress\WooCommerce\Coupon\Status as CouponStatus;

// Template variables make sure they exist
$wallet_amount = isset( $wallet_amount ) ? $wallet_amount : null;
$actions       = isset( $actions ) ? $actions : null;
$coupons       = isset( $coupons ) ? $coupons : null;
$payouts       = isset( $payouts ) ? $payouts : null;

?>

<div class="ignico-row ignico-my-5">
	<div class="ignico-col-12">
		<div class="ignico-bg-light ignico-py-4 ignico-px-5 ignico-rce ignico-h-100 ignico-text-center">
			<h3><?php esc_html_e( 'Your commission', 'ignico' ); ?></h3>
			<div class="h1 ignico-font-weight-bold ignico-font-size-large"><?php echo wp_kses_post( wc_price( $wallet_amount ) ); ?></div>
		</div>
	</div>
</div>

<?php if ( ig_is_payout_available() || ig_is_coupon_available() ) : ?>
	<div class="ignico-row ignico-my-3 no-gutters ignico-text-center ignico-justify-content-center">
		<?php if ( ig_is_payout_available() ) : ?>
			<div class="ignico-col-6">
				<a href="<?php echo esc_url( get_permalink( ig_get_payout_page_id() ) ); ?>" class="ignico-btn ignico-btn-lg ignico-btn-primary ignico-btn-block"><?php esc_html_e( 'Withdraw commission', 'ignico' ); ?></a>
			</div>
		<?php endif; ?>
		<?php if ( ig_is_coupon_available() ) : ?>
			<div class="ignico-col-6">
				<a href="<?php echo esc_url( get_permalink( ig_get_coupon_page_id() ) ); ?>" class="ignico-btn ignico-btn-lg ignico-btn-primary ignico-btn-block"><?php esc_html_e( 'Generate coupon', 'ignico' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( ! empty( $actions ) ) : ?>
	<h3><?php esc_html_e( 'Referrals', 'ignico' ); ?></h3>
	<table class="table">
		<thead>
		<tr>
			<th scope="col" style="width: 30%;"><?php esc_html_e( 'Title', 'ignico' ); ?></th>
			<th scope="col" style="width: 20%;"><?php esc_html_e( 'Referred user', 'ignico' ); ?></th>
			<th scope="col" style="width: 20%;"><?php esc_html_e( 'Amount', 'ignico' ); ?></th>
			<th scope="col" style="width: 30%;"><?php esc_html_e( 'Created at', 'ignico' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $actions as $action ) : ?>

			<tr>
				<td><?php esc_html_e( $action['title'] ); ?></td>
				<td><?php esc_html_e( $action['performer'] ); ?></td>
				<td><?php echo wp_kses_post( wc_price( $action['value'] ) ); ?></td>
				<td><?php esc_html_e( date( get_option( 'date_format' ), strtotime( $action['created_at'] ) ) ); ?></td>
			</tr>

		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if ( $coupons && $coupons->have_posts() ) : ?>
	<h3><?php esc_html_e( 'Coupons', 'ignico' ); ?></h3>
	<table class="table">
		<thead>
		<tr>
			<th scope="col" style="width: 45%;"><?php esc_html_e( 'Code', 'ignico' ); ?></th>
			<th scope="col" style="width: 15%;"><?php esc_html_e( 'Amount', 'ignico' ); ?></th>
			<th scope="col" style="width: 15%;"><?php esc_html_e( 'Status', 'ignico' ); ?></th>
			<th scope="col" style="width: 25%;"><?php esc_html_e( 'Created at', 'ignico' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		while ( $coupons->have_posts() ) :
			$coupons->the_post();

			$code      = get_the_title();
			$coupon_id = get_post_meta( get_the_ID(), '_ignico_coupon_id', true );
			$status    = get_post_meta( get_the_ID(), '_ignico_coupon_status', true );
			$amount    = get_post_meta( $coupon_id, 'coupon_amount', true );
			?>

			<tr>
				<td><?php esc_html_e( $code ); ?></td>
				<td><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
				<td><?php esc_html_e( CouponStatus::get_name( $status ) ); ?></td>
				<td><?php esc_html_e( get_the_date() ); ?></td>
			</tr>

		<?php endwhile; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if ( $payouts && $payouts->have_posts() ) : ?>
	<h3><?php esc_html_e( 'Payouts', 'ignico' ); ?></h3>
	<table class="table">
		<thead>
		<tr>
			<th scope="col" style="width: 45%;"><?php esc_html_e( 'Title', 'ignico' ); ?></th>
			<th scope="col" style="width: 15%;"><?php esc_html_e( 'Amount', 'ignico' ); ?></th>
			<th scope="col" style="width: 15%;"><?php esc_html_e( 'Status', 'ignico' ); ?></th>
			<th scope="col" style="width: 25%;"><?php esc_html_e( 'Created at', 'ignico' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		while ( $payouts->have_posts() ) :
			$payouts->the_post();

			$title  = get_the_title();
			$status = get_post_meta( get_the_ID(), '_ignico_payout_status', true );
			$amount = get_post_meta( get_the_ID(), '_ignico_payout_amount', true );
			?>

			<tr>
				<td><?php esc_html_e( $title ); ?></td>
				<td><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
				<td><?php esc_html_e( PayoutStatus::get_name( $status ) ); ?></td>
				<td><?php esc_html_e( get_the_date() ); ?></td>
			</tr>

		<?php endwhile; ?>
		</tbody>
	</table>
<?php endif; ?>
