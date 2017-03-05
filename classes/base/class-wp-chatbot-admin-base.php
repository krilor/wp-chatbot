<?php


if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WP_Chatbot_Admin_Base' ) ):

class WP_Chatbot_Admin_Base {


  /**
	 * Print input elements, generic callback.
	 */
	public function display_input_generic( $args ) {

		$defaults = array(
			'id' => null,
			'type' => 'text',
			'desc' => '',
			'min' => 0,
			'max' => 20,
			'value' => '',
			'setting' => null,
			'classes' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		$options = get_option( $args['setting'] );

		if ( 'checkbox' == $args[ 'type' ] ) {

			$args[ 'value' ] = '1';

	  } else if ( 'number' == $args[ 'type' ] && '' == $args[ 'value' ] ) {

			$args[ 'value' ] = ( isset( $args['id'] ) && isset( $options[ $args['id'] ] ) ) ? $options[ $args['id'] ] : '0';

	  } else if ( 'radio' != $args[ 'type' ] ) {

			$args[ 'value' ] = ( isset( $args['id'] ) && isset( $options[ $args['id'] ] ) ) ? $options[ $args['id'] ] : '';

		}



		$attrs = ' ';

		switch( $args['type'] ){
			case 'number':
				$attrs .= sprintf( 'min="%d" max="%d"', $args['min'], $args['max'] );
				break;
			case 'checkbox':
			  $attrs .= checked( isset( $options[ $args['id'] ] ), true , false );
				break;
			case 'radio':
			  $attrs .= checked( isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '' , $args[ 'value' ] , false );
				break;
		}

		printf(
			'<input name="%s[%s]" id="%s" type="%s" value="%s" %s class="%s"/> <span class="wp-chatbot-setting-desc description">%s</span>',
			$args['setting'],
			$args['id'],
			$args['id'] + ( 'radio' == $args[ 'type' ] ? $args[ 'value' ] : '' ),
			$args['type'],
			$args['value'],
			$attrs,
			$args['classes'],
			$args['desc']
		);
	}

	/**
	 * Print input where the user can select one or multiple values.
	 */
	public function display_input_selection( $args ) {

		// TODO: Add <select> input type

		$defaults = array(
			'id' => null,
			'type' => 'radio',
			'multiple' => false, // if the user can select multiple values
			'values' => array(), // value - description pairs
			'setting' => null,
			'classes' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		$options = get_option( $args['setting'] );

 		foreach( $args[ 'values' ] as $value => $description ) {
			$this->display_input_generic( wp_parse_args( array(

				'value' => $value,
				'desc' => $description

			), $args ) );

		}

	}

}

endif; // WP_Chatbot_Admin_Base exists
