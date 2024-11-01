<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap">
<h1><?php _e('WP Mail Shortcodes','mail_system');?></h1>
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="blogname"><?php _e('Login Shortcode','mail_system');?></label></th>
<td><input type="text" class="regular-text" value="[wp_mail page='login' redirect='<?php echo admin_url('admin.php?page=wp_mail_dash');?>']" id="blogname" name="blogname" readonly="readonly" style="width:57em;">
<p id="tagline-description" class="description"><?php _e("Copy this shortcode to your wp pages, In php files use <code>&lt;?php echo do_shortcode('[wp_mail page='login' redirect='".admin_url('admin.php?page=wp_mail_dash')."']');?&gt;",'mail_system');?></code></p>
<p id="tagline-description" class="description"><?php _e('Note: Parameter <strong>redirect</strong> is optional. Remove it or leave it empty to redirect logged in  users directly on WP Mail Page. </code>','mail_system');?></p>
</td>
</tr>                                                                                                                       
</tbody></table>
</div>