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
				<th scope="row"><?php esc_html_e( 'Cookie flow:', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->select( 'ignico_settings[cookie_flow]', $available_cookie_flow, $settings['cookie_flow'] ); ?>
					<p class="description"><?php esc_html_e( 'Choose if new referral link can overwrite already existing cookie', 'ignico' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Cookie removal:', 'ignico' ); ?></th>
				<td>
					<?php $plugin['admin/form/fields']->checkbox( 'ignico_settings[cookie_removal]', esc_html__( 'Remove referral cookie after order has been successfully placed', 'ignico' ), $settings['cookie_removal'] ); ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<?php submit_button( esc_html__( 'Save Changes', 'ignico' ), 'primary', 'save_application_data', false ); ?>
		</p>

	</form>
</div>
