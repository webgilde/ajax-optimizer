<?php 
if (isset($action['description'])):?>
<p class="description"><?php echo $action['description'] ?></p>
<p class="description"><?php esc_html_e( 'Choose the plugins which will be enabled for AJAX calls.', 'ajax-optimizer' ); ?></p>
<a href="javascript:void(0);" onClick="javascript:ajax_optimizer_admin.set_to_default('<?php echo $action_id?>');">Set to default</a>
<?php endif;?>
<table class="widefat plugins">
	<thead>
		<?php require AJAX_OPT_BASE_PATH . 'admin/views/plugin-columns.php'; ?>
	</thead>
	<tbody>
	<?php
	$plugins = isset($targetOption['plugins']) ? $targetOption['plugins'] : array();
	foreach ( $all_plugins as $plugin_path => $plugin ):
	    $rec_status = null;
	    $rec_description = null;
	    
	    $status = isset($plugins[$plugin_path]) && isset($plugins[$plugin_path]['status']) ? $plugins[$plugin_path]['status'] : null;
		$recommendation = isset($recommendations[$plugin_path]) ? $recommendations[$plugin_path] : null;
		if ($recommendation){
		    $rec_status = isset($recommendation['status']) ? $recommendation['status'] : null;
		    $rec_description = isset($recommendation['description']) ? $recommendation['description'] : null;
		}
		else{
		    if ($action_id === "_default"){
		        $rec_status = "active";
		    }
		    else{
		        $rec_status = "inactive";
		    }
		}
		
		
		
		if ($rec_status){
		    if ($rec_status !== $status)
    		    $rec = "<strong>" . $rec_status . "</strong>";
		    else 
		        $rec = $rec_status;
		    if ($rec_description){
		        $rec .= "<br/>" . $rec_description;
		    }
		}
		else $rec = 'No recommendation info';
		
		if (! $status){
		    if ($rec_status){
		        $status = $rec_status;
		    }
		    else{
		        $status = $action_id === '_default' ? 'active' : 'inactive';
		    }
		}
		
		$html_id = "ajax_optimizer_" . $action_id . "_" . $plugin_path;
		?>
		<tr class="<?php echo $status; ?>">
			<th class="check-column"></th>
			<td><?php echo $plugin['Name']; ?></td>
			<td>
			<input id="<?php echo $html_id?>"type="checkbox" <?php checked( $status, 'active' ); ?> onchange="ajax_optimizer_admin.set_checked(this);"> 
			<input id="ajax_optimizer_status_<?php echo $html_id?>" type="hidden" name="<?php echo AJAX_OPT_SLUG . '_' . $action_id; ?>[plugins][<?php echo $plugin_path; ?>][status]" value="<?php echo $status?>">
			</td>
			<td><?php echo $rec ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<?php require_once AJAX_OPT_BASE_PATH . 'admin/views/plugin-columns.php'; ?>
	</tfoot>
</table>