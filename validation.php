<?php
/**
 * All validation and sanetization functions
 *
 * @package WP Chatbot
 */


function wp_chatbot_validate_jsonpath( $input ) {
  $output = $input;
  return apply_filters( 'wp_chatbot_validate_jsonpath', $output, $input );
}


?>
