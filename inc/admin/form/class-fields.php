<?php
/**
 * Class provided to manage form fields in admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin/form
 */

namespace IgnicoWordPress\Admin\Form;

/**
 * Class provided to manage form fields in admin section of the plugin
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/admin/form
 */
class Fields {

	/**
	 * Plugin container.
	 *
	 * @var object $plugin IgnicoWordPress Plugin container
	 */
	public $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param object $plugin IgnicoWordPress Plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Render select field
	 *
	 * @param string $name    Select field name.
	 * @param array  $options Select field options.
	 * @param string $value   Select field value.
	 *
	 * @return void
	 */
	public function select( $name, $options, $value = '' ) {
	?>
		<select name="<?php echo esc_attr( $name ); ?>">;
			<?php $this->options( $options, $value ); ?>
		</select>
	<?php
	}

	/**
	 * Render select field options
	 *
	 * @param array $options Selection field options.
	 * @param array $value   Selection field value.
	 *
	 * @return void
	 */
	public function options( $options, $value ) {
	?>
		<?php foreach ( $options as $option_value => $option_label ) : ?>

			<?php $selected = ( $option_value === $value ) ? ' selected' : ''; ?>

			<option value="<?php echo esc_attr( $option_value ) ?>"<?php echo esc_attr( $selected ) ?>><?php esc_html_e( $option_label, 'ignico' ) ?></option> <?php // @codingStandardsIgnoreLine We want to pass variable to esc_html__ as first argument not string literal. ?>
		<?php endforeach; ?>
	<?php
	}

	/**
	 * Render checkbox field
	 *
	 * @param string $name  Checkbox field name.
	 * @param string $label Checkbox field label.
	 * @param bool   $value Checkbox field value.
	 *
	 * @return void
	 */
	public function checkbox( $name, $label, $value ) {
	?>
		<?php $checked = ( (bool) $value ) ? ' checked' : ''; ?>

		<label>
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
			<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1"<?php echo esc_attr( $checked ); ?>>
		    <?php esc_html_e( $label, 'ignico' ); // @codingStandardsIgnoreLine We want to pass variable to esc_html__ as first argument not string litera ?>
		</label>
	<?php
	}
}
