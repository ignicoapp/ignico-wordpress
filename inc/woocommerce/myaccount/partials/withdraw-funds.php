<?php
/**
 * Partial for displaying payout funds view
 */
?>
<?php

$form = $this->plugin['woocommerce/myaccount/form/payout-funds'];

?>
<h2><?php esc_html_e( 'Payout funds', 'ignico' ); ?></h2>
<p><?php esc_html_e( 'Wprowadź kwotę, jaką chcesz przeznaczyć na wypłatę z konta prowizji i kliknij "Wypłać środki". Po wysłaniu formularza zostanie wysłana wiadomość email do administracji sklepu z Twoją prośbą wypłaty środków. Administracja sklepu skontaktuje się z Tobą w celu ustalenia formy wypłaty środków.', 'ignico' ); ?></p>

<div class="ignico-row">
	<div class="ignico-col-6 ignico-rce">
		<?php echo $form->display_form(); ?>
	</div>
</div>
