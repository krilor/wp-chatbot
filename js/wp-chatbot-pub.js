
'use strict';
function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError('Cannot call a class as a function');
    }
}

/**
 * WPChatbotMessenger
 *
 * Will hold the messenger functionality
 * On wrapper functions for private properties http://arjanvandergaag.nl/blog/javascript-class-pattern.html
 */
var WPChatbotMessenger = function () {

    function WPChatbotMessenger(element) {
        _classCallCheck(this, WPChatbotMessenger); // only create instance of class
        this.element = typeof element !== 'undefined' ? element : '#wp-chatbot-content'; // default content element

        // Private properties
        this.messageList = [];
        this.deletedList = [];
        this.me = 1;
        this.bot = 2;


        // Functions that will be replaced by other functions to e.g. add/delete in DOM.
        // http://stackoverflow.com/questions/21243790/is-it-possible-to-redefine-a-javascript-classs-method
        this.onRecieve = function (message) {
            return console.log('Recieved: ' + message.text);
        };
        this.onSend = function (message) {
            return console.log('Sent: ' + message.text);
        };
        this.onDelete = function (message) {
            return console.log('Deleted: ' + message.text);
        };

    }


    WPChatbotMessenger.prototype.send = function send() {
        var text = arguments.length <= 0 || arguments[0] === undefined ? '' : arguments[0];

        text = this.filter(text);
        if (this.validate(text)) {
            var message = {
                user: this.me,
                text: text,
                time: new Date().getTime()
            };
            this.messageList.push(message);
            this.onSend(message);
        }
    };
    WPChatbotMessenger.prototype.recieve = function recieve() {
        var text = arguments.length <= 0 || arguments[0] === undefined ? '' : arguments[0];
        text = this.filter(text);
        if (this.validate(text)) {
            var message = {
                user: this.bot,
                text: text,
                time: new Date().getTime()
            };
            this.messageList.push(message);
            this.onRecieve(message);
        }
    };
    WPChatbotMessenger.prototype.delete = function _delete(index) {
        index = index || this.messageLength - 1;
        var deleted = this.messageLength.pop();
        this.deletedList.push(deleted);
        this.onDelete(deleted);
    };
    WPChatbotMessenger.prototype.filter = function filter(input) {
        var output = input.replace('bad input', 'good output');
        return output;
    };
    WPChatbotMessenger.prototype.validate = function validate(input) {
        return !!input.length;
    };

    WPChatbotMessenger.prototype.buildMessage = function buildMessage(text, who) {
      return '<div class="message-wrapper ' + who + '"><div class="text-wrapper animated fadein">'+ text + '</div>\n</div>';
    }

    return WPChatbotMessenger;
}();

jQuery(document).ready(function ( $ ) {
    var messenger = new WPChatbotMessenger();

    var $content = $('#wp-chatbot-content')
    var $input = $('#input');
    var $send = $('#send');

    function scrollBottom() {
        jQuery('.chatbot-wrapper .inner').scrollTop(jQuery('#wp-chatbot-content').height());
        //jQuery('#wp-chatbot-content').prop("scrollHeight");
    }
    function buildSent(message) {
        console.log('sending: ', message.text);
        $content.append(messenger.buildMessage(message.text, 'me'));
        scrollBottom();
    }
    function buildRecieved(message) {
        console.log('recieving: ', message.text);
        $content.append(messenger.buildMessage(message.text, 'bot'));
        scrollBottom();
    }

    function escapeHtml(str) {
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      return div.innerHTML;
    }

    function sendMessage() {

        var text = escapeHtml( $input.val() );


        // Only send if there is actual input
        if ( text == '' ) {
          console.log('WP Chatbot ERROR: No message');
          return;
        }

        messenger.send( text );
        $input.val( '' );

        jQuery.ajax({
          url : wp_chatbot.ajax_url,
          type : 'post',
          data : {
            action : 'wp_chatbot_converse',
            message : text
          },
          success : function( response ) {

            if ( $.isArray( response[ 'response' ] )  && response[ 'response'].length > 0 ) {

              if ( response[ 'response_code' ] == 'RESPONSE' ) {

                for ( var i in response['response'] ) {
                    messenger.recieve( response['response'][i]['message'] );
                }

              } else if ( response[ 'response_code' ] == 'ERROR' ) {

                for ( var i in response['response'] ) {

                    console.log( 'WP Chatbot ERROR: ' + response['response'][i]['message'] );

                }

              }

            } else {

              if ( response[ 'response_code' ] != 'SILENT' ) {
                console.log( 'WP Chatbot ERROR: Silent response is not marked as such' );
              }

            }



        },
        error : function( response ) {
          console.log('WP Chatbot ERROR: Error in AJAX call or unable to parse result');
        }
        });

        $input.focus();
    }

    // Callbacks
    messenger.onSend = buildSent;
    messenger.onRecieve = buildRecieved;

    $send.on('click', function (e) {
        sendMessage();
        $input.focus();
    });

    $input.on('keydown', function (e) {
        var key = e.which || e.keyCode;
        if (key === 13) {
            e.preventDefault();
            sendMessage();
        }
    });

    $('#chatbot-launcher-icon').click( function(){
        $('.chatbot-wrapper-livechat').show();
        $(this).hide();
    });

    $('.chatbot-wrapper-livechat .close').click(function(){
        $('#chatbot-launcher-icon').show();
        $('.chatbot-wrapper-livechat').hide();
    });
});
