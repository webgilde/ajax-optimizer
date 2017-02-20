<?php
/**
 * Admin UI.
 */
class AJAX_Optimizer_Admin {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Slug of the settings page.
	 *
	 * @var string
	 */
	public $plugin_screen_hook_suffix = null;

	/**
	 * @var AJAX_Optimizer
	 */
	protected $plugin;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->plugin = AJAX_Optimizer::get_instance();

		// Add menu items.
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		// Settings page.
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return Plugin_Conflicts_Admin a single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register admin pages.
	 */
	public function add_menu_page() {
		if ( is_super_admin() ) {
			$this->plugin_screen_hook_suffix = add_plugins_page( __( 'AJAX Optimizer', 'ajax-optimizer' ), __( 'AJAX Optimizer', 'ajax-optimizer' ), 'activate_plugins', AJAX_OPT_SLUG, array(
				$this,
				'render_menu_page',
			) );
		}
	}

	/**
	 * Render plugin page.
	 */
	public function render_menu_page() {
		require_once AJAX_OPT_BASE_PATH . 'admin/views/page.php';
		AJAX_Optimizer_Filesystem::get_instance()->print_request_filesystem_credentials_modal();
	}

	/**
	 * Initialize settings.
	 */
	public function settings_init() {

		// Get settings page hook.
		$hook = $this->plugin_screen_hook_suffix;

		// Register settings.
		register_setting( AJAX_OPT_SLUG, AJAX_OPT_SLUG, array( $this, 'sanitize_settings' ) );

		// General settings section.
		add_settings_section(
			'plugin_conflicts_setting_section',
			__( 'Must Use plugin', 'ajax-optimizer' ),
			array( $this, 'render_settings_create_must_use_plugin' ),
			$hook
		);

		// Choose plugins.
		add_settings_section(
			'plugins',
			__( 'Choose Plugins', 'ajax-optimizer' ),
			array( $this, 'render_settings_plugins' ),
			$hook
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param arr $settings Unsanitized settings.
	 * @return arr $settings Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {
		return $settings;
	}

	/**
	 * Render settings to create Must Use plugin.
	 */
	public function render_settings_create_must_use_plugin() {
		$exists = AJAX_Optimizer_MU_Admin::get_instance()->check_plugin_exists();
		$status = $exists ? esc_html__( 'Exists', 'ajax-optimizer' ) : esc_html__( 'Does not exist', 'ajax-optimizer' );

		require_once AJAX_OPT_BASE_PATH . 'admin/views/setting-create-mu-plugin.php';
	}

	/**
	 * Render setting to select plugins.
	 */
	public function render_settings_plugins() {
		$options = $this->plugin->options();

		if ( isset( $options['plugins']['frontend'] ) && is_array( $options['plugins']['frontend'] ) ) {
			$plugins = $options['plugins']['frontend'];
		} else {
			$plugins = array();
		}

		$all_plugins = get_plugins();

		// Load the template.
		require_once AJAX_OPT_BASE_PATH . 'admin/views/settings-plugins.php';
	}

	/**
	 * Register and enqueue admin-specific scripts.
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( AJAX_OPT_SLUG . '-admin-script', AJAX_OPT_BASE_URL . 'admin/assets/admin.js', array() );
	}

}
