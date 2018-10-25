<?php
/**
 * Plugin Name:     AJAX Optimizer Must Use
 * Description:     Disables selected plugins in AJAX requests initiated by the frontend
 * Version:         0.2.0
 * Author:          Thomas Maier
 * Author URI:      http://webgilde.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * This file belongs to the AJAX Optimizer plugin.
 * You can remove it if you no longer use that plugin.
 * 
 * Find more information in wp-content/plugins/ajax-optimizer/readme.txt
 * 
 */
defined('ABSPATH') || exit();

define('AJAX_OPTIMIZER_MU_VERSION', '0.2.0');

/**
 * Plugin filter.
 */
class AJAX_Optimizer_MU_Plugin
{
    private $buffered_results = array();
    /**
     * Constructor.
     */
    public function __construct()
    {
        if ($this->is_plugin_active()) {
            add_filter('option_active_plugins', array(
                $this,
                'exclude_plugins'
            ));
            add_filter('site_option_active_sitewide_plugins', array(
                $this,
                'exclude_plugins'
            ));
        }
    }

    /**
     * Check if Ajax Optimizer plugin is active (single site or network wide).
     *
     * @return bool
     */
    private function is_plugin_active()
    {
        $plugin = 'ajax-optimizer/ajax-optimizer.php';
        
        if (! function_exists('is_plugin_inactive')) {
            require_once (ABSPATH . 'wp-admin/includes/plugin.php');
        }
        
        return is_plugin_active($plugin);
    }
    
    /**
     * Exclude plugins.
     *
     * @param array $plugins
     *            Set of existing plugins.
     * @return array $plugins New set.
     */
    public function exclude_plugins($plugins)
    {
        if (! is_array($plugins) || empty($plugins)) {
            return $plugins;
        }
        
        if (! defined('DOING_AJAX') || ! DOING_AJAX) {
            return $plugins;
        }
        
        unset($plugins['ajax-optimizer/ajax-optimizer.php']);
        $action_id = isset($_REQUEST['action']) ? $_REQUEST['action'] : '_default';
        return $this->apply_action($action_id, $plugins);
    }

    /**
     * removes all the disabled plugins for the action specified with action_id
     * @param array $action
     * @return array the plugins_to_disable array for chaining
     */
    private function apply_action($action_id, &$all_plugins)
    {
        if (isset($this->buffered_results[$action_id])){
            return $this->buffered_results[$action_id];
        }
            
        $action = (array) get_option('ajax-optimizer_' . $action_id, array());
        if (! $action) {
            if ($action_id === '_default'){
                return $all_plugins;
            }
            return $this->apply_action('_default', $all_plugins);

//             return $all_plugins;
        }
        
        $plugins_to_disable = array();
        if (isset($action['plugins'])) {
            $plugin_options = $action['plugins'];
            foreach ($all_plugins as $plugin_path){
                if (isset ($plugin_options[$plugin_path])){
                    $opts = $plugin_options[$plugin_path];
                    if ($opts && isset($opts['status'])) 
                        $option_status = $opts['status'];
                }
                if (! $option_status || $option_status === ''){
                    if ($action_id === '_default'){
                        $option_status = 'active';
                    }
                    else{
                        $option_status = 'inactive';
                    }
                }
                if ($option_status === 'inactive') {
                    $plugins_to_disable[$plugin_path] = true;
                }
            }
        }
        foreach ($all_plugins as $key => $path) {
            if (isset($plugins_to_disable[$path])) {
                unset($all_plugins[$key]);
            }
        }
        $this->buffered_results[$action_id] = $all_plugins;
        return $all_plugins;
    }

    /**
     * Determines whether the AJAX request was initiated by the frontend.
     *
     * @return bool
     */
    private function is_frontend_request()
    {
        $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
        $referer = '';
        
        if (! empty($_REQUEST['_wp_http_referer'])) {
            $referer = wp_unslash($_REQUEST['_wp_http_referer']);
        } elseif (! empty($_SERVER['HTTP_REFERER'])) {
            $referer = wp_unslash($_SERVER['HTTP_REFERER']);
        }
        
        $result = (false === strpos($referer, admin_url())) && 'admin-ajax.php' === $script_filename;
        
        return $result;
    }
}

new AJAX_Optimizer_MU_Plugin();
