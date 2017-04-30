<?php defined( 'ABSPATH' ) || exit; ?>
<div id="ajax-optimizer-mu-plugin-status" class="<?php echo $exists ? 'updated' : 'error'; ?> inline notice">
	<p><?php echo $status; ?></p>
</div>
<?php if ( $can_create ) : ?>
<div id="ajax-optimizer-create-mu-plugin-loading" class="spinner is-active" style="display:none; float: none;"></div>
<p>
	<button <?php if ( $exists ) { echo 'style="display:none;"'; } ?> id="ajax_optimizer_create_mu_plugin" data-nonce="<?php echo wp_create_nonce( 'ajax-optimizer-create-mu-plugin' ); ?>" type="button" class="button"><?php esc_html_e( 'Create plugin', 'ajax-optimizer' ); ?></button>
	<button <?php if ( ! $exists ) { echo 'style="display:none;"'; } ?> id="ajax_optimizer_delete_mu_plugin" data-nonce="<?php echo wp_create_nonce( 'ajax-optimizer-delete-mu-plugin' ); ?>" type="button" class="button"><?php esc_html_e( 'Delete plugin', 'ajax-optimizer' ); ?></button>	
</p>
<?php endif; ?>
<p class="description"><?php esc_html_e( 'Must-Use plugin disables selected plugins in AJAX requests initiated by the frontend', 'ajax-optimizer' ); ?></p>
