<?php
/**
 * Plugin Name:     AJAX Optimizer
 * Description:     Allows to disable selected plugins in AJAX requests initiated by the frontend
 * Version:         0.2
 * Author:          Thomas Maier
 * Author URI:      http://webgilde.com
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     ajax-optimizer
 * Domain Path:     /languages
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'AJAX_OPT_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AJAX_OPT_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'AJAX_OPT_SLUG', 'ajax-optimizer' );
define( 'AJAX_OPT_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'AJAX_OPT_BASE_DIR', dirname( AJAX_OPT_BASE_NAME ) );
define( 'AJAX_OPT_VERSION', '0.2' );

require_once AJAX_OPT_BASE_PATH . 'plugin.php';
require_once AJAX_OPT_BASE_PATH . 'classes/mu-admin.php';
require_once AJAX_OPT_BASE_PATH . 'classes/filesystem.php';

if ( is_admin() ) {
	require_once AJAX_OPT_BASE_PATH . 'admin/admin.php';
	new AJAX_Optimizer_Admin;
	AJAX_Optimizer_MU_Admin::get_instance();
}

register_activation_hook( __FILE__, array( 'AJAX_Optimizer', 'activation' ) );
