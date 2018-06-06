<?php
/**
 * Partial for displaying settings tab in admin area.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/admin/pages/partials
 */

?>

<div class="wrap">
	<h2><?php esc_html_e( 'Ignico settings', 'ignico' ); ?></h2>

	<?php $plugin = ignico(); ?>
	<?php $plugin['admin/pages']->display_tabs(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( '/options.php' ) ); ?>">

		<input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr( esc_url( $plugin['admin/pages']->get_admin_plugin_url( 'options', 'settings' ) ) ); ?>">

		<h2 class="title">General settings</h2>
		<p class="description"><?php echo esc_html( __( 'Nothing to set here yet.', 'ignico' ) ); ?></p>
	</form>
</div>
