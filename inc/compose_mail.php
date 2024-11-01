<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$msgId = isset($_GET['mid']) ? $_GET['mid'] : '';
if(isset($_POST['sendMessage']) && wp_verify_nonce( $_POST['compose_nonce_field'], 'compose_action' ) )
{
	$to = sanitize_email($_POST['reciever_mail']);
	$subject = sanitize_text_field($_POST['mail_subject']);
	$attachment = sanitize_text_field($_POST['wp_mail_attachment']);
	$message = sanitize_text_field($_POST['wp_mail_system_editor']);
	$sendMail = ms_mail_system::sendMail($to, $subject, $attachment, $message, 0, $msgId);
	if($sendMail)
	{
		ms_mail_system::alert('Mail sent successfully.');
		ms_mail_system::redirect('admin.php?page=wp_mail_compose');
	}
	else
	{
		ms_mail_system::alert('Mail Not Sent.');
	}
}
else if(isset($_POST['saveMessage']) && wp_verify_nonce( $_POST['compose_nonce_field'], 'compose_action' ))
{
    $to = sanitize_email($_POST['reciever_mail']);
	$subject = sanitize_text_field($_POST['mail_subject']);
	$attachment = sanitize_text_field($_POST['wp_mail_attachment']);
	$message = sanitize_text_field($_POST['wp_mail_system_editor']);
	$sendMail = ms_mail_system::sendMail($to, $subject, $attachment, $message, 1);
	if($sendMail)
	{
		 ms_mail_system::alert('Mail Saved as Draft!');
	}
	else
	{
		ms_mail_system::alert('Mail Not Saved as Draft!');
	}
}
if(!empty($_GET['mid'])):
	$mailData = ms_mail_system::mailData($_GET['mid'], 'sent');
	$sender = get_user_by('id', $mailData->msg_to);
	$to = $sender->user_email;
	$subject = $mailData->msg_subject;
	$attachment = $mailData->msg_attachment;
	$message = $mailData->msg_message;
else:
	$to = '';
	$subject = '';
	$attachment = '';
	$message = '';
endif;
?>
<div class="page-container">
<div class="page-content" style="">
<div class="content-frame"> 
                        <form class="form-horizontal" role="form" action="" method="post">  
                        <?php wp_nonce_field( 'compose_action', 'compose_nonce_field' );  ?>   
                        <input type="hidden" value="<?php echo $msgId; ?>" class="msgId" name="msgId"/>                          
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">
                        <div class="page-title">                    
                            <h2><span class="fa fa-pencil"></span> <?php _e('Compose','mail_system');?></h2>
                        </div>                         
                        <?php if(empty($msgId)): ?>
                        <div class="pull-right">
                            <img src="<?php echo plugins_url('/img/sending.gif',  __FILE__ );?>" class="sending_img_save" />  <button class="btn btn-default saveMessage" name="saveMessage"><span class="fa fa-floppy-o"></span><?php _e('Save','mail_system');?></button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- END CONTENT FRAME TOP -->
                    
                    <!-- START CONTENT FRAME LEFT -->
                    <div class="content-frame-left" style="height: 777px;">
                        <?php include('sidebar.php');?>
                    </div>
                    <!-- END CONTENT FRAME LEFT -->
                    
                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body" style="height: 837px;">
                        <div class="block">
                            <div class="form-group">
                                <div class="col-md-12">                                 
                                    <div class="pull-right">
                                    <img src="<?php echo plugins_url('/img/sending.gif',  __FILE__ );?>" class="sending_img" />    <button class="btn btn-danger sendMessage" name="sendMessage"><span class="fa fa-envelope"></span><?php _e('Send Message','mail_system');?></button>
                                    </div>                                    
                                </div>
                            </div>
                                                    
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php _e('To:','mail_system');?></label>
                                <div class="col-md-9">                                        
                               <input type="text" class="form-control reciever_mail" placeholder="Enter receiver's wordpress email" name="reciever_mail" required="required" value=""> 
                               <p class="wp_rm_error wpmail_error"></p>                           
                                </div>
                                
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php _e('Subject:','mail_system');?></label>
                                <div class="col-md-10">                                        
                                    <input type="text" value="<?php echo $subject; ?>" class="form-control mail_subject" placeholder="Re: Enter subject here" name="mail_subject" required="required"> 
                                     <p class="wp_ms_error wpmail_error"></p>                                
                                </div>                                
                            </div>
                            
                            <div class="form-group">
                                <div class="col-md-12">                            
                                    <!--textarea class="summernote_email" style="display: none;"></textarea-->
                                   <?php wp_editor( $message, 'wp_mail_system_editor' );  ?>                         
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label"><?php _e('Attachment:','mail_system');?></label>
                                <div class="col-md-10">  
                                <span class='upload'>
                    <input type='text' id='wp_mail_attachment' class='form-control text-upload' name='wp_mail_attachment' value='<?php echo $attachment; ?>' placeholder="Click 'Upload Attachment' Button or Paste Attachment path here"/>
                    <input type='button' class='button button-primary button-upload' value='Upload Attachment'/></br>
              </span>                                      
                                </div>                                
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                    <img src="<?php echo plugins_url('/img/sending.gif',  __FILE__ );?>" class="sending_img" />    <button class="btn btn-danger sendMessage" name="sendMessage"><span class="fa fa-envelope"></span><?php _e('Send Message','mail_system');?></button>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <!-- END CONTENT FRAME BODY -->
                   </form>
                </div>
                 </div>
                </div>