<?php
/**
 * Base class for a message containing only text
 */

if( ! class_exists( 'WP_Chatbot_Message' ) ):

class WP_Chatbot_Message {

  /**
   * Type of message
   */
  public $type;

  /**
   * The message text
   */
  public $message;

  public function __construct( $message ){
    $this->type = 'text';
    $this->message = $message;
  }

  public function get_contents(){
    return get_object_vars( $this );
  }

}


endif; // WP_Chatbot_Message exists

 ?>
