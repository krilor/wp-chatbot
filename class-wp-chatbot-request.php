<?php
class WP_Chatbot_Request {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $url;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $method;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct(  ) {
    $this->url = 'https://api.motion.ai/1.0/messageBot';//'http://api.program-o.com/v2/chatbot/';
    $this->method = 'GET';
	}

  public function setUrl( $url ){

    if( '' == $url ){
      return;
    }

    $this->url = esc_url( $url );

  }

  public function setMethod( $method ) {
    if( in_array( $method, array( 'POST','GET','HEADER' ) ) ){
      $this->method = $method;
    }
  }

  public function request( $message, $user, $conv ) {
    if('' == $message ){
      return False;
    }

    /*$params = array(
      'bot_id' => '6',
      'say' => $message,
      'convo_id' => $conv,
      'format' => 'json'
    );*/

    $params = array(
      'bot' => 24983,
      'msg' => $message,
      'session' => $conv,
      'key' => get_option('wp-chatbot-options-api')['api-key'] #Todo add check if exists
    );


    switch ( $this->method ){
      case 'GET':
        $response = wp_remote_get( $this->url . '?' . http_build_query( $params ) );
        break;
      case 'POST':
        $response = wp_remote_post( $this->url, array( 'body' => $params ) );
        break;
      default:
        $response = False;
    }

    if( is_array($response) ) {
      // TODO: CHECK FOR SUCCESS and other error scenarios
      $response_body = json_decode( wp_remote_retrieve_body ( $response ), true );
      $bot_response = $response_body['botResponse'];
    }

    return $bot_response;
  }
}

?>
