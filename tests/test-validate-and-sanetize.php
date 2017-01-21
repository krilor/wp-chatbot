<?php
class WP_Chatbot_Validator_Test extends WP_UnitTestCase {

  /**
   * Correct jsonpaths are seen as correct by validator
   */
	function testValidateJsonpathTrue() {

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
      '$..*',
      "$['store']['book'][0]['title']",
    );

    foreach ( $jsonpaths as $jsonpath ) {
      $this->assertTrue( wp_chatbot_validate_jsonpath( $jsonpath ), $jsonpath );
    }

	}

  /**
   * Tests negative scenario with invalid jsonpaths
   */
  function testValidateJsonpathFalse() {

    $jsonpaths = array(
      '.store.book[*].author',
      '$.store.book[.test',
      True,
      1,
      '$'
    );

    foreach ( $jsonpaths as $jsonpath ) {
      $this->assertFalse( wp_chatbot_validate_jsonpath( $jsonpath ), $jsonpath );
    }

	}

  /**
   * Tests negative scenario with invalid bracket pairing
   */
  function testValidatePairedBracketsFalse() {

    $strings = array(
      '.store.book[*).author',
      '.store.book[.test',
      'test{',
      '} string',
      'intertwined[(])',
      'it2{(sadn}daga)'
    );

    foreach ( $strings as $string ) {
      $this->assertFalse( wp_chatbot_validate_paired_brackets( $string ), $string );
    }

  }

  /**
   * Tests positive scenario with invalid bracket pairing
   */
  function testValidatePairedBracketsTrue() {

    $strings = array(
      '$.store.book[*].author',
      '$.stor'
    );

    foreach ( $strings as $string ) {
      $this->assertTrue( wp_chatbot_validate_paired_brackets( $string ), $string );
    }

  }

}
