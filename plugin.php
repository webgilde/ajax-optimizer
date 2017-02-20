<?php
/**
 * Common functions.
 */
class AJAX_Optimizer {
	/**
	 * Single instance of the class.
	 *
	 * @var AJAX_Optimizer
	 */
	protected static $instance;

	/**
	 * Plugin options.
	 *
	 * @var     array (if loaded)
	 */
	protected $options;

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_plugin_textdomain();
	}

	/**
	 * Return instance of the class.
	 *
	 * @return Plugin_Conflicts
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'ajax-optimizer', false, AJAX_OPT_BASE_DIR . '/languages' );
	}

	/**
	 * Return plugin options.
	 *
	 * @return array $options
	 */
	public function options() {
		if ( ! isset( $this->options ) ) {
			$this->options = get_option( AJAX_OPT_SLUG, array() );
		}

		return $this->options;
	}

	/**
	 * On activation.
	 */
	public function activation() {
		AJAX_Optimizer_MU_Admin::get_instance()->create_mu_plugin_on_activation();
	}

}
