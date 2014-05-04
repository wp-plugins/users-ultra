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
        
        <h3><?php _e('Import Users','xoousers'); ?></h3>
        <p><?php _e('This feature lets you import users easily into the Users Ultra System. The file must be CSV format. Delimited Comma-separated Values ','xoousers'); ?></p>
        
        <p><?php _e('<b>IMPORTANT: </b> The CSV format should be:  "user name", "email", "display name", "registration date", "first name", "last name", "age", "country"  ','xoousers'); ?></p>
        
                
       
   <form action=""  name="uultra-form-cvs-form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="uultra-form-cvs-form-conf" />
                   
          
           <p class="submit">
	<input type="file" name="file_csv" class="" value="<?php _e('Chose File','xoousers'); ?>"  /><?php _e(' <b>ONLY CSV EXTENSIONS ALLOWED: </b>  ','xoousers'); ?>
	
     </p>
       
     <h4>Account Activation:</h4>
       
     <p>
       <input name="uultra-send-welcome-email" type="checkbox" id="uultra-send-welcome-email" value="1" checked="checked" /> Send welcome email with new password. <br />
         
         
          <label>
           <input name="uultra-activate-account" type="radio" id="RadioGroup1_1" value="active" checked="checked" />
           Activate account automatically.</label>
       
    <br />
       
       
 <input type="radio" name="uultra-activate-account" value="pending" id="RadioGroup1_0" />
           Send Activation Link.</label>
          <strong> PLEASE NOTE</strong>: the account status will be &quot;pending&quot; until the user clicks on the activation link.<br />
     </p>
       
     <p class="submit">
	<input type="submit" name="submit"  class="button button-primary " value="<?php _e('Start Importing','xoousers'); ?>"  />
	
       </p>
       
        <p>
        
        <?php echo $xoouserultra->userpanel->messages_process;?>
	    </p>
       
       
            
             
         </form>
    
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
