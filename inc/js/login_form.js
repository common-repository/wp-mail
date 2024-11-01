jQuery(document).ready(function(e) {
    jQuery('#loginform').submit(function(e) {
		var user_login = jQuery('#user_login').val();
		var user_pass = jQuery('#user_pass').val();
		if(user_login == '')
		{
		  jQuery('.login-username label').css('color','#F00');
		  e.preventDefault();	
		}
		else if(user_pass == '')
		{
		  jQuery('.login-password label').css('color','#F00');
		  e.preventDefault();		
		}
    });
});