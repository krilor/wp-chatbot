<?php
/**
 * All utility functions
 *
 * Contains all small utility funtions used by the different classes in the plugin
 *
 * @package WP Chatbot
 */

/**
 * Generate a unique string
 *
 * @param int $length The length of the returned string.
 *
 * @return string A unique string.
 */
function wp_chatbot_generate_unique_key( $length = null ){
  $key = apply_filters( 'wp_chatbot_generate_unique_key_gen', md5( microtime() - rand( 0, 50000 ) ), $length );

  if ( isset( $length ) ){

    if ( strlen( $key ) > $length ) {
      $key = substr( $key, 0, $length );
    } else {
      $key = str_pad( $key, $length, $key);
    }

  }

  return apply_filters( 'wp_chatbot_generate_unique_key_output', $key, $length );
}

 ?>
