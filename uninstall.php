<?php
/**
 * Delete Must Use plugin and options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' )
	|| dirname( WP_UNINSTALL_PLUGIN ) !== dirname( plugin_basename( __FILE__ ) )
) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'classes/mu-admin.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/utils.php';
require_once plugin_dir_path( __FILE__ ).  'classes/filesystem.php';

AJAX_Optimizer_MU_Admin::get_instance()->delete_mu_plugin_on_uninstall();

function ajax_opt_uninstall_single() {
	delete_option( 'ajax-optimizer' );
}

if ( is_multisite() ) {
	AJAX_Optimizer_Utils::call_for_each_blog( 'ajax_opt_uninstall_single' );
} else {
	ajax_opt_uninstall_single();
}
