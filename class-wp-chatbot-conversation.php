<?php
/**
 * The chatbot conversation stuff is kept her
 *
 * @since      0.1.0
 *
 * @package    WP Chatbot
 */

/**
 * A conversation and all its functionality
 *
 * @package    WP Chatbot
 */
class WP_Chatbot_Conversation {

	/**
	 * The the current conversation (sessions) id
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $conversation Current convo ID
	 */
	protected $conversation;

	/**
	 * The current users id
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $user Current user ID
	 */
	protected $user;

	public function __construct() {

		/* Start a session if not allready started */
		if ( ! session_id() ) {
			session_start();
		}

		$this->conversation = $this->conversation_id();
		$this->user = $this->user_id();

	}

	public function __set( $var, $value ) {

		if ( in_array( $var, array( 'user', 'conversation' ) ) ) {

			$this->$var = (string)$value;

		} else {

			$this->$var = $value;

		}

	}

	public function __get( $var ) {

		return $this->$var;

	}

	/**
	 * Get an ID that represents the current conversation.
	 *
	 * @return string ID of current sessions conversation
	 */
	private function conversation_id() {

		$conv_var = 'wp_chatbot_conv';

		if ( ! isset( $_SESSION[ $conv_var ] ) ) {
			$conv = wp_chatbot_generate_unique_key();
			$_SESSION[ $conv_var ] = $conv;
		} else {
			$conv = $_SESSION[ $conv_var ];
		}

		return $conv;

	}

	/**
	 * Get an ID that represents the current user.
	 *
	 * @return string ID of user
	 */
	private function user_id() {

		$user_var = 'wp_chatbot_user';

		if ( ! isset( $_COOKIE[ $user_var ] ) ) {
			$user = wp_chatbot_generate_unique_key();
		} else {
			$user = $_COOKIE[ $user_var ];
		}
		setcookie( $user_var, $user , time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

		return $user;

	}

/**
 * Add a message to the conversation
 *
 * @param string $message Sanitized message to be sent
 * @return array
 */

 public function say( $message ){

	 $wpcr = new WP_Chatbot_Request();
	 $response = $wpcr->request( $message, $this->user, $this->conversation ); // TODO: Check response and add filter

	 return $response;

 }
}
