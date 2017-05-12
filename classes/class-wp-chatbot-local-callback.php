<?php
/**
 * Use local callback as the response
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP-Chatbot
 */


/**
 * The API calling part of the plugin.
 */
class WP_Chatbot_Local_Callback {

	/**
	 * Return string
	 */
	public function request( $message, $user, $conv ) {

		if ( '' == $message ) {

			$error_msg = new WP_Chatbot_Message( __( 'Empty message is hard to understand', 'wp-chatbot' ) );

			return array(
				'response_code' => 'ERROR',
		 		'response' => array( $error_msg->get_contents())
			);
		} else {

			$random_responses = array(
				__( 'Hi there! Congrats on talking to a machine!', 'wp-chatbot' ),
				__( 'Did you know that AI Chatbots will take over the world?' ,'wp-chatbot' ),
				__( 'This is just a random response', 'wp-whatbot' )
			);

			$response = new WP_Chatbot_Message( $random_responses[ array_rand( $random_responses, 1 ) ] );

			/**
			 * Do whatever you want about the response
			 */
			return apply_filters( 'wp_chatbot_local_callback_response', array(
				'response_code' => 'RESPONSE',
		 		'response' => array( $response->get_contents() )
			), $message, $user, $conv  );

		}

	}
}

?>
