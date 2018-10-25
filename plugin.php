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

// 	/**
// 	 * Plugin options.
// 	 *
// 	 * @var     array (if loaded)
// 	 */
// 	protected $options;

	protected $global_options;
	protected $action_options = array();
	

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_plugin_textdomain();
		add_filter( AJAX_OPT_ACTIONS_FILTER, array($this, 'addDefaultAction') );
	}
	
	public function addDefaultAction($arr){
	    if (!$arr) $arr = array();
	    $arr['_default'] = array('name' => 'Default Settings', 'description' => 'The default behaviour. This will be completely overriden by the custom actions.');
	    return $arr;
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
// 	public function options() {
// 		if ( ! isset( $this->options ) ) {
// 			$this->options = (array) get_option( AJAX_OPT_SLUG, array() );
// 		}
// 		return $this->options;
// 	}
	public function get_action_option($action_id){
	    if (! isset($this->action_options[$action_id])){
	        //  try to load the options
	        $this->action_options[$action_id] = (array) get_option( AJAX_OPT_SLUG . "_" . $action_id, array() );
	    }
	    return $this->action_options[$action_id];
	}


	/**
	 * Update plugin options.
	 *
	 * @param array $options
	 */
	public function update_options( array $options ) {
		$this->options = $options;
		update_option( AJAX_OPT_SLUG, $options );
	}
	
	public function get_supported_actions(){
	    if (! isset($this->supported_actions)){
	        $this->supported_actions = apply_filters(AJAX_OPT_ACTIONS_FILTER, array());
	        ksort($this->supported_actions);
// 	        echo "TODO HANDLE THIS";
// 	        $options = $this->options();
// 	        $actions = isset($options['actions']) ? $options['actions'] : array();
// 	        foreach ($this->supported_actions as $action_id => $supported_action){
// 	            if (! isset($actions[$action_id])){
// 	                $actions[$action_id] = array();
// 	            }
// 	        }
// 	        $options['actions'] = $actions;
// 	        $this->options = $options;
// 	        ksort($this->options['actions']);
	    }
	    return $this->supported_actions;
	}

	/**
	 * Single site or network wide activation.
	 *
	 * @param bool $network_wide Whether to enable the plugin for all sites in the network
	 *                           or just the current site. Multisite only. Default is false.
	 */
	public static function activation( $network_wide ) {
		AJAX_Optimizer_MU_Admin::get_instance()->create_mu_plugin_on_activation();
	}
}
