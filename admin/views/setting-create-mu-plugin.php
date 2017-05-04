<?php defined( 'ABSPATH' ) || exit; ?>
<div id="ajax-optimizer-mu-plugin-status" class="<?php echo $exists ? 'updated' : 'error'; ?> inline notice">
	<p><?php echo $status; ?></p>
</div>
<?php if ( $can_create ) : ?>
<div id="ajax-optimizer-create-mu-plugin-loading" class="spinner is-active" style="display:none; float: none;"></div>
<p>
	<button <?php if ( $exists ) { echo 'style="display:none;"'; } ?> id="ajax_optimizer_create_mu_plugin" data-nonce="<?php echo wp_create_nonce( 'ajax-optimizer-create-mu-plugin' ); ?>" type="button" class="button"><?php esc_html_e( 'Activate Optimizer', 'ajax-optimizer' ); ?></button>
	<button <?php if ( ! $exists ) { echo 'style="display:none;"'; } ?> id="ajax_optimizer_delete_mu_plugin" data-nonce="<?php echo wp_create_nonce( 'ajax-optimizer-delete-mu-plugin' ); ?>" type="button" class="button"><?php esc_html_e( 'Disable Optimizer', 'ajax-optimizer' ); ?></button>	
</p>
<?php endif; ?>
<p class="description"><?php _e( 'AJAX Optimizer creates a must-use plugin in <code>wp-content/mu-plugins</code>. Use the button above to create or remove it manually in order to start or stop disabling plugins for AJAX calls.', 'ajax-optimizer' ); ?></p>
