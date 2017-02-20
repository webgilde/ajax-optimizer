<?php
/**
 * Must Use plugin.
 *
 * There is one global Must Use plugin.
 * This plugin is created automatically on activation, or manually on the Settings page.
 * When Must Use plugin is created, then all other sites in the network will use it.
 */
class AJAX_Optimizer_MU_Admin {
	/**
	 * Singleton instance of the plugin
	 *
	 * @var AJAX_Optimizer_MU_Admin
	 */
	protected static $instance;

	const PLUGIN_NAME = 'ajax-optimizer-mu.php';

	/**
	 * Return an instance of AJAX_Optimizer_MU_Admin
	 *
	 * @return AJAX_Optimizer_MU_Admin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'wp_ajax_ajax_optimizer_create_mu_plugin', array( $this, 'wp_ajax_ajax_optimizer_create_mu_plugin' ) );
		add_action( 'wp_ajax_ajax_optimizer_delete_mu_plugin', array( $this, 'wp_ajax_ajax_optimizer_delete_mu_plugin' ) );

		if ( ! defined( 'WPMU_PLUGIN_DIR' ) ) {
			define( 'WPMU_PLUGIN_DIR', ABSPATH . 'wp-content/mu-plugins' );
		}
	}

	/**
	 * Connect to filesystem.
	 *
	 * @return book true on success
	 *         string on failure
	 */
	private function fs_connect() {
		global $wp_filesystem;

		if ( ! class_exists( 'AJAX_Optimizer_Filesystem' ) ) {
			return 'Unable to connect to the filesystem';
		}

		$fs_connect = AJAX_Optimizer_Filesystem::get_instance()->fs_connect();

		if ( false === $fs_connect || is_wp_error( $fs_connect ) ) {
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$message = esc_html( $wp_filesystem->errors->get_error_message() );
			} else {
				$message = __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'ajax-optimizer' );
			}

			return $message;
		}
		return true;
	}

	/**
	 * Create Must-Use plugin.
	 *
	 * @return array
	 */
	private function create_mu_plugin() {
		global $wp_filesystem;

		$is_cli = defined( 'WP_CLI' ) && WP_CLI;
		if ( ! is_super_admin() && ! $is_cli ) {
			return array( 'success' => false, 'message' => 'You do not have permission' );
		}

		if ( $this->check_plugin_exists() ) {
			return array( 'success' => false, 'message' => 'Plugin exists' );
		}

		$fs_connect = $this->fs_connect();
		if ( true !== $fs_connect ) {
			return array( 'success' => false, 'message' => $fs_connect );
		}

		$mu_dir = AJAX_Optimizer_Filesystem::get_instance()->normalize_path( WPMU_PLUGIN_DIR );
		$mu_dir_exists = $wp_filesystem->is_dir( $mu_dir ) || $wp_filesystem->mkdir( $mu_dir );
		$src_file = AJAX_Optimizer_Filesystem::get_instance()->normalize_path( AJAX_OPT_BASE_PATH ) . '/mu/' . self::PLUGIN_NAME;
		$dst_file = path_join( $mu_dir, self::PLUGIN_NAME );

		if ( ! $mu_dir_exists || ! $wp_filesystem->copy( $src_file, $dst_file, true ) ) {
			return array( 'success' => false, 'message' => __( 'Could not create plugin', 'ajax-optimizer' ) );
		}

		return array( 'success' => true, 'message' => __( 'Plugin created', 'ajax-optimizer' ) );
	}

	/**
	 * Delete Must-Use plugin.
	 */
	private function delete_mu_plugin() {
		global $wp_filesystem;

		$is_cli = defined( 'WP_CLI' ) && WP_CLI;
		if ( ! is_super_admin() && ! $is_cli ) {
			return array( 'success' => false, 'message' => 'You do not have permission' );
		}

		$fs_connect = $this->fs_connect();
		if ( true !== $fs_connect ) {
			return array( 'success' => false, 'message' => $fs_connect );
		}

		$mu_dir = AJAX_Optimizer_Filesystem::get_instance()->normalize_path( WPMU_PLUGIN_DIR );
		$dst_file = path_join( $mu_dir, self::PLUGIN_NAME );

		if ( ! $wp_filesystem->delete( $dst_file ) ) {
			return array( 'success' => false, 'message' => __( 'An error occured', 'ajax-optimizer' ) );
		}

		return array( 'success' => true, 'message' => __( 'Plugin deleted', 'ajax-optimizer' ) );
	}

	/**
	 * AJAX callback to create Must-Use plugin.
	 */
	public function wp_ajax_ajax_optimizer_create_mu_plugin() {
		check_ajax_referer( 'ajax-optimizer-create-mu-plugin', 'nonce' );

		$result = $this->create_mu_plugin();
		wp_send_json( $result );
	}

	/**
	 * AJAX callback to delete Must-Use plugin.
	 */
	public function wp_ajax_ajax_optimizer_delete_mu_plugin() {
		check_ajax_referer( 'ajax-optimizer-delete-mu-plugin', 'nonce' );

		$result = $this->delete_mu_plugin();
		wp_send_json( $result );
	}

	/**
	 * Creates Must-Use plugin on activation.
	 * If 'direct' filesystem method can not be used then plugin will not be created.
	 */
	public function create_mu_plugin_on_activation() {
		static $processed = null;

		// Process once when network activated.
		if ( null === $processed ) {
			$res = $this->create_mu_plugin();
			$processed = true;
		}
	}

	/**
	 * Deletes Must-Use plugin on uninstall.
	 * If 'direct' filesystem method can not be used then plugin will not be deleted.
	 */
	public function delete_mu_plugin_on_uninstall() {
		$this->delete_mu_plugin();
	}

	/**
	 * Checks if plugin exists.
	 */
	public function check_plugin_exists() {
		$name = path_join( WPMU_PLUGIN_DIR, self::PLUGIN_NAME );
		return file_exists( $name ) && is_file( $name ) && is_readable( $name );
	}

}
