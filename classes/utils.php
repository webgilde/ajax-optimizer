<?php
class AJAX_Optimizer_Utils {
	/**
	 * Call a function for each blog in the network.
	 *
	 * @param callable $callback Callback to be called for each blog.
	 */
	public static function call_for_each_blog( $callback ) {
		global $wpdb;

		$network_blogs = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d", $wpdb->siteid ) );

		if ( is_array( $network_blogs ) && $network_blogs !== array() && count( $network_blogs ) <= 100 ) {
			foreach ( $network_blogs as $blog_id ) {
				switch_to_blog( $blog_id );

				if ( is_callable( $callback ) ) {
					call_user_func( $callback );
				}

				restore_current_blog();
			}
		}
	}
}