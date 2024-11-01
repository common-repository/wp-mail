<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap wp_mail_page_settings">
<h1><?php _e('WP Mail Settings', 'mail_system')?></h1>
<?php global $wp_roles;
$opt = get_option('wp_mail_options');
$roles = $wp_roles->get_names();
unset($roles['administrator']);
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
if(isset($_POST['submit_wp_mail']) && wp_verify_nonce( $_POST['wp_mail_nonce_field'], 'wp_mail_action' )):
	_e("<strong>Saving Please wait...</strong>", 'duplicate_page');
	$needToUnset = array('submit_wp_mail');//no need to save in Database
	foreach($needToUnset as $noneed):
	  unset($_POST[$noneed]);
	endforeach;
		foreach($_POST as $key => $val):
		$wpmailpageoptions[$key] = $val;
		endforeach;
		 $saveSettings = update_option('wp_mail_options', $wpmailpageoptions );
		if($saveSettings)
		{
			ms_mail_system::redirect('options-general.php?page=wp_mail_settings&msg=1');
		}
		else
		{
			ms_mail_system::redirect('options-general.php?page=wp_mail_settings&msg=2');
		}
endif;
if(!empty($msg) && $msg == 1):
  _e( '<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p><strong>Settings saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'duplicate_page');	
elseif(!empty($msg) && $msg == 2):
  _e( '<div class="error settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p><strong>Settings not saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'duplicate_page');
endif; ?>
<form action="" method="post" name="wp_mail_form">
<?php  wp_nonce_field( 'wp_mail_action', 'wp_mail_nonce_field' ); ?>
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="wpmail_allowed_roles"><?php _e('Accessibility', 'mail_system')?></label></th>
<td>
<fieldset>
<p>
<?php 
$allowedRoles = $opt['wpmail_allowed_roles'];
if(empty($allowedRoles) && !is_array($allowedRoles)) {$allowedRoles = array(); }
foreach ($roles as $name => $display_name): 
?>
<label><input type="checkbox" value="<?php echo $name; ?>" name="wpmail_allowed_roles[]" <?php echo(in_array($name, $allowedRoles)) ? 'checked = "checked"' : '';?>> <?php echo $display_name; ?></label><br>
<?php endforeach;?>
</p>
</fieldset>
    <p><?php _e('Please select user roles to access WP Mail.', 'mail_system')?></p>
</td>
</tr>
<tr>
<th scope="row"><label for="wpmail_allow_non_users"><?php _e('Allow users to send mails also on mail boxes.', 'mail_system')?></label></th>
<td>
<fieldset>
<p>
<label><input type="checkbox" value="yes" name="wpmail_allow_non_users" <?php echo($opt['wpmail_allow_non_users'] == 'yes') ? 'checked="checked"' : ''?>><?php _e('Yes', 'mail_system')?></label><br>
</p>
</fieldset>
    <p><?php _e('If checked then it will allow to send emails on mail boxes. e.g gmail, hotmail etc.', 'mail_system')?></p>
</td>
</tr>
</tbody></table>
<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit_wp_mail"></p>
</form>