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
		// Add assets.
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
		$this->plugin_screen_hook_suffix = add_plugins_page( __( 'AJAX Optimizer', 'ajax-optimizer' ), __( 'AJAX Optimizer', 'ajax-optimizer' ), 'activate_plugins', AJAX_OPT_SLUG, array(
			$this,
			'render_menu_page',
		) );
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

		// Must Use plugin settings section.
		add_settings_section(
			'plugin_conflicts_setting_section',
			__( 'Initiate AJAX Optimizer', 'ajax-optimizer' ),
			array( $this, 'render_settings_create_must_use_plugin' ),
			$hook
		);

		// The globally valid settings
		$settings_id = AJAX_OPT_SLUG . '_settings';
		$field_id = $settings_id . '_field';
		add_settings_section(
		    $settings_id,
		    __( 'AJAX Optimizer Settings', 'ajax-optimizer' ),
		    array( $this, 'render_settings_global' ),
		    $hook
		    );
		add_settings_field(
		    $field_id,
		    null,
		    array( $this, 'render_void'),
		    $hook,
		    $settings_id);
		register_setting(AJAX_OPT_SLUG, $settings_id, array( $this, 'sanitize_settings_global' ));
		
		// Settings per action
		$supported_actions = $this->plugin->get_supported_actions();
		foreach ($supported_actions as $action_id => $action){
		    $settings_id = AJAX_OPT_SLUG . '_' . $action_id;
		    $field_id = $settings_id . '_field';
		    $title = isset($action['name']) ? $action['name'] : $action_id;//__( 'Disable Plugins By Action', 'ajax-optimizer' );
		    // The new action based settings
		    add_settings_section(
		        $settings_id,
		        $title,
		        array( $this, 'render_settings_actions'),
		        $hook
		        );
		    
		    add_settings_field(
		        $field_id, 
		        null, 
		        array( $this, 'render_void'),
		        $hook, 
		        $settings_id);
		    register_setting(AJAX_OPT_SLUG, $settings_id, array( $this, 'sanitize_settings_action' ));
		}
	}

	/**
	 * Sanitize settings for actions.
	 *
	 * @param arr $settings Unsanitized settings.
	 * @return arr $settings Sanitized settings.
	 */
	public function sanitize_settings_action( $settings ) {
		return $settings;
	}
	
	/**
	 * Sanitize global settings
	 *
	 * @param arr $settings Unsanitized settings.
	 * @return arr $settings Sanitized settings.
	 */
	public function sanitize_settings_global( $settings ) {
	    return $settings;
	}

	/**
	 * Render settings to create Must Use plugin.
	 */
	public function render_settings_create_must_use_plugin() {
		$exists = AJAX_Optimizer_MU_Admin::get_instance()->check_plugin_exists();
		$status = $exists ? esc_html__( 'AJAX Optimizer is activated', 'ajax-optimizer' ) : esc_html__( 'AJAX Optimizer is NOT activated', 'ajax-optimizer' );
		$can_create = is_super_admin() && is_main_site();

		require_once AJAX_OPT_BASE_PATH . 'admin/views/setting-create-mu-plugin.php';
	}

	/**
	 * Render the settings to pick plugins to exclude for specific actions
	 */
	public function render_settings_global() {
	    $all_plugins = get_plugins();
	    unset( $all_plugins[ AJAX_OPT_BASE_NAME ] );
	    $all_plugin_paths = [];
	    foreach ($all_plugins as $plugin_path => $plugin){
	        $all_plugin_paths[] = $plugin_path;
	    }
	    
	    $supported_actions = $this->plugin->get_supported_actions();
	    $action_ids = [];
	    $recs = [];
	    foreach ($supported_actions as $action_id => $action){
	        $action_ids[] = $action_id;
	        $recommendations = isset($action['recommendations']) ? $action['recommendations'] : array();
	        $recs_for_action = array();
	        foreach ($recommendations as $plugin_id => $r){
	            $status = isset($r['status']) ? $r['status'] :
	                       $plugin_id === '_default' ? 'active' : 'inactive';
                $recs_for_action[$plugin_id] = $status;
	        }
	        $recs[$action_id] = $recs_for_action;
	    }
	    // Load the template.
	    require_once AJAX_OPT_BASE_PATH . 'admin/views/settings-global.php';
	}
	
	
	public function render_void() {
	    
	}
	/**
	 * Render the settings to pick plugins to exclude for specific actions
	 */
	public function render_settings_actions($arr) {
	    $action_id = substr($arr['id'], 15);
	    $options = $this->plugin->get_action_option($action_id);
	    
	    $supported_actions = $this->plugin->get_supported_actions();
	    $action = $supported_actions[$action_id];
	    
	    $recommendations = isset($action['recommendations']) ? $action['recommendations'] : array();

	    $targetOption = $options; 
	    $all_plugins = get_plugins();
	    unset( $all_plugins[ AJAX_OPT_BASE_NAME ] );
	    
	    // Load the template.
	    require AJAX_OPT_BASE_PATH . 'admin/views/settings-actions.php';
	}
	
	
	/**
	 * Register and enqueue admin-specific scripts.
	 */
	public function enqueue_admin_scripts() {
	    $handle = AJAX_OPT_SLUG . '-admin-script';
	    $translation = array(
	        'disable_mu' => __( 'Disable Optimizer', AJAX_OPT_SLUG),
	        'enable_mu' => __( 'Activate Optimizer', AJAX_OPT_SLUG ),
	        'enable_mu2' => __( 'Yes, I want to activate the optimzer.', AJAX_OPT_SLUG ),
	        'enable_mu_confirm_title' => __( 'Confirm the activation of the optimizer.', AJAX_OPT_SLUG ),
	        'enable_mu_confirm_text' => __( 'TODO: Add some text', AJAX_OPT_SLUG ),
	        'enable_mu_checkbox' => __( 'I undestand the risks and know what I am doing.', AJAX_OPT_SLUG ),
	    );
	    wp_register_script( $handle, AJAX_OPT_BASE_URL . 'admin/assets/admin.js' );
	    wp_localize_script( $handle, 'ajax_optimizer_translation', $translation );
	    wp_enqueue_script($handle);
	}

}
