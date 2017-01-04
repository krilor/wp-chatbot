(function($) {

  $('.wp-chatbot-button').on('click', function() {

    var input = $('#wp-chatbot-input');
    var message = input.val();
    input.val('');

    $('.wp-chatbot-text').append('<span class="wp-chatbot-say-user">'+message+'</span>');

  	jQuery.ajax({
  		url : wp_chatbot.ajax_url,
  		type : 'post',
  		data : {
  			action : 'wp_chatbot_converse',
        message : message
  		},
  		success : function( response ) {
        response = JSON.parse(response);
  			$('.wp-chatbot-text').append('<span class="wp-chatbot-say-bot">'+response['response']+'</span>');;
  		}
  	});
  })

})( jQuery );
