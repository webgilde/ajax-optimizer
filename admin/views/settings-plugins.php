<?php 
defined( 'ABSPATH' ) || exit;

if ( is_array( $all_plugins ) && count( $all_plugins ) ) : ?>

	<p class="description"><?php esc_html_e( 'Choose the plugins which will be disabled for all AJAX calls in the frontend', 'ajax-optimizer' ); ?></p>

	<table class="widefat plugins">
		<thead>
			<?php require_once AJAX_OPT_BASE_PATH . 'admin/views/plugin-columns.php'; ?>
		</thead>
		<tbody>
		<?php
		foreach ( $all_plugins as $_plugin_path => $_plugin ) :
			$status = ( isset( $plugins[ $_plugin_path ] ) && 'inactive' === $plugins[ $_plugin_path ] ) ? 'inactive' : 'active';
			?>
			<tr class="<?php echo $status; ?>">
				<th class="check-column"></th>
				<td><?php echo $_plugin['Name']; ?></td>
				<td><input type="checkbox" name="<?php echo AJAX_OPT_SLUG; ?>[plugins][frontend][default][<?php echo $_plugin_path; ?>]" value="inactive" <?php checked( $status, 'inactive' ); ?>></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php require_once AJAX_OPT_BASE_PATH . 'admin/views/plugin-columns.php'; ?>
		</tfoot>
	</table>
<?php else : ?>
	<p class="description"><?php esc_html_e( 'There needs to be at least one plugin selected.', 'ajax-optimizer' ); ?></p>
<?php endif; ?>
