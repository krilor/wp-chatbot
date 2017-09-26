
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
        this.onRichText = function (richText) {
            return console.log('Recieved RichText')
        }
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
    WPChatbotMessenger.prototype.printRichText = function printRichText(richText) {
        var message = {
            user: this.bot,
            text: 'Rich Message',
            time: new Date().getTime()
        };
        this.messageList.push(message);
        this.onRichText(richText);
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

    WPChatbotMessenger.prototype.buildMessage = function buildMessage(text, who, src) {
        //checks if image is gif png or jpg
        var re = /\.(jpg|png|gif|jpeg)\b/;
        var yt = false;
        if(!re.test(src)) {
            src = "https://ih0.redbubble.net/image.291625450.7598/flat,800x800,070,f.u1.jpg";
        }
        var re = /^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/;
        if(re.test(text)){
            yt = true;
        }
        console.log("TEXT: "+text.slice(-4));
        if(text.slice(-4)===".jpg" && who=='bot'){
            return '<div class="message-wrapper ' + who + 
            '"><img class="animated fadein" src="'+ src +'" width="50" max-height="100" style="border-radius: 50%; height: 50px; float: left; margin-right: 10px; margin-bottom: 20px;">'+
            '<div class="text-wrapper animated fadein">'+
                '<div class="wrapFrame">'+
                '<img width="100%" height="100%" style="border-radius: 5%;" src="'+ text + '"></img></div></div>\n</div>';
        }
        else if(yt && who=='bot'){
            return '<div class="message-wrapper ' + who + 
            '"><img class="animated fadein" src="'+ src +'" width="50" max-height="100" style="border-radius: 50%; height: 50px; float: left; margin-right: 10px; margin-bottom: 20px;">'+
            '<div class="text-wrapper animated fadein">'+
                '<div class="wrapFrame">'+
                '<iframe width="auto" height="auto" src="'+text+'?autoplay=1&amp;modestbranding=1&amp;autohide=1&amp;showinfo=0&amp;controls=0&#10;" frameborder="0" style="margin-bottom: 0em; !important;"></iframe></div>\n</div></div></div>\n</div>';
        }
        if(who=='bot'){
            return '<div class="message-wrapper ' + who + '"><img class="animated fadein" src="'+ src +'" width="50" max-height="100" style="border-radius: 50%; height: 50px; float: left; margin-right: 10px; margin-bottom: 20px;"><div class="text-wrapper animated fadein">'+ text + '</div>\n</div>';
        }
      return '<div class="message-wrapper ' + who + '"><div class="text-wrapper animated fadein">'+ text + '</div>\n</div>';
    }

    return WPChatbotMessenger;
}();

/**
 * WPChatbotRichParser
 *
 * Parse Richtext message objects to html
 */
var WPChatbotRichParser = function() {

    function WPChatbotRichParser() {
        _classCallCheck(this, WPChatbotRichParser); // only create instance of class
    }

    WPChatbotRichParser.prototype.buildRichText = function buildRichText(message) {
        switch(message.type) {
            case 'textResponse':
                return {text: message.speech};
            case 'quickReplies':
                return {
                    text: message.title,
                    richtext : this.buildQuickReplies(message.replies)
                };
            case 'image':
                return {text: this.buildImage(message.imageUrl)};
            default:
                return
        }
    };

    WPChatbotRichParser.prototype.buildImage = function buildImage(url) {
      return '<img src="' + url + '">'
    };

    WPChatbotRichParser.prototype.buildQuickReplies = function buildQuickReplies(replies) {
        var html = '<div class="chatbot-richText-wrapper">';
        replies.forEach(function (reply) {
            html += '<div class="chatbot-quickReply-wrapper animated fadein" onclick="' +
                'document.getElementById(\'input\').value = \'' + escapeQuotes(reply) + '\';' + // set the input value with the button value
                'document.getElementById(\'send\').click()"><span class="chatbot-quickReply">' // send the message
                + reply +'</span></div>';
        });
        return html + '</div>'
    };

    function escapeQuotes(text) {
        return text.replace(/'/g, "\\&#39;")
            .replace(/"/, '\\&quot;')
    }
    return WPChatbotRichParser

}();

jQuery(document).ready(function ( $ ) {
    var messenger = new WPChatbotMessenger();
    var richParser = new WPChatbotRichParser();

    var $content = $('#wp-chatbot-content')
    var $input = $('#input');
    var $send = $('#send');
    var $url = $('#image-url-input');
    // var $intro = $('#intro-text-input');


    function scrollBottom() {
        jQuery('.chatbot-wrapper .chatbot-inner').scrollTop(jQuery('#wp-chatbot-content').prop("scrollHeight"));
    }
    function buildSent(message) {
        // window.alert($url);
        // console.log($url.text);
        console.log('sending: ', message.text);
        $content.append(messenger.buildMessage(message.text, 'me', $url.val()));
        scrollBottom();
    }
    function buildRecieved(message) {
        console.log('recieving: ', message.text);
        $content.append(messenger.buildMessage(message.text, 'bot', $url.val()));
        scrollBottom();
    }
    function buildRichText(richText) {
        console.log('recieving RichText');
        $content.append(richText);
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
                    var message = response['response'][i];

                    if (message['type'] === 'text') {
                        // default type
                        messenger.recieve(message['message']);
                    } else {
                        // custom types for rich messages
                        var richMessage = richParser.buildRichText(message);
                        // First print the text part in a message
                        if (richMessage.text) messenger.recieve(richMessage.text);
                        // Then print the rich part
                        if (richMessage.richtext) messenger.printRichText(richMessage.richtext)
                    }
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
    messenger.onRichText = buildRichText;

    $send.on('click', function (e) {
        $('#intro-text-div').fadeOut();
        sendMessage();
        $input.focus();
    });

    $input.on('keydown', function (e) {
        var key = e.which || e.keyCode;
        if (key === 13) {
            $('#intro-text-div').fadeOut();
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
