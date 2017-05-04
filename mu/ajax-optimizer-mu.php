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

defined( 'ABSPATH' ) || exit;

define( 'AJAX_OPTIMIZER_MU_VERSION', '0.2.0' );

/**
 * Plugin filter.
 */
class AJAX_Optimizer_MU_Plugin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( $this->is_plugin_active() ) {
			add_filter( 'option_active_plugins', array( $this, 'exclude_plugins' ) );
			add_filter( 'site_option_active_sitewide_plugins', array( $this, 'exclude_plugins' ) );
		}
	}

	/**
	 * Check if Ajax Optimizer plugin is active (single site or network wide).
	 *
	 * @return bool
	 */
	private function is_plugin_active() {
		$plugin = 'ajax-optimizer/ajax-optimizer.php';

		if ( ! function_exists( 'is_plugin_inactive' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active( $plugin );
	}

	/**
	 * Exclude plugins.
	 *
	 * @param array $plugins Set of existing plugins.
	 * @return array $plugins New set.
	 */
	public function exclude_plugins( $plugins ) {
		if ( ! is_array( $plugins ) || empty( $plugins ) ) {
			return $plugins;
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return $plugins;
		}


		if ( ! $this->is_frontend_request() ) {
			return $plugins;
		}

		$options = (array) get_option( 'ajax-optimizer', array() );
		
		// get current action
		$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'default';
		
		if ( ! isset( $options['plugins']['frontend'] ) || ! is_array( $options['plugins']['frontend'] ) ) {
			return $plugins;
		}
		
		// use "default" if current action has no own settings
		if( !isset( $options['plugins']['frontend'][$current_action] ) || ! is_array( $options['plugins']['frontend'][$current_action] ) ){
			if( isset( $options['plugins']['frontend']['default'] ) ){
				$disabled_plugins = $options['plugins']['frontend']['default'];
			} else {
				return $plugins;
			}
		} else {
			$disabled_plugins = $options['plugins']['frontend'][$current_action];
		}

		// Blog-active plugins.
		if ( 'option_active_plugins' === current_filter() ) {
			foreach ( $plugins as $_key => $_name ) {
				if ( isset( $disabled_plugins[ $_name ] ) ) {
					unset( $plugins[ $_key ] );
				}
			}
		} else {
			// Network-active plugins.
			foreach ( $plugins as $_name  => $timestamp ) {
				if ( isset( $disabled_plugins[ $_name ] ) ) {
					unset( $plugins[ $_name ] );
				}
			}
		}

		return $plugins;
	}

	/**
	 * Determines whether the AJAX request was initiated by the frontend.
	 *
	 * @return bool
	 */
	private function is_frontend_request() {
		$script_filename = isset( $_SERVER['SCRIPT_FILENAME'] ) ? basename( $_SERVER['SCRIPT_FILENAME'] ) : '';
		$referer = '';

		if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
			$referer = wp_unslash( $_REQUEST['_wp_http_referer'] );
		} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
			$referer = wp_unslash( $_SERVER['HTTP_REFERER'] );
		}

		$result = ( false === strpos( $referer, admin_url() ) ) && 'admin-ajax.php' === $script_filename;

		return $result;
	}
}

new AJAX_Optimizer_MU_Plugin();
