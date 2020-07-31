<?php
/**
 * Consent form
 */
?>

<form method="post" class="ignico-form">
	<?php if ( ig_consent_required() ) : ?>
		<?php
			$plugin   = ignico();
			$settings = $plugin['admin/settings'];
		?>
		<div class="woocommerce-terms-and-conditions-wrapper">

			<p class="form-row">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="ignico_consent" <?php checked( isset( $_POST['ignico_consent'] ) ); // WPCS: input var ok, csrf ok. ?> id="ignico_consent" value="1" />
					<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo $settings->get_consent_text(); ?></span>
				</label>
			</p>

		</div>
	<?php endif; ?>
	<div>
		<input type="submit" name="submit_consent" value="<?php esc_html_e( 'Join rewards program', 'ignico' ); ?>" />
	</div>
</form>

