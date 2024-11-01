<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$num_rec_per_page = 10;
$allEmails = ms_mail_system::getEmails('trash', $num_rec_per_page); 
$current_user = wp_get_current_user();
$loggedInUser = $current_user->ID;
$trashedmsgs =  ms_mail_system::totalMails($type = 'trashed');
if(isset($_POST['restorethese']))
{
	$multimailselect = $_POST['multimailselect'];
	$restoreTrash = ms_mail_system::restoreMail($multimailselect);
	if($restoreTrash)
	{
	   ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=1');	
	}
	else
	{
	  ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=2');	
	}
}
/* Trash These permanently */
if(isset($_POST['trashpermanent']))
{
   $multimailselect = $_POST['multimailselect'];
  $removeThis = ms_mail_system::removePermanently($multimailselect, 'single');
  if($removeThis)
	{
	   ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=3');	
	}
	else
	{
	  ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=2');	
	}
}
/* Trash ALL permanently */
if(isset($_POST['trashempty']))
{
  $removeThis = ms_mail_system::removePermanently('', 'emptytrash');
  if($removeThis)
	{
	   ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=3');	
	}
	else
	{
	  ms_mail_system::redirect('admin.php?page=wp_trashed_mails&msg=2');	
	}
}
?>
<div class="page-container">
<div class="page-content" style="">
<div class="content-frame">                                    
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">                        
                        <div class="page-title">                    
                            <h2><span class="fa fa-trash"></span><?php _e(' Trash ','mail_system');?><small>(<?php echo $trashedmsgs; ?> <?php _e(' Trashed','mail_system');?>)</small></h2>
                        </div>                                                                                                  
                    </div>
                    <!-- END CONTENT FRAME TOP -->
                    
                    <!-- START CONTENT FRAME LEFT -->
                    <div class="content-frame-left" style="height: 536px;">
                        <div class="block">
                            <a class="btn btn-danger btn-block btn-lg" href="admin.php?page=wp_mail_compose"><span class="fa fa-edit"></span> <?php _e('COMPOSE','mail_system');?></a>
                        </div>
                         <?php include('sidebar.php');?>
                    </div>
                    <!-- END CONTENT FRAME LEFT -->
                    
                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body" style="height: 596px;">
                            <form action="admin.php?page=wp_trashed_mails" name="mailform" method="post">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <label class="check mail-checkall">
                                   <input type="checkbox" class="icheckbox" style="position: absolute; opacity: 0;">
                                </label>                             
                                <button class="btn btn-default" name="restorethese" title="Restore Mails"><span class="fa fa-mail-reply"></span></button>
                                <button class="btn btn-default" name="trashpermanent" title="Delete Selected Permanently"><span class="fa fa-trash-o"></span></button>
                                <button class="btn btn-default" name="trashempty" title="Empty Trash"><?php _e('Empty Trash ','mail_system');?></button>
                            </div>
                            <div class="panel-body mail">
                                <?php 
								if(!empty($allEmails)):
								//print_r($allEmails);
								foreach($allEmails as $recieved):
								$sender = get_user_by('id', $recieved->msg_from);
							    $displayName = $sender->display_name;
								if($loggedInUser == $recieved->msg_from)
								{
									$displayName = 'me';
								}
								?>
                                <div class="mail-item mail-success">                                    
                                    <div class="mail-checkbox">
                                        <input type="checkbox" class="icheckbox" style="position: absolute; opacity: 0;" name="multimailselect[]" value="<?php echo $recieved->msg_id;?>">
                                    </div>
                                  
                                    <div class="mail-user"><?php echo ucwords($displayName)?></div>                                   
                                    <a class="mail-text" href="admin.php?page=wp_mail_view_message&mid=<?php echo $recieved->msg_id;?>&view=received"><?php echo ucwords($recieved->msg_subject)?></a>                                    
                                    <div class="mail-date"><?php echo ms_mail_system::get_email_date($recieved->msg_date); ?></div>
                                </div>
                                 <?php endforeach; else: echo '<center>No Mail in Trash.</center>'; endif; ?>  
                            </div>
                            <div class="panel-footer">                             
                                 <?php ms_mail_system::mk_pagenavi('trash', $num_rec_per_page);  ?>    
                            </div>                            
                        </div>
                        </form>
                        
                    </div>
                    <!-- END CONTENT FRAME BODY -->
                </div>
                </div>
                </div>
                 <!-- START SCRIPTS -->
       