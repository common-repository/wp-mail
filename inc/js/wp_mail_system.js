(function($) {
    $(function() {
        $.fn.wp_mail_system = function(options) {
            var selector = $(this).selector;
            var defaults = {
                'preview' : '.preview-upload',
                'text'    : '.text-upload',
                'button'  : '.button-upload',
            };
            var options  = $.extend(defaults, options);

        	// When the Button is clicked...
            $(options.button).click(function() { 
                // Get the Text element.
                var text = $(this).siblings(options.text);
                // Show WP Media Uploader popup
                tb_show('Upload an Attachment', 'media-upload.php?referer=wptuts&type=image&TB_iframe=true&post_id=0', false);
        		// Re-define the global function 'send_to_editor'
        		// Define where the new value will be sent to
                window.send_to_editor = function(html) {
                	// Get the URL of new image
					var src = $(html).attr('href');
                   // var src = $('img', html).attr('src');
                    // Send this value to the Text field.
                    text.attr('value', src).trigger('change'); 
					$('.showimg').show();
                    tb_remove(); // Then close the popup window
                }
                return false;
            });

            $(options.text).bind('change', function() {
            	// Get the value of current object
                var url = this.value;
                // Determine the Preview field
                //var preview = $(this).siblings(options.preview);
                // Bind the value to Preview field
                //$(preview).attr('src', url);
            });
        }

        // Usage
        $('.upload').wp_mail_system(); // Use as default option.
    });		
}(jQuery));
/* Ajax wp mail */
jQuery(document).ready(function(e) {
/* Compose Mail */		
jQuery('.sendMessage').click(function(e) {
    e.preventDefault();
	var go_ahead = true;
	var compose_nonce_field = jQuery('#compose_nonce_field').val();
	var reciever_mail = jQuery('.reciever_mail').val();
	var mail_subject = jQuery('.mail_subject').val();
	var wp_mail_attachment = jQuery('#wp_mail_attachment').val();
	var wp_mail_system_editor = jQuery('#wp_mail_system_editor').val();
	var msgId = jQuery('.msgId').val();
	 jQuery('.wp_rm_error').text('');
	 jQuery('.wp_ms_error').text('');
	if(reciever_mail == '')
	{		
	  go_ahead = false;	
	  jQuery('.wp_rm_error').html('&uarr; Please enter user email.');
	}
	else if(mail_subject == '')
	{
	  go_ahead = false;
	  jQuery('.wp_ms_error').html('&uarr; Please enter mail subject.');	
	}
	/* Go aHead everything is ok */
	if(go_ahead == true)
	{
		jQuery('.sending_img').show();
		var data = {
			'action': 'wp_mail',
			'case': 'compose',
			'compose_nonce_field': compose_nonce_field,
			'reciever_mail': reciever_mail,
			'mail_subject': mail_subject,
			'wp_mail_attachment': wp_mail_attachment,
			'wp_mail_system_editor': wp_mail_system_editor,
			'msgId': msgId
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.sending_img').hide();
			if(response == '1')
			{
					jQuery('.reciever_mail').val('');
					jQuery('.mail_subject').val('');
					jQuery('#wp_mail_attachment').val('');
					jQuery('#wp_mail_system_editor').val('');
				alert('Mail sent successfully.');				
			}
			else
			{
				alert('Mail Not Sent.');
			}
		});
	}
});
/* Save Message */
jQuery('.saveMessage').click(function(e) {
    e.preventDefault();
	var go_ahead = true;
	var compose_nonce_field = jQuery('#compose_nonce_field').val();
	var reciever_mail = jQuery('.reciever_mail').val();
	var mail_subject = jQuery('.mail_subject').val();
	var wp_mail_attachment = jQuery('#wp_mail_attachment').val();
	var wp_mail_system_editor = jQuery('#wp_mail_system_editor').val();
	 jQuery('.wp_rm_error').text('');
	 jQuery('.wp_ms_error').text('');
	if(reciever_mail == '')
	{		
	  go_ahead = false;	
	  jQuery('.wp_rm_error').html('&uarr; Please enter user email.');
	}
	else if(mail_subject == '')
	{
	  go_ahead = false;
	  jQuery('.wp_ms_error').html('&uarr; Please enter mail subject.');	
	}
	/* Go aHead everything is ok */
	if(go_ahead == true)
	{
		jQuery('.sending_img_save').show();
		var data = {
			'action': 'wp_mail',
			'case': 'save',
			'compose_nonce_field': compose_nonce_field,
			'reciever_mail': reciever_mail,
			'mail_subject': mail_subject,
			'wp_mail_attachment': wp_mail_attachment,
			'wp_mail_system_editor': wp_mail_system_editor
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.sending_img_save').hide();
			if(response == '1')
			{
					jQuery('.reciever_mail').val('');
					jQuery('.mail_subject').val('');
					jQuery('#wp_mail_attachment').val('');
					jQuery('#wp_mail_system_editor').val('');
				alert('Mail Saved as Draft!');				
			}
			else
			{
				alert('Mail Not Saved as Draft!');
			}
		});
	}
});        
});
 var feiCheckbox = function(){
            if(jQuery(".icheckbox").length > 0){
                 jQuery(".icheckbox,.iradio").iCheck({checkboxClass: 'icheckbox_minimal-grey',radioClass: 'iradio_minimal-grey'});
            }
        }  
jQuery(document).ready(function(){   
		feiCheckbox();   
      jQuery(".mail-checkall .iCheck-helper").on("click",function(){

        var prop = jQuery(this).prev("input").prop("checked");

        jQuery(".mail .mail-item").each(function(){            
            var cl = jQuery(this).find(".mail-checkbox > div");            
            cl.toggleClass("checked",prop).find("input").prop("checked",prop);                        
        }); 

    });  
		  
});
