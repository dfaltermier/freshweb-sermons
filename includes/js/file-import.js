( function( $ )  {

	$(document).ready( function() {
		
		// custom media uploader for user import page
 
	    var user_custom_uploader;
	 
	 
	    $('#upload_audio_file_button').click(function(e) {
	 
	        e.preventDefault();
	 
	        //If the uploader object has already been created, reopen the dialog
	        if (user_custom_uploader) {
	            user_custom_uploader.open();
	            return;
	        }
	 
	        //Extend the wp.media object
	        user_custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: 'Choose Audio File',
	            button: {
	                text: 'Choose Audio File'
	            },
	            multiple: false
	        });
	 
	        //When a file is selected, grab the URL and set it as the text field's value
	        user_custom_uploader.on('select', function() {
	            attachment = user_custom_uploader.state().get('selection').first().toJSON();

	            console.log( attachment );

	            $('#fws_audio_file').val(attachment.url);
	        });
	 
	        //Open the uploader dialog
	        user_custom_uploader.open();
	 
	    });


	    var sermon_custom_uploader;
	 
	 
	    $('#upload_sermon_image_button').click(function(e) {
	 
	        e.preventDefault();
	 
	        //If the uploader object has already been created, reopen the dialog
	        if (sermon_custom_uploader) {
	            sermon_custom_uploader.open();
	            return;
	        }
	 
	        //Extend the wp.media object
	        sermon_custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: 'Choose Image File',
	            button: {
	                text: 'Choose Image File'
	            },
	            multiple: false
	        });
	 
	        //When a file is selected, grab the URL and set it as the text field's value
	        sermon_custom_uploader.on('select', function() {
	            attachment = sermon_custom_uploader.state().get('selection').first().toJSON();

	            console.log( attachment );

	            $('#sermon_series_image').val(attachment.url);
	        });
	 
	        //Open the uploader dialog
	        sermon_custom_uploader.open();
	 
	    });

	});

})( jQuery );