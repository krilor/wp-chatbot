<?php

class WP_Chatbot_ApiAi_Request extends WP_Chatbot_Request {

  public function add_options( ){

		$this->options = get_option( 'wp-chatbot-options-apiai' );


		$this->options['response-jsonpath'] = '$.result.fulfillment.messages[*].speech';

    $this->url = 'https://api.api.ai/v1/query';
    $this->method = 'GET';

    $this->params = array(
      'v' => '20150910',
      'query' => 'WP_CHATBOT_INPUT_MSG',
      'sessionId' => 'WP_CHATBOT_CONV',
      'lang' => 'en'
    );

    $this->headers = array(
      'Authorization' => 'Bearer ' . ( isset( $this->options['client-token'] ) ? $this->options['client-token'] : '' )
    );

	}

  /**
  * Check if settings are missing
  */
  public function settings_has_errors(){
    return ( isset( $this->options['client-token'] ) ? False : __( 'No access token given', 'wp-chatbot' ) );
  }

};

?>
