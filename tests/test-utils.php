<?php
class WP_Chatbot_Utils_Test extends WP_UnitTestCase {

  /**
   * Test that unique string generation is "unique"
   */
	function testGenerateUniqueKey() {

    $keys = array();

    for( $i = 1; $i <= 10; $i++ ){
      array_push( $keys, wp_chatbot_generate_unique_key() );
    }

    $this->assertEquals( count( $keys ), count( array_unique( $keys ) ) );

  }

  /**
   * Test specifying the length of a unique key
   */

  function testGenerateUniqueKeyLength() {
    $lengths = array(
      10,
      15,
      20,
      32,
      65,
      104
    );

    foreach ( $lengths as $length ) {
      $this->assertEquals( $length, strlen( wp_chatbot_generate_unique_key( $length ) ), $length );
    }

  }

}
?>
