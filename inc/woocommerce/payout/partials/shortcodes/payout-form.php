<?php
/**
 * Payout form shortcode
 */

$form   = isset( $form ) ? $form : null;
$amount = isset( $amount ) ? $amount : null;
?>

<form method="post" class="ignico-form">
	<div class="ignico-mb-3">
		<label class="ignico-label" for="amount"><?php esc_html_e( 'Amount', 'ignico' ); ?></label>
		<input type="text" name="amount" value="<?php esc_attr_e( $amount ); ?>">
		<?php $errors = $form->get_errors( 'amount' ); ?>
		<?php foreach ( $errors as $error ) : ?>
			<p class="ignico-errors"><?php esc_html_e( $error ); ?></p>
		<?php endforeach; ?>
	</div>
	<div>
		<input type="submit" name="submit_payout" value="<?php esc_html_e( 'Withdraw commission', 'ignico' ); ?>" />
	</div>
</form>
