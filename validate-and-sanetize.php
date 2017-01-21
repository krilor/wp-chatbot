<?php
/**
 * All validation and sanetization functions
 *
 * All types of data should have a pair of functions to sanetize and validate.
 * All functions must be prefixed with wp_chatbot_validate_ or wp_chatbot_sanetize_, and apply filters, with same name as function to the returned values.
 * Return boolean for validation. Return the sanetized value for sanetizing functions
 *
 * @package WP Chatbot
 */

/**
 * Validate that a string is a jsonpath
 * @param string $input Value to be validated
 *
 * @return boolean Value is valid or not
 */
function wp_chatbot_validate_jsonpath( $input ) {

  $valid = is_string( $input );

  if( $valid ){
    $valid = (boolean)preg_match( '/^\$.+$/', $input ); // starts with $ and contains more than two characters
  }

  if( $valid ){
    $valid = wp_chatbot_validate_paired_brackets( $input );
  }

  return apply_filters( 'wp_chatbot_validate_jsonpath', $valid, $input );
}

/**
 * Sanetize a jsonpath
 *
 * @param string $input Value to sanetized
 *
 * @return string Sanetized value
 */
function wp_chatbot_sanetize_jsonpath( $input ) {
  $output =  sanitize_text_field( $input );
  return apply_filters( 'wp_chatbot_sanetize_jsonpath', $output, $input );
}


/**
 * Validate that a string has paired brackets
 * @param string $input Value to be validated
 *
 * @return boolean Value is valid or not
 */
function wp_chatbot_validate_paired_brackets( $input ) {
  $valid = is_string( $input );

  if( $valid ){

    $bracketmap = array(
      ')' => '(',
      ']' => '[',
      '}' => '{'
    );

    $openbrackets = array();

    $chars = str_split($input);
    foreach( $chars as $char ){
      switch ( $char ){
        case '(':
        case '[':
        case '{':
          array_push( $openbrackets, $char );
          break;
        case ')':
        case ']':
        case '}':
          $valid = count( $openbrackets ) > 0;
          if ( $valid ) {
              $valid = array_pop( $openbrackets ) == $bracketmap[ $char ] ;
          }
          break;
      }

      if ( ! $valid ) break;

    } //foreach
    $valid = ( $valid && count( $openbrackets ) == 0 );
  }
  return apply_filters( 'wp_chatbot_validate_paired_brackets', $valid, $input );
}

/**
 * Sanetize unpaired brackets
 *
 * @param string $input Value to sanetized
 *
 * @return string Sanetized value
 */
function wp_chatbot_sanetize_paired_brackets( $input ) {
  $output =  sanitize_text_field( $input ); // TODO Make this function do something
  return apply_filters( 'wp_chatbot_sanetize_paired_brackets', $output, $input );
}


?>
