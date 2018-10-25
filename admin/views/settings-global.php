These are the global settings. Todo: Finish me!
<?php if (count($supported_actions) > 0):?>
    <br/>Jump To
    <?php foreach ($supported_actions as $action_id => $action):
        $displayName = isset($action['name']) ? $action['name'] : $action_id;?>
    	<a href="#<?php echo $action_id?>"><?php echo $displayName?></a> | 
    <?php endforeach;?>
<?php endif;?>
<script>
ajax_optimizer_admin.recommendations = <?php echo json_encode($recs)?>;
ajax_optimizer_admin.action_ids = <?php echo json_encode($action_ids)?>;
ajax_optimizer_admin.plugin_paths = <?php echo json_encode($all_plugin_paths)?>;
</script>