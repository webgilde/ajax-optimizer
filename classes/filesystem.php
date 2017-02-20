<?php
/**
 * Filesystem operations.
 */
class AJAX_Optimizer_Filesystem {
	/**
	 * Singleton instance of the class
	 *
	 * @var AJAX_Optimizer_Filesystem
	 */
	protected static $instance;

	/**
	 * Return an instance of AJAX_Optimizer_Filesystem.
	 *
	 * @return AJAX_Optimizer_Filesystem
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
	private function __construct() {}

	/**
	 * Connect to the filesystem.
	 *
	 * @param array $directories                  A list of directories. If any of these do
	 *                                            not exist, a WP_Error object will be returned.
	 * @return bool|WP_Error True if able to connect, false or a WP_Error otherwise.
	 */
	public function fs_connect( $directories = array() ) {
		global $wp_filesystem;
		$directories = ( is_array( $directories ) && count( $directories ) ) ? $directories : array( WP_CONTENT_DIR );

		// This will output a credentials form in event of failure, We don't want that, so just hide with a buffer.
		ob_start();
		$credentials = request_filesystem_credentials( '', '', false, $directories[0] );
		ob_end_clean();

		if ( false === $credentials ) {
			return false;
		}

		if ( ! WP_Filesystem( $credentials ) ) {
			$error = true;
			if ( is_object( $wp_filesystem ) && $wp_filesystem->errors->get_error_code() ) {
				$error = $wp_filesystem->errors;
			}
			// Failed to connect, Error and request again.
			ob_start();
			request_filesystem_credentials( '', '', $error, $directories[0] );
			ob_end_clean();
			return false;
		}

		if ( ! is_object( $wp_filesystem ) ) {
			return new WP_Error( 'fs_unavailable', __( 'Could not access filesystem.', 'ajax-optimizer' ) );
		}

		if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
			return new WP_Error( 'fs_error', __( 'Filesystem error.' ), $wp_filesystem->errors );
		}

		foreach ( (array) $directories as $dir ) {
			switch ( $dir ) {
				case ABSPATH:
					if ( ! $wp_filesystem->abspath() ) {
						return new WP_Error( 'fs_no_root_dir', __( 'Unable to locate WordPress root directory.', 'ajax-optimizer' ) );
					}
					break;
				case WP_CONTENT_DIR:
					if ( ! $wp_filesystem->wp_content_dir() ) {
						return new WP_Error( 'fs_no_content_dir', __( 'Unable to locate WordPress content directory (wp-content).', 'ajax-optimizer' ) );
					}
					break;
				default:
					if ( ! $wp_filesystem->find_folder( $dir ) ) {
						return new WP_Error( 'fs_no_folder', sprintf( __( 'Unable to locate needed folder (%s).', 'ajax-optimizer' ) , esc_html( basename( $dir ) ) ) );
					}
					break;
			}
		}

		return true;
	}

	/**
	 * Replace the 'direct' absolute path with the Filesystem API path. Useful only when the 'direct' method is not used.
	 * Check https://codex.wordpress.org/Filesystem_API for info
	 *
	 * @param string $path Existing path.
	 * @return string Normalized path.
	 */
	public function normalize_path( $path ) {
		global $wp_filesystem;
		return str_replace( ABSPATH, $wp_filesystem->abspath(), $path );
	}

	/**
	 * Print the filesystem credentials modal when needed.
	 */
	public function print_request_filesystem_credentials_modal() {
		if ( function_exists( 'wp_print_request_filesystem_credentials_modal' ) ) {
			return wp_print_request_filesystem_credentials_modal();
		}

		$filesystem_method = get_filesystem_method();
		ob_start();
		$filesystem_credentials_are_stored = request_filesystem_credentials( self_admin_url() );
		ob_end_clean();
		$request_filesystem_credentials = ( 'direct' !== $filesystem_method && ! $filesystem_credentials_are_stored );
		if ( ! $request_filesystem_credentials ) {
			return;
		}
		?>
		<div id="request-filesystem-credentials-dialog" class="notification-dialog-wrap request-filesystem-credentials-dialog">
			<div class="notification-dialog-background"></div>
			<div class="notification-dialog" role="dialog" aria-labelledby="request-filesystem-credentials-title" tabindex="0">
				<div class="request-filesystem-credentials-dialog-content">
					<?php request_filesystem_credentials( site_url() ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
