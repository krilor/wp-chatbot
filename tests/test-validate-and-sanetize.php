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

  /**
   * Tests positive scenario with checkbox values
   */
  function testValidateCheckboxTrue() {

    $strings = array(
      '1',
      null
    );

    foreach ( $strings as $string ) {
      $this->assertTrue( wp_chatbot_validate_checkbox( $string ), $string );
    }

  }

  /**
   * Tests negative scenario with checkbox values
   */
  function testValidateCheckboxFalse() {

    $strings = array(
      '2',
      'wrong',
      1
    );

    foreach ( $strings as $string ) {
      $this->assertFalse( wp_chatbot_validate_checkbox( $string ), $string );
    }

  }

  /**
   * Tests sanetizing of checkbox values. Output should either be 1 when there is a string
   */
  function testSanetizeCheckboxChecked() {

    $strings = array(
      '2',
      'some-values',
      '1'
    );

    foreach ( $strings as $string ) {
      $this->assertEquals( wp_chatbot_sanetize_checkbox( $string ), 1 , $string );
    }

  }

  /**
   * Tests sanetizing of checkbox values. Output should either be null if null
   */
  function testSanetizeCheckboxUnChecked() {

    $strings = array(
      null
    );

    foreach ( $strings as $string ) {
      $this->assertEquals( wp_chatbot_sanetize_checkbox( $string ), null , $string );
    }

  }

  /**
   * Tests validation of requestmethod with true result. Only GET and POST is allowed
   */
  function testValidateRequestMethodTrue() {

    $strings = array(
      'GET',
      'POST'
    );

    foreach ( $strings as $string ) {
      $this->assertTrue( wp_chatbot_validate_requestmethod( $string ) , $string );
    }

  }

  /**
   * Tests validation of requestmethods with false result. Only GET and POST is allowed
   */
  function testValidateRequestMethodFalse() {

    $strings = array(
      'get',
      'post',
      'gt',
      'test'
    );

    foreach ( $strings as $string ) {
      $this->assertFalse( wp_chatbot_validate_requestmethod( $string ) , $string );
    }

  }

}
