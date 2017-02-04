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

/**
 * Validate that a string value for a checkbox is either 1 or null
 * @param string $input Value to be validated
 *
 * @return boolean Value is valid or not
 */
function wp_chatbot_validate_checkbox( $input ) {
  $valid = ( is_string( $input ) && '1' == $input ) || is_null( $input );

  return apply_filters( 'wp_chatbot_validate_checkbox', $valid, $input );
}

/**
 * Sanetize a checkbox value. String should return 1.
 *
 * @param string $input Value to sanetized
 *
 * @return string Sanetized value
 */
function wp_chatbot_sanetize_checkbox( $input ) {
  $output =  is_string( $input ) && strlen( $input ) > 0 ? '1' : null;
  return apply_filters( 'wp_chatbot_sanetize_checkbox', $output, $input );
}

/**
 * Validate that a string value for a checkbox is either 1 or null
 * @param string $input Value to be validated
 *
 * @return boolean Value is valid or not
 */
function wp_chatbot_validate_requestmethod( $input ) {
  $valid = ( is_string( $input ) && in_array( $input, array( 'POST', 'GET' ) ) );

  return apply_filters( 'wp_chatbot_validate_requestmethod', $valid, $input );
}

/**
 * Sanetize a checkbox value. String should return 1.
 *
 * @param string $input Value to sanetized
 *
 * @return string Sanetized value
 */
function wp_chatbot_sanetize_requestmethod( $input ) {

  $output = '';

  if ( is_string( $input ) ) {

    if ( wp_chatbot_validate_requestmethod( $input ) ) {

      $output = $input;

    } else if ( wp_chatbot_validate_requestmethod( strtoupper( trim( $input ) ) ) ) {

      $output = strtoupper( trim( $input ) );

    }
  }

  return apply_filters( 'wp_chatbot_sanetize_checkbox', $output, $input );
}


?>
