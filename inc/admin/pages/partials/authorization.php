<?php
/**
 * Partial for displaying authorization tab in admin area.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/admin/pages/partials
 */

?>

<div class="wrap">
	<h2><?php esc_html_e( 'Ignico authorization', 'ignico' ); ?></h2>

	<?php $plugin = ignico(); ?>
	<?php $plugin['admin/pages']->display_tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( '/options.php' ) ); ?>">

		<?php
			settings_fields( $plugin['settings_id'] );

			$settings = $plugin['admin/settings']->get_settings();
		?>

		<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( esc_url( $plugin['admin/pages']->get_admin_plugin_url( 'options', 'authorization' ) ) ); ?>">

		<div class="card">
			<h2><?php esc_html_e( 'Plugin authorization', 'ignico' ); ?></h2>
			<p><?php esc_html_e( 'This plugin requires access to your Ignico account. To access Ignico, you have to provide Client id and Client secret from Ignico Admin Panel -> Integration > OAuth clients page.', 'ignico' ); ?></p>
		</div>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Workspace:', 'ignico' ); ?></th>
				<td>
					<input type="text" name="ignico_settings[workspace]" value="<?php echo esc_attr( $settings['workspace'] ); ?>" class="regular-text" />
					<p class="description"><?php esc_html_e( 'Your workspace name would be used to build your API custom  url https://{{workspace}}.igni.co', 'ignico' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Client ID:', 'ignico' ); ?></th>
				<td><input type="password" name="ignico_settings[client_id]" value="<?php echo esc_attr( $settings['client_id'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Client secret:', 'ignico' ); ?></th>
				<td><input type="password" name="ignico_settings[client_secret]" value="<?php echo esc_attr( $settings['client_secret'] ); ?>" class="regular-text" /></td>
			</tr>
		</table>
		<p class="submit">
			<?php submit_button( esc_html__( 'Save Changes', 'ignico' ), 'primary', 'save_application_data', false ); ?>
		</p>

	</form>
</div>
