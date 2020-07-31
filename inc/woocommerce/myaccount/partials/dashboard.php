<?php
/**
 * Partial for displaying referral program
 */

use IgnicoWordPress\WooCommerce\Coupon\Status as CouponStatus;
use IgnicoWordPress\WooCommerce\Payout\Status as PayoutStatus;

$wallet  = $this->plugin['woocommerce/myaccount/referral/wallet'];
$coupons = $this->plugin['woocommerce/myaccount/referral/coupons'];
$payouts = $this->plugin['woocommerce/myaccount/referral/payouts'];
$actions = $this->plugin['woocommerce/myaccount/referral/actions'];

?>
<?php $this->plugin['notice']->woocommerce_notice(); ?>
<h3><?php esc_html_e( 'Referral links', 'ignico' ); ?></h3>
<p>Link partnerski pozwala polecać sklep <?php bloginfo( 'name' ); ?> z korzyścią dla partnera i osoby, która skorzysta z polecenia. Partner za każdy sfinalizowany zakup otrzyma prowizję za każdy sfinalizowany zakup na swoje konto, które może wykorzystać w sklepie albo wypłacić w formie pieniężnej.</p>

<div class="ignico-socials ignico-mb-3" data-ignico-share>
	<button class="ignico-btn ignico-btn-gray ignico-socials-btn ignico-mr-2" data-ignico-socials-email>
		<svg class="ignico-btn-icon ignico-mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12.713l-11.985-9.713h23.97l-11.985 9.713zm0 2.574l-12-9.725v15.438h24v-15.438l-12 9.725z"/></svg>
		<span><?php echo __( 'Email', 'ignico' ); ?></span>
	</button><!--
 --><button class="ignico-btn ignico-btn-gray ignico-socials-btn" aria-label="<?php echo __( 'Copied', 'ignico' ); ?>" data-ignico-socials-link>
		<svg class="ignico-btn-icon ignico-mr-1" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero"><path d="M14.618 9.338a5.59 5.59 0 0 0-7.905 0L1.635 14.42a5.59 5.59 0 0 0 3.952 9.537 5.553 5.553 0 0 0 3.949-1.628l4.191-4.192a.4.4 0 0 0-.283-.683h-.16a6.72 6.72 0 0 1-2.555-.495.4.4 0 0 0-.435.088L7.28 20.065a2.397 2.397 0 1 1-3.39-3.39l5.099-5.093a2.395 2.395 0 0 1 3.385 0c.63.593 1.614.593 2.244 0 .27-.272.435-.632.463-1.014.029-.458-.14-.906-.463-1.23z"/><path d="M22.319 1.637a5.59 5.59 0 0 0-7.905 0l-4.188 4.184a.4.4 0 0 0 .292.683h.147a6.707 6.707 0 0 1 2.551.499.4.4 0 0 0 .436-.088l3.006-3.002a2.397 2.397 0 1 1 3.389 3.389l-3.745 3.74-.032.037-1.31 1.301a2.395 2.395 0 0 1-3.384 0 1.637 1.637 0 0 0-2.244 0 1.597 1.597 0 0 0-.463 1.022c-.03.457.14.905.463 1.23a5.541 5.541 0 0 0 1.597 1.118c.084.04.167.071.251.107.084.036.172.064.256.096.084.032.171.06.255.084l.236.064c.16.04.32.072.483.1.197.029.396.048.595.056H13.308l.24-.028c.087-.004.18-.024.283-.024h.136l.275-.04.128-.024.232-.048h.044a5.589 5.589 0 0 0 2.59-1.47l5.083-5.081a5.59 5.59 0 0 0 0-7.905z"/></g></svg>
		<span><?php echo __( 'Copy link', 'ignico' ); ?></span>
	</button>
</div>


<h3><?php esc_html_e( 'Commission', 'ignico' ); ?></h3>
<p>Zgromadzoną prowizję możesz przeznaczyć na nowe zakupy lub wypłacić w formie pieniężnej. Jeżeli zdecydujesz się przeznaczyć prowizję na zakupy kliknij "Wygeneruj coupon", jeżeli chcesz wypłacić prowizję kliknij "Wypłać prowizję". Po kliknięciu przycisku zostaniesz przekierowany na stronę z instrukcją co dalej.</p>
<div class="ignico-row ignico-my-5">
	<div class="ignico-col-12 ignico-col-md-6">
		<div class="ignico-bg-light ignico-py-4 ignico-px-5 ignico-rce ignico-h-100 ignico-text-center">
			<h3><?php esc_html_e( 'Your commission', 'ignico' ); ?></h3>
			<div class="h1 ignico-font-weight-bold ignico-font-size-large"><?php echo wp_kses_post( $this->walletAmount( $wallet ) ); ?></div>
		</div>
	</div>
	<div class="ignico-col-12 ignico-col-md-6">
		<div class="ignico-bg-light ignico-py-4 ignico-px-5 ignico-rce ignico-h-100 ignico-text-center">
			<h3><?php esc_html_e( 'Your commission in points', 'ignico' ); ?></h3>
			<div class="h1 ignico-font-weight-bold ignico-font-size-large"><?php echo wp_kses_post( $this->walletPoints( $wallet ) ); ?></div>
		</div>
	</div>
</div>
<div class="ignico-row ignico-my-3 no-gutters ignico-text-center">
	<div class="ignico-col-6">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'rewards-program', 'payout-funds' ) ); ?>" class="ignico-btn ignico-btn-lg ignico-btn-primary ignico-btn-block"><?php esc_html_e( 'Payout commission', 'ignico' ); ?></a>
	</div>
	<div class="ignico-col-6">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'rewards-program', 'generate-coupon' ) ); ?>" class="ignico-btn ignico-btn-lg ignico-btn-primary ignico-btn-block"><?php esc_html_e( 'Generate coupon', 'ignico' ); ?></a>
	</div>
</div>

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
					<td><?php echo esc_html( $action['title'] ); ?></td>
					<td><?php echo esc_html( $action['performer'] ); ?></td>
					<td><?php echo wp_kses_post( wc_price( $action['value'] ) ); ?></td>
					<td><?php echo esc_html( date( get_option( 'date_format' ), strtotime( $action['created_at'] ) ) ); ?></td>
				</tr>

			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if ( $coupons->have_posts() ) : ?>
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
					<td><?php echo esc_html( $code ); ?></td>
					<td><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
					<td><?php echo esc_html( CouponStatus::get_name( $status ) ); ?></td>
					<td><?php echo esc_html( get_the_date() ); ?></td>
				</tr>

			<?php endwhile; ?>
		</tbody>
	</table>
<?php endif; ?>

<?php if ( $payouts->have_posts() ) : ?>
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
					<td><?php echo esc_html( $title ); ?></td>
					<td><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
					<td><?php echo esc_html( PayoutStatus::get_name( $status ) ); ?></td>
					<td><?php echo esc_html( get_the_date() ); ?></td>
				</tr>

			<?php endwhile; ?>
		</tbody>
	</table>
<?php endif; ?>

<script>var referrer_code = '<?php echo esc_attr( get_user_meta( get_current_user_id(), '_ignico_referral_code', true ) ); ?>' ;</script>
