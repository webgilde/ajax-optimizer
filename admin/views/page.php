<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php settings_errors(); ?>
		
	<form method="post" action="options.php">
		<?php
		do_settings_sections( $this->plugin_screen_hook_suffix );
		settings_fields( AJAX_OPT_SLUG );
		?><div id="ajax_optimizer_save_unrecommended_notice" class="error inline notice" style="display:none;">
        	<p><?php _e( 'The save button is disabled, until you confirm that you want to use unrecommended settings.', AJAX_OPT_SLUG ) ?></p>
        	<input id="ajax_optimizer_save_unrecommended_cb" type="checkbox"/>
        	<label for="ajax_optimizer_save_unrecommended_cb"><?php _e( 'I am aware of the unrecommended settings, and I still want to save.', AJAX_OPT_SLUG ) ?></label>
        </div><?php 
		submit_button( __( 'Save', 'ajax-optimizer' ), 'primary', 'ajax_optimizer_submit_settings' );
		?>
	</form>
</div>
