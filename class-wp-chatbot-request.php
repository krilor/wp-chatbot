<?php
class WP_Chatbot_Request {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $options;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct(  ) {
    $this->options = get_option('wp-chatbot-options-api');
	}

  public function request( $message, $user, $conv ) {
    if('' == $message ){
      return __( "Empty messages is hard to understand", 'wp-chatbot' );
    }

		if ( !isset( $this->options['endpoint-url'] ) || !isset( $this->options['request-method'] )) {
			return __( "I'm feeling sick today, so I am AFK", 'wp-chatbot' );
		}

		if ( !isset( $this->options['request-param-num'] ) || 0 == strlen( $this->options['request-param-num'] ) || !isset( $this->options['response-jsonpath'] ) ) {
			return __( "I think you have the wrong number", 'wp-chatbot');
		}

		// Build params
		$params = array();

		for ( $i = 1; $i <= $this->options['request-param-num']; $i++ ) {

			$option_id = sprintf( 'request-param-%d', $i);
			if ( isset( $this->options[$option_id] ) && isset( $this->options[$option_id . '-val'] ) ) {

				switch ( $this->options[$option_id . '-val'] ) {
					case 'WP_CHATBOT_INPUT_MSG':
						$value = $message;
						break;
					case 'WP_CHATBOT_CONV':
						$value = $conv;
						break;
					default:
						$value = $this->options[$option_id . '-val'];
				}

				$params[$this->options[$option_id]] = $value;
			}

		} // for

		// Build headers
		$headers = array();

		for ( $i = 1; $i <= $this->options['request-headers-num']; $i++ ) {

			$option_id = sprintf( 'request-headers-%d', $i);

			if ( isset( $this->options[$option_id] ) && isset( $this->options[$option_id . '-val'] ) ) {

				$headers[$this->options[$option_id]] = $this->options[$option_id . '-val'];
			}

		} // for


    switch ( $this->options['request-method'] ){
      case 'POST':
        $response = wp_safe_remote_post( $this->options['endpoint-url'], array( 'body' => $params, 'headers' => $headers ) );
        break;
      default: // GET
				$response = wp_safe_remote_get( $this->options['endpoint-url'] . '?' . http_build_query( $params ), array( 'headers' => $headers ) );
				break;
    }

		//var_dump($response);

    if( is_array($response) ) {
      // TODO: CHECK FOR SUCCESS and error scenarios
      $response_body = json_decode( wp_remote_retrieve_body ( $response ), true );
			$bot_response = jsonPath($response_body, $this->options['response-jsonpath']);
			
      //$bot_response = $response_body[$this->options['response-key-msg']];
			//$bot_response = $response_body['result']['fulfillment']['messages'][0]['speech'];
    }

    return $bot_response;
  }
}

?>
