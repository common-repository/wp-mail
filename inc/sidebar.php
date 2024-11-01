<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$unreadmsgs =  ms_mail_system::totalMails($type = 'unread');
$trashedmsgs =  ms_mail_system::totalMails($type = 'trashed');
$draftmsgs =  ms_mail_system::totalMails($type = 'draft');
?>
<div class="block">
<div class="list-group border-bottom">
         <a class="list-group-item" href="admin.php?page=wp_mail_inbox"><span class="fa fa-inbox"></span><?php _e('Inbox','mail_system');?><span class="badge badge-success"><?php echo $unreadmsgs; ?></span></a>
         <a class="list-group-item" href="admin.php?page=wp_sent_mails"><span class="fa fa-rocket"></span><?php _e('Sent','mail_system');?></a>
          <a class="list-group-item" href="admin.php?page=wp_draft_mails"><span class="fa fa-clock-o"></span><?php _e('Draft','mail_system');?><span class="badge badge-success"><?php echo $draftmsgs; ?></span></a>
         <a class="list-group-item" href="admin.php?page=wp_trashed_mails"><span class="fa fa-trash-o"></span><?php _e('Trash','mail_system');?><span class="badge badge-default"><?php echo $trashedmsgs ?></span></a>                            
</div>                        
</div>