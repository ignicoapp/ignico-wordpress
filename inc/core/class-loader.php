<?php
/**
 * Register all actions and filters for the theme
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 */

namespace IgnicoWordPress\Core;

/**
 * Register all actions and filters for the theme.
 *
 * Maintain a list of all hooks that are registered throughout
 * the theme, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 * @author     Ignico <contact@igni.co>
 */
class Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @var      array $actions The actions registered with WordPress to fire when the theme loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @var      array $filters The filters registered with WordPress to fire when the theme loads.
	 */
	protected $filters;

	/**
	 * Plugin container which store properties, objects, callbacks.
	 *
	 * @access   protected
	 * @var      Init $plugin Plugin container.
	 */
	protected $plugin;


	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @param Init $plugin Plugin container.
	 */
	public function __construct( $plugin ) {

		$this->actions = array();
		$this->filters = array();

		$this->plugin = $plugin;
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param    string $hook          The name of the WordPress action that is being registered.
	 * @param    object $component     A reference to the instance of the object on which the action is defined.
	 * @param    string $callback      The name of the function definition on the $component.
	 * @param    int    $priority      Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param    string $hook          The name of the WordPress filter that is being registered.
	 * @param    object $component     A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback      The name of the function definition on the $component.
	 * @param    int    $priority      Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @param    array  $hooks         The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string $hook          The name of the WordPress filter that is being registered.
	 * @param    object $component     A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback      The name of the function definition on the $component.
	 * @param    int    $priority      The priority at which the function should be fired.
	 * @param    int    $accepted_args The number of arguments that should be passed to the $callback.
	 *
	 * @return   array  $hooks The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Call the apply_filters function from WordPress.
	 *
	 * Method is useful with test when we want to mock loader class and check if apply_filters method is executed.
	 *
	 * @param string $tag     The name of the filter hook.
	 * @param mixed  $value   The value on which the filters hooked to `$tag` are applied on.
	 *
	 * @return mixed The filtered value after all hooked functions are applied to it.
	 */
	public function apply_filters( $tag, $value ) {
		return apply_filters( $tag, $value );
	}

	/**
	 * Register the filters and actions with WordPress.
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}
}
