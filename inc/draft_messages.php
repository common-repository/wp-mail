<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$num_rec_per_page = 10;
$allEmails = ms_mail_system::getEmails('draft', $num_rec_per_page);
$current_user = wp_get_current_user();
$loggedInUser = $current_user->ID;
 ?>
<div class="page-container">
<div class="page-content" style="">
<div class="content-frame">                                    
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">                        
                        <div class="page-title">                    
                            <h2><span class="fa fa-clock-o"></span><?php _e(' Draft','mail_system');?></h2>
                        </div>                                                                                                  
                    </div>
                    <!-- END CONTENT FRAME TOP -->                    
                    <!-- START CONTENT FRAME LEFT -->
                    <div class="content-frame-left" style="height: 536px;">
                        <div class="block">
                            <a class="btn btn-danger btn-block btn-lg" href="admin.php?page=wp_mail_compose"><span class="fa fa-edit"></span><?php _e('COMPOSE','mail_system');?> </a>
                        </div>
                         <?php include('sidebar.php');?>
                    </div>
                    <!-- END CONTENT FRAME LEFT -->
                    
                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body" style="height: 596px;">
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <label class="check mail-checkall">
                                 <input type="checkbox" class="icheckbox" style="position: absolute; opacity: 0;">
                                </label>                             
                                <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>
                            </div>
                            <div class="panel-body mail">
                                <?php 
								if(!empty($allEmails)):
								//print_r($allEmails);
								foreach($allEmails as $recieved):
								$sender = get_user_by('id', $recieved->msg_to);
								$displayName = $sender->display_name;
								if($loggedInUser == $recieved->msg_to)
								{
									$displayName = 'me';
								}
								?>
                                <div class="mail-item mail-unread mail-info">                                    
                                    <div class="mail-checkbox">
                                        <input type="checkbox" class="icheckbox" style="position: absolute; opacity: 0;" name="multiselect[]" value="<?php echo $recieved->msg_id;?>">
                                    </div>
                                  
                                    <div class="mail-user"><?php echo ucwords($displayName)?></div>                                    
                                    <a class="mail-text" href="admin.php?page=wp_mail_compose&mid=<?php echo $recieved->msg_id;?>"><?php echo ucwords($recieved->msg_subject)?></a>                                    
                                    <div class="mail-date"><?php echo ms_mail_system::get_email_date($recieved->msg_date); ?></div>
                                </div>
                                 <?php endforeach; else: echo '<center>No Mails in Draft.</center>'; endif; ?>  
                            </div>
                            <div class="panel-footer">                             
                              <?php ms_mail_system::mk_pagenavi('draft', $num_rec_per_page);  ?>    
                            </div>                            
                        </div>
                        
                    </div>
                    <!-- END CONTENT FRAME BODY -->
                </div>
                </div>
                </div>
                 <!-- START SCRIPTS -->