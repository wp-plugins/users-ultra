<?php
global $xoouserultra;
	
?>

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Synchronize Already Users','xoousers'); ?></h3>
        <p><?php _e('This feature allows you to synchronize the alreay users in you Wordpress website so they can be recognized by Users Ultra ','xoousers'); ?></p>
        
        
        <p class="submit">
	<input type="submit" name="submit" id="uultra-btn-sync-btn" class="button button-primary " value="<?php _e('Start Sync Now','xoousers'); ?>"  />
	
       </p>
       
       <p id='uultra-sync-results'>
       
       </p>
                     
       
    
</div>  


 <div class="user-ultra-sect ">
        
        <h3><?php _e('Woocommerce Synchronize','xoousers'); ?></h3>
        <p><?php _e('This feature allows you to sync Woocommerce user profiles and let them manage their personal information through Users Ultra'); ?></p>
        
        
        <p class="submit">
	<input type="submit" name="submit" id="uultra-btn-sync-woo-btn" class="button button-primary " value="<?php _e('Start Woocommerce Sync Now','xoousers'); ?>"  />
	
       </p>
       
       <p id='uultra-sync-woo-results'>
       
       </p>
                     
       
    
</div> 

<script>
var message_sync_users = "<?php echo _e('Please wait, this process may take several minutes','xoousers')?>"
</script>
