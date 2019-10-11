<?php
/**
 * Partial for displaying generate coupon view
 */
?>
<?php

$form = $this->plugin['woocommerce/myaccount/form/generate-coupon'];

?>
<h2><?php esc_html_e( 'Generate coupon', 'ignico' ); ?></h2>
<p><?php esc_html_e( 'Coupon jest kuponem rabatowym, który możesz wykorzystać w sklepie na kolejne zakupy. Wprowadź ilość punktów, które chcesz przeznaczyć na coupon i kliknij "Wygeneruj coupon". Każdy jeden punkt na couponze to jedna złotówka do wydania w sklepie. Wygenerowany coupon możesz wykrozystać na widoku koszyka.', 'ignico' ); ?></p>

<div class="ignico-row">
	<div class="ignico-col-6 ignico-rce">
		<?php echo $form->display_form(); ?>
	</div>
</div>
