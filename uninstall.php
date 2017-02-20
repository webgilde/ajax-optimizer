<?php
/**
 * Delete Must Use plugin and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' )
	|| dirname( WP_UNINSTALL_PLUGIN ) !== dirname( plugin_basename( __FILE__ ) )
) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'classes/mu-plugin.php';
AJAX_Optimizer_MU_Admin::get_instance()->delete_mu_plugin_on_uninstall();

delete_option( 'ajax-optimizer' );
