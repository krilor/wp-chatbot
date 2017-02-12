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
	 * @access   private
	 * @var      string    $options    The options for the request
	 */
	private $options;

	/**
	 * Array of headers to be sent
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $header    The headers
	 */
	private $headers;

	/**
	 * The parameters to be sent in the request
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $params    The params
	 */
	private $params;

	/**
	 * The URL to request to
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $url   The url
	 */
	private $url;


	/**
	 * The request method
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $method  The request method
	 */
	private $method;

	/**
	 * The response array
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      array    $response  Array of responses
	 */
	private $response;


	/**
	 * The response code
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $response_code The response code
	 */
	private $response_code;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 */
	public function __construct( ) {

		$this->add_options();

		// Set up response
		$this->response = array();
		$this->response_code = 'RESPONSE';

	}

	/**
	 * Add the options needed for the request
	 *
	 * To be overwritten by child classes.
	 */
	private function add_options( ){

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
	 */
	public function replace_special_values( $message, $user, $conv ) {

		foreach ( $this->params as $param => $value ){

			switch ( $value ) {
				case 'WP_CHATBOT_INPUT_MSG':
					$this->params[ $param ] = $message;
					break;
				case 'WP_CHATBOT_CONV':
					$this->params[ $param ] = $conv;
					break;
				case 'WP_CHATBOT_USER':
					$this->params[ $param ] = $user;
					break;
			}

		}

	}

	/**
	 * Check for errors in settings
	 *
	 * @return mixed Returns error message (string) or False (boolean) if params are ok
	 */
	public function settings_has_errors(){

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
	public function add_response( $response ){
		array_push( $this->response, $response );
	}

	/**
	 * Get a response object
	 *
	 */
	public function get_response(){
		return array(
			'response_code' => $this->response_code,
	 		'response' => $this->response
		);
	}

	/**
	 * Makes the request to the external API
	 */
	public function request( $message, $user, $conv ) {

		if ( '' == $message ) {

			$this->add_response( array(
				'message' => __( 'Empty messages is hard to understand', 'wp-chatbot' ),
				'type' => 'text'
			) );
			$this->response_code = 'ERROR';
			return $this->get_response();

		} else if ( $this->settings_has_errors( ) ){

			$this->add_response( array(
				'message' => $this->settings_has_errors( ),
				'type' => 'text'
			) );
			$this->response_code = 'ERROR';
			return $this->get_response();

		}


		$this->replace_special_values( $message, $user, $conv );


		switch ( $this->method ) {
			case 'POST':
				$response = wp_safe_remote_post( $this->url, array( 'body' => $this->params, 'headers' => $this->headers ) );
				break;
			default: // GET
				$response = wp_safe_remote_get( $this->url . '?' . http_build_query( $this->params ), array( 'headers' => $this->headers ) );
				break;
		}


		if ( is_array( $response ) ) {
			// TODO: CHECK FOR SUCCESS and error scenarios

			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( !$response_body ){

				$this->add_response( array(
					'message' => __( 'No response body from external API.', 'wp-chatbot' ),
					'type' => 'text'
				) );
				$this->response_code = 'ERROR';
				return $this->get_response();

			}

			$bot_responses = wp_chatbot_jsonpath( $response_body, sanitize_text_field( $this->options['response-jsonpath'] ) );

			foreach ( $bot_responses as $bot_response ){
				$this->add_response(  array(
					'message' => $bot_response,
					'type' => 'text'
				) );
			}
		}

		return $this->get_response();
	}
}

?>
