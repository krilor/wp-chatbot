<?php
class WP_Chatbot_Validator_Test extends WP_UnitTestCase {

  /**
   * Tests a positive scenario where the strings are correct and are returned the same as the input
   */
	function testJsonpathPositive() {

    $jsonpaths = array(
      '$.store.book[*].author',
      '$..author',
      '$.store.*',
      '$.store..price',
      '$..book[2]	the third book',
      '$..book[(@.length-1)]',
      '$..book[-1:]',
      '$..book[0,1]',
      '$..book[:2]',
      '$..book[?(@.isbn)]',
      '$..book[?(@.price<10)]',
      '$..*'
    );

    foreach ( $jsonpaths as $jsonpath ) {
      $this->assertEquals( $jsonpath, wp_chatbot_validate_jsonpath( $jsonpath ) );
    }

	}

  /**
   * Tests negative scenario where invalid jsonpaths are sanetized
   */

  function testJsonpathNegative() {

    $jsonpaths = array(
      '.store.book[*].author'
    );

    foreach ( $jsonpaths as $jsonpath ) {
      $this->assertNotEquals( $jsonpath, wp_chatbot_validate_jsonpath( $jsonpath ) );
    }

	}

}
