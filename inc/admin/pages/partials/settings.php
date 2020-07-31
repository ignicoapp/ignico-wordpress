<?php
/**
 * Partial for displaying settings tab in admin area.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/admin/pages/partials
 */

?>

<div class="wrap">
	<h2><?php esc_html_e( 'Settings', 'ignico' ); ?></h2>

	<?php $plugin = ignico(); ?>
	<?php $plugin['admin/pages']->display_tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( '/options.php' ) ); ?>">

		<?php
			settings_fields( $plugin['settings_id'] );

			$settings              = $plugin['admin/settings']->get_settings();
			$available_cookie_flow = $plugin['admin/settings']->get_available_cookie_flow();

		?>

		<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( esc_url( $plugin['admin/pages']->get_admin_plugin_url( 'ignico', 'settings' ) ) ); ?>">

		<h2 class="title"><?php esc_html_e( 'Referral cookie', 'ignico' ); ?></h2>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Cookie flow', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->select( 'ignico_settings[cookie_flow]', $available_cookie_flow, $settings['cookie_flow'] ); ?>
					<p class="description"><?php esc_html_e( 'Choose if a new referral link can overwrite already existing cookie.', 'ignico' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Cookie removal', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->checkbox( 'ignico_settings[cookie_removal]', esc_html__( 'Remove referral cookie after order has been successfully placed', 'ignico' ), $settings['cookie_removal'] ); ?>
				</td>
			</tr>
		</table>

		<hr>

		<h2 class="title"><?php esc_html_e( 'Rewards program sign up', 'ignico' ); ?></h2>
		<p><?php esc_html_e( 'Decide if users should mark additional consent in order to join the program.', 'ignico' ); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Consent', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->checkbox( 'ignico_settings[consent_required]', 'Show a consent in register and checkout forms and on “My account” page by providing additional checkbox.', $settings['consent_required'] ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Consent checkbox text:', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->text( 'ignico_settings[consent_text]', $settings['consent_text'] ); ?>
				</td>
			</tr>
		</table>

		<hr>

		<h2 class="title"><?php esc_html_e( 'Payouts', 'ignico' ); ?></h2>
		<p><?php esc_html_e( 'Choose payout options that will be available for user on “Rewards program.” dashboard', 'ignico' ); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Payout options', 'ignico' ); ?></th>
				<td>
					<p><?php $plugin['admin/form/fields']->checkbox( 'ignico_settings[payout_available]', esc_html__( 'Withdrawal request', 'ignico' ), $settings['payout_available'] ); ?></p>
					<p><?php $plugin['admin/form/fields']->checkbox( 'ignico_settings[coupon_available]', esc_html__( 'Coupon', 'ignico' ), $settings['coupon_available'] ); ?></p>
				</td>
			</tr>
		</table>

		<hr>

		<h2 class="title"><?php esc_html_e( 'Content on rewards program dashboard', 'ignico' ); ?></h2>
		<p><?php esc_html_e( 'There are custom pages you can edit to customize user experience of your rewards program dashboard.', 'ignico' ); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php esc_html_e( 'Dashboard homepage', 'ignico' ); ?><br>
					<?php esc_html_e( 'Payout', 'ignico' ); ?><br>
					<?php esc_html_e( 'Coupon', 'ignico' ); ?><br>
					<?php esc_html_e( 'Sign up form', 'ignico' ); ?>
				</th>
				<td>
					<a href="<?php echo esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', ig_get_rewards_program_page_id() ) ) ); ?>">[edit]</a><br>
					<a href="<?php echo esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', ig_get_payout_page_id() ) ) ); ?>">[edit]</a><br>
					<a href="<?php echo esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', ig_get_coupon_page_id() ) ) ); ?>">[edit]</a><br>
					<a href="<?php echo esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', ig_get_consent_page_id() ) ) ); ?>">[edit]</a>
				</td>
			</tr>
		</table>

		<p class="submit">
			<?php submit_button( esc_html__( 'Save Changes', 'ignico' ), 'primary', 'save_application_data', false ); ?>
		</p>

	</form>
</div>
