<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$mailData = ms_mail_system::mailData($_GET['mid'], $_GET['view']);
$current_user = wp_get_current_user();
$loggedInUser = $current_user->ID;
if(isset($_GET['view']) && $_GET['view'] == 'received')
{
 $sender = get_user_by('id', $mailData->msg_from);
}
else
{
 $sender = get_user_by('id', $mailData->msg_to);
}
$avtarUrl = get_avatar_url($sender->ID);
ms_mail_system::mailViewed($_GET['mid']);
if(isset($_POST['sendMessage']))
{
	 $to = $sender->user_email;
	 $subject = $mailData->msg_subject;
	 $attachment = '';
	$message = sanitize_text_field($_POST['wp_mail_system_editor']);
	$sendMail = ms_mail_system::sendMail($to, $subject, $attachment, $message);
	if($sendMail)
	{
		ms_mail_system::alert('Mail sent');
	}
	else
	{
	    ms_mail_system::alert('Mail Not sent');
	}
}
?>
<div class="page-container">
<div class="page-content" style="">
<div class="content-frame">                                    
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">                        
                        <div class="page-title">                    
                            <h2><span class="fa fa-file-text"></span><?php _e('Re: ','mail_system');?><?php echo $mailData->msg_subject; ?></h2>
                        </div>                                                                                
                        
                        <!--div class="pull-right">                                                                                    
                            <button class="btn btn-default"><span class="fa fa-print"></span> Print</button>
                            <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                        </div-->                        
                    </div>
                    <!-- END CONTENT FRAME TOP -->
                    
                    <!-- START CONTENT FRAME LEFT -->
                    <div class="content-frame-left" style="height: 875px;">
                        <div class="block">
                            <a class="btn btn-danger btn-block btn-lg" href="admin.php?page=wp_mail_compose"><span class="fa fa-edit"></span><?php _e('COMPOSE','mail_system');?></a>
                        </div>
                         <?php include('sidebar.php'); ?>
                    </div>
                    <!-- END CONTENT FRAME LEFT -->                    
                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body" style="height: 935px;">
                       <?php  
								$displayName = $sender->display_name;
								if($loggedInUser == $sender->ID)
								{
									$displayName = $sender->display_name .' (Me)';
								}
						?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="pull-left">
                                    <img alt="<?php echo  $displayName;?>" class="panel-title-image" src="<?php echo $avtarUrl;?>">
                                    <h3 class="panel-title"><?php echo $displayName;?> <small><?php echo $sender->user_email;?></small></h3>
                                </div>
                                <div class="pull-right">
                                <?php if($_GET['view'] == 'received'):
								  $url = 'admin.php?page=wp_mail_inbox&msgid='.$_GET['mid'].'&action=delete';
							   else:
							     $url = 'admin.php?page=wp_sent_mails&msgid='.$_GET['mid'].'&action=delete';
							   endif; ?>
                            <a href="<?php echo $url; ?>" onclick="return confirm('Are you sure want to delete ?')"> <button class="btn btn-default" title="Trash"><span class="fa fa-trash-o"></span></button> </a>                                   
                                </div>
                            </div>
                            <form action="?page=wp_mail_view_message&mid=<?php echo $_GET['mid']; ?>&view=received" name="quickreply" method="post">
                            <div class="panel-body">
                                <h3><?php _e('Re:','mail_system');?><?php echo $mailData->msg_subject; ?> <small class="pull-right text-muted"><span class="fa fa-clock-o"></span> <?php echo ms_mail_system::get_email_date($mailData->msg_date); ?></small></h3>                              
                                <?php echo $mailData->msg_message; 
								if($_GET['view'] == 'received'):
								?>
                                <div class="form-group push-up-20">
                                    <label><?php _e('Quick Reply','mail_system');?></label>
                                   <?php //summernote_lite ?>
                            <textarea placeholder="Type a message..." rows="3" class="form-control" name="wp_mail_system_editor" required="required"></textarea>
                                </div>
                                <?php endif;?>
                            </div>
                            <?php if(!empty($mailData->msg_attachment)):?>
                            <div class="panel-body panel-body-table">
                                <h6><?php _e('Attachment','mail_system');?></h6>
                                <table class="table table-bordered table-striped">
                                    <tbody><tr>
                                        <th width="50"><a href="<?php echo $mailData->msg_attachment; ?>" download><?php _e('Download','mail_system');?></a></th>
                                    </tr>                                                                 
                                </tbody></table>
                            </div>
                            <?php endif; 
							if($_GET['view'] == 'received'):
							?>
                            <div class="panel-footer">
                                <button class="btn btn-success pull-right" name="sendMessage"><span class="fa fa-mail-reply"></span><?php _e('Post Reply','mail_system');?></button>
                            </div>
                            <?php endif;?>
                            </form>
                        </div>
                    </div>
                    <!-- END CONTENT FRAME BODY -->
                </div>
                </div>
                </div>       