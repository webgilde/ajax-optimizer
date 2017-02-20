<?php
/**
 * Plugin Name:     AJAX Optimizer Must Use
 * Description:     Disables selected plugins in AJAX requests initiated by the frontend
 * Version:         0.1.0
 * Author:          Thomas Maier
 * Author URI:      http://webgilde.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit;

define( 'AJAX_OPTIMIZER_MU_VERSION', '0.1.0' );

/**
 * Plugin filter.
 */
class AJAX_Optimizer_MU_Plugin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'option_active_plugins', array( $this, 'exclude_plugins' ) );
		add_filter( 'site_option_active_sitewide_plugins', array( $this, 'exclude_plugins' ) );
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

		$options = get_option( 'ajax-optimizer' );
		if ( ! isset( $options['plugins']['frontend'] ) || ! is_array( $options['plugins']['frontend'] ) ) {
			return $plugins;
		}

		// Blog-active plugins.
		if ( 'option_active_plugins' === current_filter() ) {
			foreach ( $plugins as $_key => $_name ) {
				if ( isset( $options['plugins']['frontend'][ $_name ] ) ) {
					unset( $plugins[ $_key ] );
				}
			}
		} else {
			// Network-active plugins.
			foreach ( $plugins as $_name  => $timestamp ) {
				if ( isset( $options['plugins']['frontend'][ $_name ] ) ) {
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
