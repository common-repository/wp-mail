<?php if ( ! defined( 'ABSPATH' ) ) exit;
$case = $_POST['case'];
/* Compose */
if($case == 'compose'):
	if(wp_verify_nonce( $_POST['compose_nonce_field'], 'compose_action' ) )
	{
		 $to = sanitize_email($_POST['reciever_mail']);
		 $subject = sanitize_text_field($_POST['mail_subject']);
		 $attachment = sanitize_text_field($_POST['wp_mail_attachment']);
		 $message = sanitize_text_field($_POST['wp_mail_system_editor']);
		 $msgId = sanitize_text_field($_POST['msgId']);
		$sendMail = ms_mail_system::sendMail($to, $subject, $attachment, $message, 0, $msgId);
		if($sendMail)
		{
		echo '1';	
		}
		else
		{
		echo '2';	
		}
	}	
endif;
/* Save */
if($case == 'save')
{
	if(wp_verify_nonce( $_POST['compose_nonce_field'], 'compose_action' ))
{
    $to = sanitize_email($_POST['reciever_mail']);
	$subject = sanitize_text_field($_POST['mail_subject']);
	$attachment = sanitize_text_field($_POST['wp_mail_attachment']);
	$message = sanitize_text_field($_POST['wp_mail_system_editor']);
	$sendMail = ms_mail_system::sendMail($to, $subject, $attachment, $message, 1);
	if($sendMail)
		{
		echo '1';	
		}
		else
		{
		echo '2';	
		}
}
}
die;