<?php
/*
Plugin Name: WP Mail
Plugin URI: https://wordpress.org/plugins/wp-mail/
Description: User can send and receive emails or messages on a Wordpress Network
Version: 1.3
Author: mndpsingh287
Author URI: https://profiles.wordpress.org/mndpsingh287/
License: GPLv2 or later
Text Domain: wp-mail
*/
if(!defined("mail_system"))
{ 
   define("mail_system", "mail_system");
}
if(!class_exists('ms_mail_system'))
{
	class ms_mail_system
	{
		/*
		* autoload hooks
		*/
		public function __construct()
		{
		   global $wpdb;
		   date_default_timezone_set(get_option('timezone_string'));
		   add_action('admin_menu', array(&$this,'wp_mail_system_menu'));
		   add_action( 'admin_enqueue_scripts', array(&$this,'load_wp_mail_admin_things'));
		   register_activation_hook(__FILE__, array(&$this,'wp_mail_system_activation_process'));
	       add_action('admin_init', array(&$this, 'allow_subscriber_uploads'));
		   add_action( 'wp_ajax_wp_mail', array(&$this, 'wp_mail_callback'));
           add_action( 'wp_ajax_nopriv_wp_mail', array(&$this, 'wp_mail_callback'));
		   load_plugin_textdomain('mail_system', false, basename(dirname( __FILE__ ) ) . '/languages' );
		   add_filter( 'plugin_action_links', array(&$this, 'wp_mail_system_action_links'), 10, 2 );
		   add_shortcode('wp_mail', array(&$this, 'wp_mail_shortcode'));
		   add_action('wp_head', array(&$this, 'header_scripts'));
		}
		/*
		* Plugin Links
		*/
		public function wp_mail_system_action_links($links, $file)
		{
		  if ( $file == plugin_basename( __FILE__ ) ) {
			    $wp_mail_settings = '<a href="options-general.php?page=wp_mail_settings" title="Settings" style="font-weight:bold">'.__('Settings').'</a>';
				 $wp_mail_buy_pro = '<a href="http://www.webdesi9.com/product/wp-mail/" title="Buy PRO" target="_blank" style="font-weight:bold">'.__('Buy PRO').'</a>';
				$wp_mail_donate = '<a href="http://www.webdesi9.com/donate/?plugin=wp-mail" title="Donate Now" target="_blank" style="font-weight:bold">'.__('Donate').'</a>';
				array_unshift( $links, $wp_mail_donate );
				array_unshift( $links, $wp_mail_buy_pro );
				array_unshift( $links, $wp_mail_settings );
			}
		
			return $links;	
		}
		/*
		* activation process
		*/
		public function wp_mail_system_activation_process()
		{
			include('inc/install/install.php');
			       $defaultsettings =  array(
			                        		 'wpmail_allowed_roles' => array('subscriber'),
											 'wpmail_allow_non_users' => 'no'
											 );
					$opt = get_option('wp_mail_options');
					if(!$opt['wpmail_allowed_roles']) {
						update_option('wp_mail_options', $defaultsettings);
					}  
		}
		/*
		* Menu
		*/
		public function wp_mail_system_menu()
		{
			$opt = get_option('wp_mail_options');
			$allowedroles = $opt['wpmail_allowed_roles'];
			$allowedroles = array_merge(array('administrator'), $allowedroles);
			global $wpdb,$current_user,$user_role_permission;
			$cm_role = $wpdb->prefix . "capabilities";
			$current_user->role = array_keys($current_user->$cm_role);
		    $cm_role = $current_user->role[0];
			if($cm_role == 'subscriber')
			{
			 echo '<style>#menu-media{display:none;}</style>';	
			}
			$allowedRoles = array('administrator' => 'manage_options', 'subscriber' => 'read', 'editor' => 'edit_posts', 'author' => 'edit_posts', 'contributor' => 'edit_posts');
			if(array_key_exists($cm_role, $allowedRoles) && in_array($cm_role, $allowedroles))
			{
			  $user_role_permission = $allowedRoles[$cm_role];		
			}
			$unreadmsgs =  self::totalMails($type = 'unread');
			add_menu_page( 
			__( 'Inbox ('.$unreadmsgs.')', 'custom_maps' ),
			'WP Mail',
			$user_role_permission,
			'wp_mail_dash',
			array(&$this,'wp_mail_inbox_callback'),
		     plugins_url('/inc/img/icon.png',  __FILE__ ),
			'200'
             );
			 /* Inbox */		 
			  add_submenu_page(
			'wp_mail_dash',
			 __( 'Inbox ('.$unreadmsgs.')', 'custom_maps' ),
			'Inbox <span class="update-plugins count-'.$unreadmsgs.'"><span class="plugin-count">'.$unreadmsgs.'</span></span>',
			$user_role_permission,
			'wp_mail_inbox',
			array(&$this,'wp_mail_inbox_callback'));		
			/* compose */		 
			  add_submenu_page(
			'wp_mail_dash',
			 __( 'Compose', 'custom_maps' ),
			'Compose',
			$user_role_permission,
			'wp_mail_compose',
			array(&$this,'wp_mail_compose_callback'));
			/* shortcodes */		 
			  add_submenu_page(
			'wp_mail_dash',
			 __( 'Shortcodes', 'custom_maps' ),
			'Shortcodes',
			'manage_options',
			'wp_mail_shortcodes',
			array(&$this,'wp_mail_shortcodes_callback'));
			/* view msg */		 
			  add_submenu_page(
			'',
			 __( 'View Message', 'custom_maps' ),
			'View Message',
			$user_role_permission,
			'wp_mail_view_message',
			array(&$this,'wp_mail_view_message_callback'));
			/* sent msg */		 
			  add_submenu_page(
			'',
			 __( 'Sent Messages', 'custom_maps' ),
			'Sent Messages',
			$user_role_permission,
			'wp_sent_mails',
			array(&$this,'wp_mail_sent_message_callback'));
			/* draft */		 
			  add_submenu_page(
			'',
			 __( 'Draft', 'custom_maps' ),
			'Draft',
			$user_role_permission,
			'wp_draft_mails',
			array(&$this,'wp_draft_mails_callback'));
			/* trash */		 
			  add_submenu_page(
			'',
			 __( 'Trashed', 'custom_maps' ),
			'Trashed',
			$user_role_permission,
			'wp_trashed_mails',
			array(&$this,'wp_trashed_mails_callback'));
			/* Setting Page */
			 add_options_page('WP Mail', 'WP Mail', 'manage_options', 'wp_mail_settings',array(&$this, 'wp_mail_settings'));
		}
		/*
		* Inbox
		*/
		public function wp_mail_inbox_callback()
		{
			include('inc/inbox.php');
		}
		/*
		* Compose
		*/
		public function wp_mail_compose_callback()
		{
			include('inc/compose_mail.php');
		}
		/*
		* View Message
		*/
		public function wp_mail_view_message_callback()
		{
			include('inc/view_message.php');
		}
		/*
		* Sent Messages
		*/
		public function wp_mail_sent_message_callback()
		{
			include('inc/sent_messages.php');
		}
		/*
		* Draft Mails Callback
		*/
		public function wp_draft_mails_callback()
		{
		    include('inc/draft_messages.php');	
		}
		/*
		* Trashed Mails Callback
		*/
		public function wp_trashed_mails_callback()
		{
		    include('inc/trashed_messages.php');	
		}
		/*
		* Shortcode
		*/
		public function wp_mail_shortcodes_callback()
		{
			include('inc/wp_mail_shortcodes.php');
		}
		/*
		* Setting
		*/
		public function wp_mail_settings()
		{
			include('inc/settings.php');	
		}
		/*
		* send mail
		*/
		static function sendMail($mto, $subject, $attachment = null, $message, $draft = 0, $msgId = '')
		{
			global $wpdb;
			$opt = get_option('wp_mail_options');
			$tbl = $wpdb->prefix.'mail_system';
		    	if(!empty($msgId))
				{
				 $trashdraft = $wpdb->delete($tbl, array('msg_id' => $msgId));
			    }
			$return  = false;
			$current_user = wp_get_current_user();
			$sender = $current_user->ID;
			$mailto = explode(',', $mto);
				foreach($mailto as $to):
					$receiverDetails = get_user_by( 'email', $to );
					if($receiverDetails)
					{
						 $receiver = $receiverDetails->ID;
						 $saveData = $wpdb->insert( $tbl , array('msg_from' => $sender, 'msg_to' => $receiver, 'msg_subject' => $subject, 'msg_message' => $message, 'msg_attachment' => $attachment, 'msg_is_draft' => $draft, 'msg_is_seen' => '0', 'msg_is_trashed_by_from' => '0', 'msg_is_trashed_by_to' => '0', 'msg_date' => date('d-m-Y h:i:s A')), array('%d','%d','%s','%s','%s','%d','%d','%d','%d','%s'));
						 if($saveData)
						 {	 
						  $return = true;
						    if($draft == 0 && $opt['wpmail_allow_non_users'] == 'yes'):
							  $headers[] = 'Content-Type: text/html; charset=UTF-8';
							  $headers[] = 'From: '.$current_user->display_name.' <'.$current_user->user_email.'>';
							  wp_mail( $to, $subject, $message, $headers, $attachment );
						  endif;
						 }
						 else
						 {
						  $return = false;	 
						 }			  
					}
					else
					{
						 $return = false;
					}
				endforeach;  
			return $return;
		}
		/*
		Get Mails
		*/
		static function getEmails($type = 'received', $num_rec_per_page = 10)
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			if (isset($_GET["pg"])) { $pg  = $_GET["pg"]; } else { $pg=1; }
			$GLOBALS['pg'] = $pg;
			$start_from = ($pg-1) * $num_rec_per_page; 
			switch($type)
			{
				case 'received':
					$query = 'select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "0" AND msg_is_draft = "0" ORDER BY msg_id DESC LIMIT '.$start_from.', '.$num_rec_per_page.'';
					$allEmails = $wpdb->get_results($query);
					return $allEmails;
				break;
				
				case 'sent':
					$query = 'select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_is_trashed_by_from = "0" AND msg_is_draft = "0" ORDER BY msg_id DESC LIMIT '.$start_from.', '.$num_rec_per_page.'';
					$allEmails = $wpdb->get_results($query);
					return $allEmails;
				break;
				
				case 'draft':
					$query = 'select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_is_draft = "1" ORDER BY msg_id DESC LIMIT '.$start_from.', '.$num_rec_per_page.'';
					$allEmails = $wpdb->get_results($query);
					return $allEmails;
				break;
				
				case 'trash':
					$query = 'select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "1" ORDER BY msg_id DESC LIMIT '.$start_from.', '.$num_rec_per_page.'';
					$allEmails = $wpdb->get_results($query);
					return $allEmails;
				break;
				
				default:
					$query = 'select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "0" AND msg_is_draft = "0" ORDER BY msg_id DESC LIMIT '.$start_from.', '.$num_rec_per_page.'' ;
					$allEmails = $wpdb->get_results($query);
					return $allEmails;
				break;
			}
		}
		/*
		* Mail data
		*/
		static function mailData($mid, $type = 'received')
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			$allowedViews = array('received','sent');
			if(!in_array($type, $allowedViews))
			{
				self::redirect('admin.php?page=wp_mail_inbox');
				die;
			}
			if(empty($mid) || empty($type))
			{
				self::redirect('admin.php?page=wp_mail_inbox');
				die;
			}
			if($type == 'received')
			{
				$getValidData = $wpdb->get_row('select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_id = "'.$mid.'"');
				if(count($getValidData) > 0 )
				{
					return $getValidData;
				}
				else
				{
					self::redirect('admin.php?page=wp_mail_inbox');
					die;
				}
			}
			else if($type == 'sent')
			{
				$getValidData = $wpdb->get_row('select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_id = "'.$mid.'"');
				if(count($getValidData) > 0 )
				{
					return $getValidData;
				}
				else
				{
					self::redirect('admin.php?page=wp_mail_inbox');
					die;
				}
			}
		}
		/*
		* Mail is viewed
		*/
		static function mailViewed($msgid)
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			$setMailviewed = $wpdb->update($tbl,
			                               array(
										         'msg_is_seen' => 1
												 ), 
										   array(
										         'msg_id' => $msgid, 
										         'msg_to' => $CurrentUser
												 ),												 
										   array('%d'),
										   array( 
												  '%d',
												  '%d'
												)
							 );
			if($setMailviewed)
			{
				return true;
			}
			else
		    {
				return false;
		    }
		}
		/*
		* TrashMail
		*/
		static function trashMail($msgids, $type)
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			if(!empty($msgids) && is_array($msgids))
			{
			   foreach($msgids as $msgid)				
				{	
				if($type == 'inbox')
				{
				 $array1 = array( 'msg_is_trashed_by_to' => 1);
				 $array2 = array( 'msg_id' => $msgid, 'msg_to' => $CurrentUser);
				}               
				else if($type == 'sent')
				{
				 $array1 = array( 'msg_is_trashed_by_from' => 1);
				 $array2 = array( 'msg_id' => $msgid, 'msg_from' => $CurrentUser);	
				}			
				 $moveToTrash = $wpdb->update($tbl, $array1, $array2,												 
											   array('%d'),
											   array( 
													  '%d',
													  '%d'
													)
								 );
				
				}				
					return true;
			}
			else
			{
				return false;
			}
	    }
		/*
		* Restore Mail
		*/
		static function restoreMail($msgids)
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			if(!empty($msgids) && is_array($msgids))
			{
			   foreach($msgids as $msgid)				
				{	
		
				 $array1 = array( 'msg_is_trashed_by_to' => 0);
				 $array2 = array( 'msg_id' => $msgid, 'msg_to' => $CurrentUser);						
				 $restoreTrash = $wpdb->update($tbl, $array1, $array2,												 
											   array('%d'),
											   array( 
													  '%d',
													  '%d'
													)
								 );				
				}				
					return true;
			}
			else
			{
				return false;
			}
	    }
		/*
		* Total Msgs
		*/
		static function totalMails($type = 'unread')
		{
			global $wpdb;
			$current_user = wp_get_current_user();
			$CurrentUser = $current_user->ID;
			$tbl = $wpdb->prefix.'mail_system';
			if($type == 'unread')
			{
				$unreadMsgs = $wpdb->get_results('select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_seen != "1" AND msg_is_trashed_by_to = 0 AND msg_is_draft = 0' );
				return count($unreadMsgs);
			}
			else if($type == 'trashed')
			{
				$trashed = $wpdb->get_results('select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "1" ');
				return count($trashed);
			}
			else if($type == 'draft')
			{				
				$draft = $wpdb->get_results($query = 'select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_is_draft = "1" ORDER BY msg_id DESC');
				
				return count($draft);
			}
		}
		/*
		* Pagination
		*/
		static function mk_pagenavi($type, $num_rec_per_page)
		{
				global $wpdb, $pg;
				$current_user = wp_get_current_user();
				$CurrentUser = $current_user->ID;
				$tbl = $wpdb->prefix.'mail_system';
						if($type == 'received')
						{
											$query = 'select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "0" AND msg_is_draft = 0 ORDER BY msg_id DESC';
						}
						else if($type == 'sent')
						{
											$query = 'select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_is_trashed_by_from = "0" AND msg_is_draft = 0 ORDER BY msg_id DESC';
						}
						else if($type == 'draft')
						{
											$query = 'select * from '.$tbl.' where msg_from = "'.$CurrentUser.'" AND msg_is_draft = "1" ORDER BY msg_id DESC';
						}
						else if($type == 'trash')
						{
						$query = 'select * from '.$tbl.' where msg_to = "'.$CurrentUser.'" AND msg_is_trashed_by_to = "1" ORDER BY msg_id DESC';		
						}
					$rs_result = $wpdb->get_results($query);
					$total_records = count($rs_result);
					$total_pages = ceil($total_records / $num_rec_per_page);
					if($total_pages > 0) {
					echo '<ul class="pagination pagination-sm pull-right">';				
					echo '<li class=""><a href="admin.php?page='.$_GET['page'].'&pg=1">«</a></li>';
						for ($i=1; $i<=$total_pages; $i++) {
								   if($i == $pg)
								   {
									   $class = 'active';
								   }
								   else
								   {
									   $class = '';
								   }
				   echo '<li class="'.$class.'"><a href="admin.php?page='.$_GET['page'].'&pg='.$i.'">'.$i.'</a></li>';
						}
	                echo '<li><a href="admin.php?page='.$_GET['page'].'&pg='.$total_pages.'">»</a></li>';										
					echo '</ul>';
					}
		}
		/*
		* Mail Date
		*/
		static function get_email_date($emailTimee)
		{
			$emailTime = strtotime($emailTimee);
			$currentDate = date('d-m-Y');
			$newTime = date('h:i:s A',$emailTime);
			$newDate = date('d-m-Y',$emailTime);
			$prev_date = date('d-m-Y', strtotime($currentDate .' -1 day'));
			if($newDate == $currentDate)
			{
				$newDate = 'Today - '.$newTime;
			}
			else if($prev_date == $newDate)
			{
				$newDate = 'Yesterday - '.$newTime;
			}
			else 
			{
			   $newDate = $emailTimee;	
			}
			return $newDate;
		}
		/*
		* Delete Permanently 
		*/
		static function removePermanently($ids = null, $type = null)
		{
				global $wpdb;
				$current_user = wp_get_current_user();
				$CurrentUser = $current_user->ID;
				$tbl = $wpdb->prefix.'mail_system';
		   if($type == 'emptytrash')	
		   {
				$removeasupdate = $wpdb->query(
												"
												UPDATE ".$tbl." 
												SET msg_is_trashed_by_to = '1000'
												WHERE msg_to = '".$CurrentUser."' 
												AND msg_is_trashed_by_to = '1'
												"
											);
				if($removeasupdate)
				{
					return true;
				}
				else
				{
					return false;
				}
		   }
		   else if($type = 'single')
		   {
				 if(!empty($ids) && is_array($ids))
				 {
					 foreach($ids as $id)
					 {
						$removeasupdate = $wpdb->update($tbl, array('msg_is_trashed_by_to' => '1000'), array('msg_id' => $id),array('%d'), array('%d', '%d', '%d'));
					 }
					 return true;
				 }
				 else
				 {
					 return false;
				 }
		   }
		}
		/*
		* WP Mail Shortcode
		*/
		public function wp_mail_shortcode($atts)
		{
			$GLOBALS['atts'] = $atts; //scope globally
			include('inc/shortcode.php');
		}
		/*
		* Header Scripts
		*/
		public function header_scripts()
		{
			wp_enqueue_script( 'login_form', plugins_url('/inc/js/login_form.js',  __FILE__ )); 
		}
		/*
		* Redirection
		*/
		static function redirect($url)
		{
			echo '<script>
			window.location.href= "'.$url.'";
			</script>';
		}
		/*
		*alert
		*/
		static function alert($msg)
		{
			echo '<script>alert("'.$msg.'");</script>';
		}
		/* 
		* Allowing Subscribers to upload attachment                                   
		*/
		public function allow_subscriber_uploads() {		
		    $opt = get_option('wp_mail_options');
			$allowedroles = $opt['wpmail_allowed_roles'];
			if(!empty($allowedroles) && is_array($allowedroles)):
			foreach($allowedroles as $val):
				$getRole = get_role($val);		
				$getRole->add_cap('upload_files');
			endforeach;	
			endif;		
		}
		/*
		* Load Js / Css
		*/		
		public function load_wp_mail_admin_things() {
			$getPage = isset($_GET['page']) ? $_GET['page'] : '';
			$allowedPages = array(
			                      'wp_mail_dash',
			                      'wp_mail_inbox',
								  'wp_mail_compose',
								  'wp_sent_mails',
								  'wp_draft_mails',
								  'wp_trashed_mails', 
								  'wp_mail_view_message',
								  'wp_mail_shortcodes'
								  );
				if(!empty($getPage) && in_array($getPage, $allowedPages)):				  
					wp_enqueue_script('media-upload');
					wp_enqueue_script('thickbox');
					wp_enqueue_style('thickbox');
					wp_enqueue_style( 'theme-default', plugins_url('/inc/css/theme-default.css', __FILE__)); 
					wp_enqueue_script( 'bootstrap.min', plugins_url('/inc/js/plugins/bootstrap/bootstrap.min.js',  __FILE__ ));
					wp_enqueue_script( 'icheck.min', plugins_url('/inc/js/plugins/icheck/icheck.min.js',  __FILE__ ));
					wp_enqueue_script( 'wp_mail_system', plugins_url('/inc/js/wp_mail_system.js',  __FILE__ )); 	
				endif;	
				}				
				/*
	  * Wp mail Ajax work
	  */
	  public function wp_mail_callback()
		  {
			  include('ajax/actions.php');
		  }
	  }
	new ms_mail_system;
}