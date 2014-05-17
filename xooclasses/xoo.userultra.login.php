<?php
class XooUserLogin {

	function __construct() 
	{
		/*-----------Referece Social Users Types*/
		/* 1 - Facebook, 2 - LinkedIn,  3- Yahoo, 4 - Google, 5 - Twitter */
		/*------------------------------*/
		
		/*Handle Paypal Login*/
		if (isset($_GET['usersultraipncall'])) 
		{
			$this->errors = false;			
			/* */
			$this->handle_paypal_login();

		}
		
		/*Handle Acctoun Verification */
		if (isset($_GET['act_link'])) 
		{
					
			/* */
			$this->handle_account_conf_link();

		}
						
		if (isset($_GET['code'])) 
		{
						
			// Setting default to false;
			$this->errors = false;			
			/* */
			$this->handle_social_facebook();

		}
		
		//yahooo and google
		if (isset($_GET["openid_ns"])) 
		{
			// Setting default to false;
			$this->errors = false;			
			/* */
			$this->handle_social_google();

		}
		
		if(!isset($_REQUEST["oauth_token"]))
		{
			$_SESSION['linkedinoauthstate'] = NULL;
			$_REQUEST['oauth_token'] = NULL;
			$_REQUEST['oauth_verifier'] = NULL;
			
		}
		
		//linkedin
		if (isset($_GET['oauth_token']) ) 
		{
						
			// Setting default to false;
			$this->errors = false;			
			/* */
			$this->handle_linkedin_authorization();

		}
		
			
		
		/*Form is fired*/
		if (isset($_POST['xoouserultra-login'])) {
						
			/* Prepare array of fields */
			$this->prepare( $_POST );
			
			// Setting default to false;
			$this->errors = false;
			
			/* Validate, get errors, etc before we login a user */
			$this->handle();

		}

	}
	
	/*Prepare user meta*/
	function prepare ($array ) 
	{
		foreach($array as $k => $v) {
			if ($k == 'xoouserultra-login') continue;
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle commons sigun ups*/
	function handle() 
	{
	    global $xoousersultra_captcha_loader, $xoouserultra, $blog_id;;
	    
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		if ( empty( $GLOBALS['wp_rewrite'] ) )
		{
			 $GLOBALS['wp_rewrite'] = new WP_Rewrite();
	    }
		
		$noactive = false;
		foreach($this->usermeta as $key => $value) 
		{
		
			if ($key == 'user_login') 
			{
				if (sanitize_user($value) == '')
				{
					$this->errors[] = __('<strong>ERROR:</strong> The username field is empty.','xoousers');
				}
			}
			
			if ($key == 'user_pass')
			{
				if (esc_attr($value) == '') 
				{
					$this->errors[] = __('<strong>ERROR:</strong> The password field is empty.','xoousers');
				}
			}
		}
		
		
		// Check captcha first
		if(!is_in_post('no_captcha','yes'))
		{
		   // if(!$xoousersultra_captcha_loader->validate_captcha(post_value('captcha_plugin')))
		   // {
		      //  $this->errors[] = __('<strong>ERROR:</strong> Please complete Captcha Test first.','xoousers');
		    //}
		}
		
	
			/* attempt to signon */
			if (!is_array($this->errors)) 
			{
				$creds = array();
				
				// Adding support for login by email
				if(is_email($_POST['user_login']))
				{
				    $user = get_user_by( 'email', $_POST['user_login'] );
				    
				    if(isset($user->data->user_login))
					{
				        $creds['user_login'] = $user->data->user_login;
						
				    }else{
						
				        $creds['user_login'] = '';
					
					}
					
					// check if active					
					$user_id =$user->ID;				
					if(!$this->is_active($user_id))
					{
						$noactive = true;
						
					}
				
				}else{
					
					// User is trying to login using username					
					$user = get_user_by('login',$_POST['user_login']);
					
					// check if active and it's not an admin		
					if(isset($user))	
					{
						$user_id =$user->ID;	
						
					
					}else{
						
						$user_id ="";
						
					}		
					
								
					
					 		
					if(!$this->is_active($user_id) && !is_super_admin($user_id))
					{
						$noactive = true;
						
					}				
					
					$creds['user_login'] = sanitize_user($_POST['user_login']);
				
				
				}
				
				$creds['user_password'] = $_POST['login_user_pass'];
				$creds['remember'] = $_POST['rememberme'];					
				
				
				if(!$noactive)
				{
					
					//echo "step e";
				
								
					$user = wp_signon( $creds, false );					
					
						//print_r($user );	
	
					if ( is_wp_error($user) ) 
					{
						
						//echo "TTEES here" ;
						if ($user->get_error_code() == 'invalid_username') {
							$this->errors[] = __('<strong>ERROR:</strong> Invalid Username was entered.','xoousers');
						}
						if ($user->get_error_code() == 'incorrect_password') {
							$this->errors[] = __('<strong>ERROR:</strong> Incorrect password was entered.','xoousers');
						}
						
						if ($user->get_error_code() == 'empty_password') {
							$this->errors[] = __('<strong>ERROR:</strong> Please provide Password.','xoousers');
						}
						
						
											
					}else{
						
						wp_set_auth_cookie($user->ID);
						wp_set_current_user($user->ID);					
						do_action( 'wp_login', $user->user_login );
					
					}
					
					//print_r($user );	
				
				}else{
					
					//not active
					$this->errors[] = __('<strong>ERROR:</strong> Your account is not active.','xoousers');
				 
				}
			}
		
		
		
		
	}
	
	/*---->> Check if user is active before login  ****/
	function is_active($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		
		if ($checkuser == 'active' || $checkuser == '') //this is a tweak for already members
		{
			return true;
		
		}else{
			
			return false;
		
		}			
		
	}
	
	 /*Handle Paypal Login*/
	public function handle_paypal_login() 
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		//get user with key		
		$key = $_GET['usersultraipncall'];
		
		$order = $xoouserultra->order->get_order_confirmed($key);
		
		if($order->order_user_id!="")
		{
			$account_page_id = get_option('xoousersultra_my_account_page');
		    $my_account_url = get_permalink($account_page_id);
			wp_set_auth_cookie( $order->order_user_id, true, $secure );
			wp_redirect($my_account_url);			
			exit;
			
		}
		
	
	}
	
	 /*Handle LinkedIn Sign up*/
	public function handle_linkedin_authorization() 
	{
		session_start();
		
		global $xoouserultra ;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(xoousers_path."libs/linkedin/oauth/linkedinoauth.php"); 
		
			 
		 //get oauttokens
		 $temp_user_session_id = session_id();			
				
		 $oauthstate =  get_option('uultra_linkedin_'.$temp_user_session_id);
		 
		 //get access token and access token secret		 
		 $oauthstate = $xoouserultra->get_linkedin_access_token($oauthstate);
		 
	 
	
		if (!$oauthstate)
		{
			echo "empty ";
		
		}else{
		
		
			// We've been given some access tokens, so try and use them to make an API call, and
			// display the results.
			
			$accesstoken = $oauthstate['access_token'];
			$accesstokensecret = $oauthstate['access_token_secret'];
			
			//print_r($oauthstate);
	
			$to = new LinkedInOAuth(
				$xoouserultra->get_option('social_media_linkedin_api_public'),
				$xoouserultra->get_option('social_media_linkedin_api_private'),
				$accesstoken,
				$accesstokensecret
			);
			
			$find_person = ':(first-name,last-name,email-address)';
			$profile_result = $to->oAuthRequest('http://api.linkedin.com/v1/people/~'.$find_person);
			$profile_data = simplexml_load_string($profile_result);
			
			
			//print htmlspecialchars(print_r($profile_data, true));
		   
			   
			$profile_data = json_decode( json_encode($profile_data) , 1);
						
			$u_name = $profile_data["first-name"] ;
			$lname = $profile_data["last-name"] ;
			$u_email = $profile_data["email-address"] ;
			
			$u_user = $u_name."-".$lname;
			
			//Sanitize Login
			 $user_login = str_replace ('.', '-', $u_user);
			 $user_login = sanitize_user ($user_login, true);
			 
			
			//check if exists
			
			//check if already registered
			$exists = email_exists($u_email);
			if(!$exists)
			{
				  //lets create
				  
				 //generat3 random password
				 $user_pass = wp_generate_password( 12, false);
				 
				 //Build user data
				 $user_data = array (
								'user_login' => $user_login,
								'display_name' => (!empty ($u_name) ? $u_name : $u_user),
								'user_email' => $u_email,																				
								'user_pass' => $user_pass
							);
	
				// Create a new user
				$user_id = wp_insert_user ($user_data);
				
				if ( ! $user_id ) 
				{
				
				}else{
					    
						$verify_key = $this->get_unique_verify_account_id();					
						update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);					
						update_user_meta ($user_id, 'xoouser_ultra_social_signup', 2);
						
						update_user_meta ($user_id, 'first_name', $u_name);
						update_user_meta ($user_id, 'last_name', $lname);
						
						//set account status
						$this->user_account_status($user_id);
						
						//notify depending on status
						$this->user_account_notify($user_id, $u_email, $user_login, $user_pass);
						
						
				}
				
				
			}else{
				
				//if user already created then try to login automatically
				
				$user = get_user_by('login',$user_login);				
				$user_id =$user->ID;
				
				if(is_active($user_id))
				{
					//is active then login
					wp_set_auth_cookie( $user_id, true, $secure );			
				
				}else{
					
					$this->errors[] = __('<strong>ERROR:</strong> YOUR ACCOUNT IS NOT ACTIVE YET.','xoousers');
					
				}
				
			
			
			
			}
			
		
		
		//redirect
		$this->login_registration_afterlogin();
		
		
		
	  }
	
	}
	
	public function login_registration_afterlogin()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		//check redir		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		wp_redirect($my_account_url);
		exit;
	
	}
	
	public function get_my_account_direct_link()
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		return $my_account_url;
	
	}
		
	 /*Handle Facebook Sign up*/
	public function handle_social_facebook() 
	{
		global $xoouserultra ;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(xoousers_path."libs/fbapi/src/facebook.php");
		 
		 //facebook credentials		
		$FACEBOOK_APPID = $xoouserultra->get_option('social_media_facebook_app_id');  
		$FACEBOOK_SECRET = $xoouserultra->get_option('social_media_facebook_secret');
		
				
        $config = array();
		$config['appId'] = $FACEBOOK_APPID;
		$config['secret'] = $FACEBOOK_SECRET;
		
					
		$facebook = new Facebook($config);
			
		// Get User ID
		$user = $facebook->getUser();
		
		if($user) 
		{
									
		
			 $user_profile = $facebook->api('/me','GET');
			 $fbid = $user_profile['id'];
			// $user_picture = $facebook->api('/'.$fbid.'/picture','GET');
			
			 $u_user = $user_profile['username'];
		     $u_name = $user_profile['name'];
		     $u_email = $user_profile['email'];
		     $u_fb_id = $user_profile['id'];
			 
			 //Sanitize Login
			 $user_login = str_replace ('.', '-', $u_user);
			 $user_login = sanitize_user ($user_login, true);	
			 
			 
			 //check if already registered
			  $exists = email_exists($u_email);
			  if(!$exists)
			  {
				  //lets create
				  
				 //generat random password
				 $user_pass = wp_generate_password( 12, false);
				 
				 //Build user data
				 $user_data = array (
								'user_login' => $user_login,
								'display_name' => (!empty ($u_name) ? $u_name : $u_user),
								'user_email' => $u_email,																				
								'user_pass' => $user_pass
							);
	
				// Create a new user
				$user_id = wp_insert_user ($user_data);
				
				if ( ! $user_id ) 
				{
				
				}else{		
					
						update_user_meta ($user_id, 'xoouser_ultra_social_signup', 1);
						update_user_meta ($user_id, 'xoouser_ultra_facebook_id', $u_fb_id);	
						
						$verify_key = $this->get_unique_verify_account_id();					
						update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);						
						update_user_meta ($user_id, 'first_name', $u_name);
											
						//update_user_meta ($user_id, 'usersultra_account_status', 'active');
						
						//set account status
						$this->user_account_status($user_id);
						
						//notify depending on status
						$this->user_account_notify($user_id, $u_email, $user_login, $user_pass);
						
						
						
				}
				
				
			}else{
				
				//if user already created then try to login automatically
				
				$user = get_user_by('login',$user_login);				
				$user_id =$user->ID;
				
				if(is_active($user_id))
				{
					//is active then login
					wp_set_auth_cookie( $user_id, true, $secure );			
				
				}else{
					
					$this->errors[] = __('<strong>ERROR:</strong> YOUR ACCOUNT IS NOT ACTIVE YET.','xoousers');
					
				}
				
			
			
			
			}
				
			
		}else{ //end if fb user is not vallid
			
			//error from facebook side
			
			$this->errors[] = __('<strong>ERROR:</strong> SOME PROBLEM OCCOURED WHILE CONNECTING FACEBOOK PLEASE LOGIN AGAIN.','xoousers');
		
		}
		
		
		//notify
		if (!is_array($this->errors)) 
		{
			
			//notify client			
			//$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
			
			//$creds['user_login'] = sanitize_user($u_user);				
			//$creds['user_password'] = $user_pass;
			//$creds['remember'] = 1;				
			//$user = wp_signon( $creds, false );
		
		}
		
		$this->login_registration_afterlogin();
	
	
			
  }
  
  public function get_unique_verify_account_id()
  {
	  $rand = $this->genRandomString(8);
	  $key = session_id()."_".time()."_".$rand;
	  
	  return $key;
	  
	 
  }
  
  public function genRandomString($length) 
  {
		
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
  
  /*---->> Set Account Status  ****/  
  public function user_account_status($user_id) 
  {
	  global $xoouserultra;
	  
	  //check if login automatically
	  $activation_type= $xoouserultra->get_option('registration_rules');
	  
	  if($activation_type==1)
	  {
		  //automatic activation
		  update_user_meta ($user_id, 'usersultra_account_status', 'active');							
	  
	  }elseif($activation_type==2){
		  
		  //email activation link
		  update_user_meta ($user_id, 'usersultra_account_status', 'pending');	
	  
	  }elseif($activation_type==3){
		  
		  //manually approved
		  update_user_meta ($user_id, 'usersultra_account_status', 'pending_admin');
	  
	  
	  }
	
  }
  
    /*---->> re send activation link ****/  
  public function user_resend_activation_link($user_id, $u_email, $user_login) 
  {
	  global $xoouserultra;
	  
	  require_once(ABSPATH . 'wp-includes/pluggable.php');
	  require_once(ABSPATH . 'wp-includes/link-template.php');
	  
	    //email activation link				  
		  $web_url =$this->get_my_account_direct_link();			  
		  $pos = strpos("page_id", $web_url);		  
		  $unique_key = get_user_meta($user_id, 'xoouser_ultra_very_key', true);

			  
		  if ($pos === false) // this is a tweak that applies when not Friendly URL is set.
		  {
			    //
				$activation_link = $web_url."?act_link=".$unique_key;
					
		  } else {
				     
			 // found then we're using seo links					 
				 $activation_link = $web_url."&act_link=".$unique_key;
					
		  }
		  
		  //send link to user
		  $xoouserultra->messaging->re_send_activation_link($u_email, $user_login, $activation_link);
		  
   }
  
  
    /*---->> Notify User ****/  
  public function user_account_notify($user_id, $u_email, $user_login, $user_pass) 
  {
	  global $xoouserultra;
	  
	  require_once(ABSPATH . 'wp-includes/pluggable.php');
	  require_once(ABSPATH . 'wp-includes/link-template.php');
	  
	  //check if login automatically
	  $activation_type= $xoouserultra->get_option('registration_rules');
	  
	  if($activation_type==1)
	  {
		  //automatic activation
		  $xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
		  						
	  
	  }elseif($activation_type==2){
		  
		  //email activation link		  
		  
		  $web_url =$this->get_my_account_direct_link();			  
		  $pos = strpos("page_id", $web_url);		  
		  $unique_key = get_user_meta($user_id, 'xoouser_ultra_very_key', true);

			  
		  if ($pos === false) // this is a tweak that applies when not Friendly URL is set.
		  {
			    //
				$activation_link = $web_url."?act_link=".$unique_key;
					
		  } else {
				     
			 // found then we're using seo links					 
				 $activation_link = $web_url."&act_link=".$unique_key;
					
		  }
		  
		  //send link to user
		  $xoouserultra->messaging->welcome_email_with_activation($u_email, $user_login, $user_pass, $activation_link);
		  
	  
	  }elseif($activation_type==3){		  
		  //manually approved
		 
	  
	  
	  }
	
  }
  
   /*Handle Account Email Confirmation*/
	public function handle_account_conf_link() 
	{
		global $xoouserultra ;
			
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$act_link = $_GET["act_link"];
		
		if(isset($act_link) && $act_link!="")
		{
			//get user with meta
			$user = $this->get_user_with_key($act_link);
			
			if($user!="error")
			{
				$secure ="";
				//activate user
				$user_id = $user->ID;
				$user_email = $user->user_email;
				
				update_user_meta ($user_id, 'usersultra_account_status', 'active');
				
				//notify				
				$xoouserultra->messaging->confirm_verification_sucess($user_email);
			
				//login user and take them to account				
				wp_set_auth_cookie( $user_id, true, $secure );				
				$this->login_registration_afterlogin();
			
			
			}else{
				
				//wrong key, display message at the screen
				
			
			
			}
		
		
		}
		
	
	}
	
	//get user with kewy - used for confirmation link only
	public function get_user_with_key( $uniquekey )
	{
		global  $wpdb,  $xoouserultra;
		
		$args = array(
		
			'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'usersultra_account_status',
						'value' => 'pending',
						'compare' => '='
					),
					array(
						'key' => 'xoouser_ultra_very_key',
						'value' => $uniquekey ,
						'compare' => '='
					),
			
			)
		);


		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
		
		if(!empty($res)) 
		{
			
			foreach ( $res as $user )
			{
				return $user;
			
			
			}
		
		
		}else{
			
			return "error";
			
		}
			
		
	}
    
   /*Handle Facebook Sign up*/
	public function handle_social_google() 
	{
		global $xoouserultra ;
			
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(xoousers_path."libs/openid/openid.php");
		
		
		
		
		 
		 //facebook libraries
		 $web_url = site_url();
		 
		 $openid = new LightOpenID($web_url);
		 
		  
		 
		 if ($openid->mode) 
		 {
						  
			  $data = $openid->getAttributes();
			
			  if ($openid->mode == 'cancel') 
			  {
			   
			  }elseif($data["contact/email"]!="") {
				  
				  $openid->validate();
				  
				  $redir_url ="";
				  
				  
				   //authentication authorized
				  
				   $data = $openid->getAttributes();
				   $email = $data['contact/email'];
				  
				   $a =  $openid->identity ;
				   
				   //validate
					$type = 4; //google
					
					if (strpos($a,'yahoo') !== false) 
					{
						$first = $data['namePerson'];
						$type = 3; //yahoo
						
						$user_full_name = trim ($first);
											
					
					}else{
						 $first = $data['namePerson/first'];
				         $last_n = $data['namePerson/last'];
				   
						
						$user_full_name = trim ($first." ".$last_n);
						
					}
					
					
					
					//save
					 $u_user = $user_full_name;
					 $u_name = $first;
					 $u_email = $email;
					
					 
					 			 //check if already registered
					  $exists = email_exists($u_email);
					  if(!$exists)
					  {
					 
						 //generat random password
						 $user_pass = wp_generate_password( 12, false);
						 
						 //Sanitize Login
						 $user_login = str_replace ('.', '-', $u_user);
						 $user_login = sanitize_user ($u_user, true);	 
						
						 //Build user data
						 $user_data = array (
										'user_login' => $user_login,
										'display_name' => (!empty ($u_name) ? $u_name : $u_user),
										'user_email' => $u_email,																				
										'user_pass' => $user_pass
									);
									
												
						// Create a new user
						$user_id = wp_insert_user ($user_data);
						
						if ( ! $user_id ) 
						{
						
						}else{
								
								update_user_meta ($user_id, 'xoouser_ultra_social_signup', $type);
								
								$verify_key = $this->get_unique_verify_account_id();					
						        update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);	
								
								$this->user_account_status($user_id);
								
								
								//update_user_meta ($user_id, 'xoouser_ultra_facebook_id', $u_fb_id);
								
								//notify client			
								$xoouserultra->messaging->welcome_email($u_email, $user_login, $user_pass);
								
								$creds['user_login'] = sanitize_user($u_user);				
								$creds['user_password'] = $user_pass;
								$creds['remember'] = 1;		
								
								
								$noactive = false;
								if(!$this->is_active($user_id) && !is_super_admin($user_id))
								{
									$noactive = true;
									
								}
								
								if(!$noactive)
								{
									$user = wp_signon( $creds, false );
									
								}
							
								
						}
						
						
					}else{
						
						$noactive = false;
						/*If alreayd exists*/
						$user = get_user_by('login',$u_user);
						$user_id =$user->ID;
						
						if(!$this->is_active($user_id) && !is_super_admin($user_id))
						{
							$noactive = true;
									
						}
						
						if(!$noactive)
						{
							 $secure = "";		
							//already exists then we log in
							wp_set_auth_cookie( $user_id, true, $secure );			
									
						}
									
					
					
					}
							
							
					   
			  }
			
		 
		 
		 }
		 
		 $this->login_registration_afterlogin();
		
			
  }
	
	
	
	/*Get errors display*/
	function get_errors()
	 {
		global $xoouserultra;
		
		$display = null;
		
		if (isset($this->errors) && is_array($this->errors))  
		{
		    $display .= '<div class="xoouserultra-errors">';
		
			foreach($this->errors as $newError) 
			{
				
				$display .= '<span class="xoouserultra-error xoouserultra-error-block"><i class="xoouserultra-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		
		
		} else {
			
			if (isset($_REQUEST['redirect_to']))
			{
				$url = $_REQUEST['redirect_to'];
				
			} elseif (isset($_POST['redirect_to'])) {
				
				$url = $_POST['redirect_to'];
				
			} else {
				
				$url = $_SERVER['REQUEST_URI'];
			}
			wp_redirect( $url );
		}
		return $display;
	}

}
$key = "login";
$this->{$key} = new XooUserLogin();