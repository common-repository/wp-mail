<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb, $atts;
 $parameters = shortcode_atts( array(
        'page' => 'login',
        'redirect' => admin_url('admin.php?page=wp_mail_dash'),
    ), $atts );
if($parameters['redirect'] == '')
{
   $parameters['redirect'] = admin_url('admin.php?page=wp_mail_dash');	
}
if($parameters['page'] == 'login')
{
$args = array(
	'echo'           => true,
	'remember'       => true,
	'redirect'       =>  $parameters['redirect'],
	'form_id'        => 'loginform',
	'id_username'    => 'user_login',
	'id_password'    => 'user_pass',
	'id_remember'    => 'rememberme',
	'id_submit'      => 'wp-submit',
	'label_username' => __( 'Username' ),
	'label_password' => __( 'Password' ),
	'label_remember' => __( 'Remember Me' ),
	'label_log_in'   => __( 'Log In' ),
	'value_username' => '',
	'value_remember' => false
);	
if(!is_user_logged_in()):
  wp_login_form( $args );
else:
  $current_user = wp_get_current_user();
  echo 'Welcome, '. $current_user->display_name.'.';
  echo '<p><a href="'.$parameters['redirect'].'">WP Mail</a> | <a href="'.wp_logout_url( get_permalink()).'">Logout</a></p>';
endif;  
}
else
{
 echo 'Invalid Shortcode!';
}