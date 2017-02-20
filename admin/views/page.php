<?php defined( 'ABSPATH' ) || exit; ?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php settings_errors(); ?>
		
	<form method="post" action="options.php">
		<?php
		do_settings_sections( $this->plugin_screen_hook_suffix );
		settings_fields( AJAX_OPT_SLUG );
		submit_button( __( 'Save', 'ajax-optimizer' ) );
		?>
	</form>
</div>
