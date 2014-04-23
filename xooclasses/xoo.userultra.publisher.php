<?php
class XooPublisher
{
	var $act_message;
	var $errors;
	
	
	function __construct() 
	{		
		
		$this->mDateToday =  date("Y-m-d"); 
		
		if(isset($_POST['uultra-conf-publisher-post']))
		{
			//add new post
			$this->submit_post();
		
		}
		
		if(isset($_POST['uultra-conf-edition-post']))
		{
			//edit post
			$this->edit_conf_post();
		
		}
		
		
		
	}
	
	//edit_post
	
	function edit_post($id)		
	{
		global $wpdb, $current_user, $xoouserultra;	
		
	
		require_once(ABSPATH . 'wp-includes/general-template.php');
		
		$user_id = get_current_user_id();	
		
		
		
		$res = $wpdb->get_results( 'SELECT `ID`, `post_author`, `post_date`, `post_title`, `post_content` , `post_status` FROM ' . $wpdb->prefix . 'posts WHERE `post_author` = "' . $user_id. '" AND  `ID` = "'.$id.'" AND (`post_status` = "publish" OR `post_status` = "pending" ) ORDER BY `post_date` DESC' );
		
		
		if ( !empty( $res ) )
		{
			foreach($res as $rc)
			{
				$post = $rc;
			
			}
			
			
		}else{
			
			//not valid post
			
			$this->errors = __('Invalid Post', 'xoousers');
			
			
		
		}
		
		$post_tags = wp_get_post_tags( $post->ID );
		
		$tagsarray = array();
		
		foreach ($post_tags as $tag) {
            $tagsarray[] = $tag->name;
        }
		
        $tagslist = implode( ', ', $tagsarray );
		
		$categories = get_the_category( $post->ID );
		
		
		$cat_type = 'normal';
		$exclude ="";
		
		$editor_id = 'uultra_post_content';
		$content = '';
		
		$html = "";		
		
		
		
		 ?> 
         
         
         <div class="commons-panel xoousersultra-shadow-borers" >
         
         
                  <div class="commons-panel-heading">
                             <h2> <?php echo  __('Edit Post','xoousers');?> </h2>
                                    
                   </div>
                   
                   
            <div class="commons-panel-content" >  
         
                           
             
                 <div class="uultra-post-publish">
                 
                 <?php
                 
				 if($this->errors!="")
				 {
					 
					  echo "<div class='uupublic-ultra-error'>".$this->errors."</div>";
					  
				 }else{
					 
					 if($this->act_message!="")
					 {
						 echo "<div class='uupublic-ultra-success'>".$this->act_message."</div>";	 
						 
					 }
				 ?>
                 
                 <form method="post" name="uultra-front-publisher-post">
                 <input type="hidden" name="uultra-conf-edition-post" value="ok" />
                 
                 <input type="hidden" name="post_id" value="<?php echo $id?>" />
                 
                 <div class="tablenav_post">
                
                <p><a class="uultra-btn-commm" href="?module=posts" title="<?php echo __('Back','xoousers')?>" ><span><i class="fa fa-angle-double-left  fa-lg"></i></span> Back </a></p>
					                    
                                        
				</div>
                 
                     <div class="field_row">
                         <p><?php echo __('Title:','xoousers')?></p>
                         <p><input name="uultra_post_title" type="text" class="xoouserultra-input" value="<?php echo $post->post_title?>" /></p>
                     
                     </div>
                     
                     <div class="field_row">
                       <p><?php echo __('Category:','xoousers')?></p>
                     
                      <p>
               
                 <?php 
				 
				 
				 $cats = get_the_category( $post->ID );
                 $selected = 0;
                 if ( $cats )
				 {
                       $selected = $cats[0]->term_id;
                 }
							
                   
                                    if ( $cat_type == 'normal' ) {
                                        wp_dropdown_categories( 'show_option_none=' . __( '-- Select --', 'wpuf' ) . '&hierarchical=1&hide_empty=0&orderby=name&name=category[]&id=cat&show_count=0&title_li=&use_desc_for_title=1&class=xoouserultra-input requiredField&exclude=' . $exclude . '&selected=' . $selected );
                                    } else if ( $cat_type == 'ajax' ) {
                                        wp_dropdown_categories( 'show_option_none=' . __( '-- Select --', 'wpuf' ) . '&hierarchical=1&hide_empty=0&orderby=name&name=category[]&id=cat-ajax&show_count=0&title_li=&use_desc_for_title=1&class=xoouserultra-input requiredField&depth=1&exclude=' . $exclude . '&selected=' . $selected );
                                    } else {
                                        wpuf_category_checklist( $curpost->ID, false, 'category', $exclude);
                                    }
                                   
                
                ?>
                 </p>
                 </div>
                 
                  <p><?php echo __('Description:','xoousers')?></p>
                 
                <?php       
				
				$editor_settings = array('media_buttons' => false); 
				
				$content = $post->post_content;     
                
                 wp_editor( $content, $editor_id , $editor_settings);
                
                ?>
                
                   <div class="field_row">
                     <p><?php echo __('Tags:','xoousers')?></p>
                     <p><input name="uultra_post_tags" type="text" class="xoouserultra-input" value="<?php echo $tagslist?>" /></p>
                 
                 </div>
                
                <div class="field_row">
                    
                     <p><input type="submit" name="xoouserultra-submit-post"  class="xoouserultra-button" value="Update"></p>
                 
                 </div>
                 
                 </form>
                 
                 
                 <?php } //end error?>
                
                </div>
            
            
            </div>
        
        
         </div> <!--  End post wrapper-->
        
        <?php 
		
		//return $html;
	
	}

	
	
	function show_front_publisher($atts)		
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/general-template.php');	
		
		$cat_type = 'normal';
		$exclude ="";
		
		$editor_id = 'uultra_post_content';
		$content = '';
		
		$html = "";		
		
		
		
		 ?> 
         
         
         <div class="commons-panel xoousersultra-shadow-borers" >
         
         
                  <div class="commons-panel-heading">
                             <h2> <?php echo  __('Add New Post','xoousers');?> </h2>
                                    
                   </div>
                   
                   
            <div class="commons-panel-content" >  
         
                           
             
                 <div class="uultra-post-publish">
                 
                 <?php
                 
				 if($this->act_message!="")
				 {
					 echo "<div class='uupublic-ultra-success'>".$this->act_message."</div>";
					 
					 
				}
				 ?>
                 
                 <form method="post" name="uultra-front-publisher-post">
                 <input type="hidden" name="uultra-conf-publisher-post" value="ok" />
                 
                 <div class="tablenav_post">
                
                <p><a class="uultra-btn-commm" href="?module=posts" title="<?php echo __('Add New Post','xoousers')?>" ><span><i class="fa fa-angle-double-left  fa-lg"></i></span> Back </a></p>
					                    
                                        
				</div>
                 
                     <div class="field_row">
                         <p><?php echo __('Title:','xoousers')?></p>
                         <p><input name="uultra_post_title" type="text" class="xoouserultra-input" /></p>
                     
                     </div>
                     
                     <div class="field_row">
                       <p><?php echo __('Category:','xoousers')?></p>
                     
                      <p>
               
                 <?php 
                                               
                                               
                                                if ( $cat_type == 'normal' ) {
                                                    wp_dropdown_categories( 'show_option_none=' . __( '-- Select --', 'xoousers' ) . '&hierarchical=1&hide_empty=0&orderby=name&name=category[]&id=cat&show_count=0&title_li=&use_desc_for_title=1&class=xoouserultra-input requiredField&exclude=' . $exclude );
                                                } else if ( $cat_type == 'ajax' ) {
                                                    wp_dropdown_categories( 'show_option_none=' . __( '-- Select --', 'xoousers' ) . '&hierarchical=1&hide_empty=0&orderby=name&name=category[]&id=cat-ajax&show_count=0&title_li=&use_desc_for_title=1&class=cat requiredField&depth=1&exclude=' . $exclude );
                                                } else {
                                                    wpuf_category_checklist(0, false, 'category', $exclude);
                                                }
                                              
                
                ?>
                 </p>
                 </div>
                 
                  <p><?php echo __('Description:','xoousers')?></p>
                 
                <?php       
				
				$editor_settings = array('media_buttons' => false);      
                
                 wp_editor( $content, $editor_id , $editor_settings);
                
                ?>
                
                   <div class="field_row">
                     <p><?php echo __('Tags:','xoousers')?></p>
                     <p><input name="uultra_post_tags" type="text" class="xoouserultra-input" /></p>
                 
                 </div>
                
                <div class="field_row">
                    
                     <p><input type="submit" name="xoouserultra-submit-post"  class="xoouserultra-button" value="Submit"></p>
                 
                 </div>
                 
                 </form>
                 
                
                </div>
            
            
            </div>
        
        
         </div> <!--  End post wrapper-->
        
        <?php 
		
		//return $html;
	
	}
	
	
	/**
	 * My Posts 
	 */
	function show_my_posts()
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
	
		$msgs = $wpdb->get_results( 'SELECT `ID`, `post_author`, `post_date`, `post_title`, `post_content` , `post_status` FROM ' . $wpdb->prefix . 'posts WHERE `post_author` = "' . $user_id. '" AND (`post_status` ="publish" OR `post_status` ="pending" ) ORDER BY `post_date` DESC' );
		
		
		echo '<div class="tablenav_post">
                
                <p><a class="uultra-btn-commm" href="?module=posts&act=add" title="'. __('Add New Post','xoousers').'" ><span><i class="fa fa-plus  fa-lg"></i></span> '.__('Add New Post','xoousers').' </a></p>
					                    
				</div>';

		if ( !empty( $status ) )
		{
			echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			echo '<p>', __( 'You have no posts.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $msgs );
			
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                       
						<th class="manage-column" ><?php _e( 'Title', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Date', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Status', 'xoousers' ); ?></th>
                        <th class="manage-column" ><?php _e( 'Actions', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							
							?>
						<tr>
							                         
                            
							<td><?php echo $msg->post_title; ?></td>
							<td> <?php echo $msg->post_date; ?></td>
							<td><?php echo $msg->post_status; ?></td>
                            
                            <td><a href="?module=posts&act=edit&post_id=<?php echo $msg->ID; ?>" title="<?php echo __('Edit','xoousers')?>" ><span><i class="fa fa-pencil-square-o fa-lg"></i></span> </a></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}
	
	function uultra_clean_tags( $string )
	{
		$string = preg_replace( '/\s*,\s*/', ',', rtrim( trim( $string ), ' ,' ) );
    	return $string;
	}
	
	
	
	function edit_conf_post()
	{
       global $wpdb, $xoouserultra;
	   
	   require_once(ABSPATH . 'wp-includes/pluggable.php');

        $errors = array();

        //if there is some attachement, validate them
        if ( !empty( $_FILES['wpuf_post_attachments'] ) )
		{
        }

        $title = trim( $_POST['uultra_post_title'] );
        $content = trim( $_POST['uultra_post_content'] );

        $tags = '';
        if ( isset( $_POST['uultra_post_tags'] ) )
		{
            $tags = $this->uultra_clean_tags( $_POST['uultra_post_tags'] );
        }

        //validate title
        if ( empty( $title ) ) 
		{
            $errors[] = __( 'Empty post title', 'xoousers' );
			
        } else {
            $title = trim( strip_tags( $title ) );
        }
		
		$cat_type = 'normal';

        //validate cat
     		
			
            if ( !isset( $_POST['category'] ) ) 
			{
				
                $errors[] = __( 'Please choose a category', 'xoousers' );
				
            } else if ( $cat_type == 'normal' && $_POST['category'][0] == '-1' ) {
				
                $errors[] = __( 'Please choose a category', 'xoousers' );
				
            } else {
				
                if ( count( $_POST['category'] ) < 1 ) {
                    $errors[] = __( 'Please choose a category', 'xoousers' );
                }
            }
        
        //validate post content
        if ( empty( $content ) )
		{
            $errors[] = __( 'Empty post content', 'xoousers' );
			
        } else {
			
            $content = trim( $content );
        }

        //process tags
        if ( !empty( $tags ) )
		 {
            $tags = explode( ',', $tags );
        }

        
       
       //if not any errors, proceed
        if ( $errors ) 
		{
           // echo uultra_error_msg( $errors );
           // return;
        }

        $post_stat = $xoouserultra->get_option( 'uultra_front_publisher_default_status' );
        $post_author =  get_current_user_id();

        //users are allowed to choose category
        if ( $xoouserultra->get_option( 'uultra_front_publisher_allows_category' )== 'yes' ) 
		{
            $post_category = $_POST['category'];
			
        } else {
			
            $post_category = $xoouserultra->get_option( 'uultra_front_publisher_default_category' );
        }

       $post_update = array(
                'ID' => trim( $_POST['post_id'] ),
                'post_title' => $title,
                'post_content' => $content,
                'post_category' => $post_category,
                'tags_input' => $tags
            );

        //plugin API to extend the functionality
        $post_update = apply_filters( 'uultra_edit_post_args', $post_update );

        $post_id = wp_update_post( $post_update );

        if ( $post_id )
		{    
		   
		   $this->act_message= __('Post updated successfully.', 'xoousers') ;

        }
    }
	
	
	function submit_post()
	{
       global $wpdb, $xoouserultra;
	   
	   require_once(ABSPATH . 'wp-includes/pluggable.php');

        $errors = array();

       // var_dump( $_POST );

        //if there is some attachement, validate them
        if ( !empty( $_FILES['uultra_post_attachments'] ) )
		{
           
        }

        $title = trim( $_POST['uultra_post_title'] );
        $content = trim( $_POST['uultra_post_content'] );

        $tags = '';
        if ( isset( $_POST['uultra_post_tags'] ) )
		{
			$tags = $this->uultra_clean_tags( $_POST['uultra_post_tags'] ); 
	    }

        //validate title
        if ( empty( $title ) ) 
		{
            $errors[] = __( 'Empty post title', 'xoousers' );
			
        } else {
            $title = trim( strip_tags( $title ) );
        }
		
		$cat_type = 'normal';

        //validate cat
     		
			
            if ( !isset( $_POST['category'] ) ) 
			{
				
                $errors[] = __( 'Please choose a category', 'xoousers' );
				
            } else if ( $cat_type == 'normal' && $_POST['category'][0] == '-1' ) {
				
                $errors[] = __( 'Please choose a category', 'xoousers' );
				
            } else {
				
                if ( count( $_POST['category'] ) < 1 ) {
                    $errors[] = __( 'Please choose a category', 'xoousers' );
                }
            }
        
        //validate post content
        if ( empty( $content ) )
		{
            $errors[] = __( 'Empty post content', 'xoousers' );
			
        } else {
			
            $content = trim( $content );
        }

        //process tags
        if ( !empty( $tags ) )
		 {
            $tags = explode( ',', $tags );
        }

        //post attachment

        //post type
        $post_type = 'post';
      
        //if not any errors, proceed
        if ( $errors ) 
		{
           
           // return;
        }

        $post_stat = $xoouserultra->get_option( 'uultra_front_publisher_default_status' );
        $post_author =  get_current_user_id();

        //users are allowed to choose category
        if ( $xoouserultra->get_option( 'uultra_front_publisher_allows_category' )== 'yes' ) {
            $post_category = $_POST['category'];
        } else {
            $post_category = $xoouserultra->get_option( 'uultra_front_publisher_default_category' );
        }

        $my_post = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => $post_stat,
            'post_author' => $post_author,
            'post_category' => $post_category,
            'post_type' => $post_type,
            'tags_input' => $tags
        );

        //insert the post
        $post_id = wp_insert_post( $my_post );

        if ( $post_id )
		{

            //upload attachment to the post

            //send mail notification
                    
            //set post thumbnail if has any
            if ( $attach_id ) {
               // set_post_thumbnail( $post_id, $attach_id );
            }

            //Set Post expiration date if has any
            if ( !empty( $_POST['expiration-date'] ) && $post_expiry == 'on' )
			{
                $post = get_post( $post_id );
                $post_date = strtotime( $post->post_date );
                $expiration = (int) $_POST['expiration-date'];
                $expiration = $post_date + ($expiration * 60 * 60 * 24);

                add_post_meta( $post_id, 'expiration-date', $expiration, true );
            }

            //plugin API to extend the functionality
		   
		   $this->act_message= __('Post published successfully. The admin will review it soon', 'xoousers') ;

           
        }
    }


}
$key = "publisher";
$this->{$key} = new XooPublisher();