<?php
/**
 * The request to the API part of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP-Chatbot
 */


/**
 * The API calling part of the plugin.
 */
class WP_Chatbot_Request {

	/**
	 * The options
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $options    The options for the request
	 */
	protected $options;

	/**
	 * Array of headers to be sent
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $header    The headers
	 */
	protected $headers;

	/**
	 * The parameters to be sent in the request
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $params    The params
	 */
	protected $params;

	/**
	 * The URL to request to
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $url   The url
	 */
	protected $url;


	/**
	 * The request method
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $method  The request method
	 */
	protected $method;

	/**
	 * The response array
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $response  Array of responses
	 */
	protected $response;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct( ) {

		$this->add_options();

		// Set up response
		$this->response = array();

	}

	/**
	 * Add the options needed for the request
	 *
	 * To be overwritten by child classes.
	 */
	protected function add_options( ){

		$this->options = get_option( 'wp-chatbot-options-request' );

		// Url
		$this->url = isset( $this->options['endpoint-url'] ) ? $this->options['endpoint-url'] : NULL;

		// Method
		$this->method = isset( $this->options['request-method'] ) ? $this->options['request-method'] : NULL;

		// Params
		$this->params = array();
		for ( $i = 1; $i <= $this->options['request-param-num']; $i++ ) {

			$option_id = sprintf( 'request-param-%d', $i );
			if ( isset( $this->options[ $option_id ] ) && isset( $this->options[ $option_id . '-val' ] ) ) {

				$this->params[ $this->options[ $option_id ] ] =	$this->options[ $option_id . '-val' ];
			}
		}

		// Headers
		$this->headers = array();

		for ( $i = 1; $i <= $this->options['request-headers-num']; $i++ ) {

			$option_id = sprintf( 'request-headers-%d', $i );

			if ( isset( $this->options[ $option_id ] ) && isset( $this->options[ $option_id . '-val' ] ) ) {

				$this->headers[ $this->options[ $option_id ] ] = $this->options[ $option_id . '-val' ];
			}

		} // for

	}

	/**
	 * Replace header and param values where there are special strings
	 *
	 * @param array $values Old => new pair
	 */
	protected function replace_special_values( $values = array() ) {

		foreach ( $values as $key => $value ){

				$this->replace_special_value( $key, $value );

		}

	}
	/**
	 * Replace header and param values where there are special strings
	 *
	 * @param string $old Old string
	 * @param string $new New string that replaces old
	 *
	 */
	protected function replace_special_value( $old, $new ){

		foreach ( $this->params as $param => $value ){
			if ( $value == $old ){

				$this->params[ $param ] = $new;

			}
		}

	}

	/**
	 * Check for errors in settings
	 *
	 * @return mixed Returns error message (string) or False (boolean) if params are ok
	 */
	protected function settings_has_errors(){

		if ( ! isset( $this->url ) || ! isset( $this->method ) ) {

			return __( "URL or request method is missing", 'wp-chatbot' );

		} else if ( 0 == count( $this->params ) ) {

			return __( 'No params has been set', 'wp-chatbot' );

		} else {

			return False;

		}
	}

	/**
	 * Append a response
	 *
	 * @param array A response object
	 *
	 * @return void
	 */
	protected function add_response( $response ){
		array_push( $this->response, $response );
	}

	/**
	 * Get a response object
	 *
	 */
	protected function get_response( $response_code = 'RESPONSE' ){

		$response = [];

		foreach ( $this->response as $message ) {
			array_push( $response, $message->get_contents() );
		}

		if ( 'RESPONSE' == $response_code )
			$response_code = 0 == count( $response ) ? 'SILENT' : 'RESPONSE';

		return array(
			'response_code' => $response_code,
	 		'response' => $response
		);

	}

	/**
	 * Makes the request to the external API
	 */
	public function request( $message, $user, $conv ) {

		if ( '' == $message ) {

			$this->add_response( new WP_Chatbot_Message( __( 'Empty messages is hard to understand', 'wp-chatbot' )	) );
			return $this->get_response( 'ERROR' );


		} else if ( $this->settings_has_errors( ) ){

			$this->add_response( new WP_Chatbot_Message( $this->settings_has_errors( ) ) );
			return $this->get_response( 'ERROR' );

		}


		$special_values = apply_filters( 'wp_chatbot_special_values', array(
			'WP_CHATBOT_INPUT_MSG' => $message,
			'WP_CHATBOT_CONV' => $conv,
			'WP_CHATBOT_USER' => $user
		));

		$this->replace_special_values( $special_values );


		switch ( $this->method ) {
			case 'POST':
				$response = wp_safe_remote_post( $this->url, array( 'body' => $this->params, 'headers' => $this->headers ) );
				break;
			default: // GET
				$response = wp_safe_remote_get( $this->url . '?' . http_build_query( $this->params ), array( 'headers' => $this->headers ) );
				break;
		}

		// Validate response

		if ( is_wp_error( $response ) ){

			$this->add_response( new WP_Chatbot_Message( $response->get_error_message() ) );
			return $this->get_response( 'ERROR' );

		} else if ( ! is_array( $response ) ) {

			$this->add_response( new WP_Chatbot_Message( __( 'The remote request was unsuccessful', 'wp-chatbot' ) ) );
			return $this->get_response( 'ERROR' );

		}


		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( !$response_body ){

			$this->add_response( new WP_Chatbot_Message( __( 'No response body from external API.', 'wp-chatbot' ) ) );
			return $this->get_response( 'ERROR' );

		}

		if ( isset( $this->options['response-jsonpath'] ) && "" != $this->options['response-jsonpath'] ){

			$bot_responses = wp_chatbot_jsonpath( $response_body, $this->options['response-jsonpath'] );

			if( is_array( $bot_responses ) ) {

				foreach ( $bot_responses as $bot_response ){
					$this->add_response( new WP_Chatbot_Message( $bot_response ));
				}
			}
		}

		return $this->get_response();
	}
}

?>
