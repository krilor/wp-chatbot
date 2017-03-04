<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class WP_Chatbot_Admin {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0

	 */
	public function __construct( ) {

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'wp-chatbot', plugin_dir_url( __DIR__ ) . 'css/wp-chatbot-admin.css', array(), '0.1.0', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'wp-chatbot', plugin_dir_url( __DIR__ ) . 'js/wp-chatbot-admin.js', array( 'jquery' ), '0.1.0', false );

	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		add_submenu_page( 'options-general.php',
					  __( 'WP Chatbot Settings','wp-chatbot' ),
					  __( 'WP Chatbot', 'wp-chatbot' ),
					  'manage_options',
					  'wp-chatbot-options',
					  array( $this, 'display_options_page' ) );
					}

	/**
	 * Registers settings for admin panel
	 */
	public function register_settings() {


		register_setting(
			'wp-chatbot-options-general',
			'wp-chatbot-options-general',
			array(
				'sanetize_callback' => array( $this, 'santize_callback_general' ),
				)
		);

		add_settings_section(
			'wp-chatbot-section-general',
			__( 'General settings', 'wp-chatbot' ),
			array( $this, 'display_section_info_general'),
			'wp-chatbot-options-general'
		);


		add_settings_field(
			'chatbot-title',
			__( 'Title', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-general',
			'wp-chatbot-section-general',
			array(
				'desc' => __( 'Chatbot Title', 'wp-chatbot' ),
				'id' => 'chatbot-title',
				'type' => 'text',
				'setting' => 'wp-chatbot-options-general',
				'classes' => 'regular-text'
			)
		);

		add_settings_field(
			'chatbot-livechat',
			__( 'Chatbot livechat', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-general',
			'wp-chatbot-section-general',
			array(
				'desc' => __( 'Add chatbot livechat button. Will disable the wp-chatbot shortcode.', 'wp-chatbot' ),
				'id' => 'chatbot-livechat',
				'type' => 'checkbox',
				'setting' => 'wp-chatbot-options-general'
			)
		);

		/**
		 * Filters the available integrations for the request
		 */
		$integrationtypes = apply_filters( 'wp_chatbot_integration_types', array(
			'WP_Chatbot_Request' => __( 'Generic request settings', 'wp-chatbot' )
		) );


		add_settings_field(
			'integration-type',
			__( 'Integration type', 'wp-chatbot' ),
			array( $this, 'display_input_selection' ),
			'wp-chatbot-options-general',
			'wp-chatbot-section-general',
			array(
				'desc' => __( 'Integration', 'wp-chatbot' ),
				'id' => 'integration-type',
				'type' => 'radio',
				'values' => $integrationtypes,
				'setting' => 'wp-chatbot-options-general'
			)
		);


		/**
		 * Used for storing the generic request options
		 */
		register_setting(
			'wp-chatbot-options-integration',
			'wp-chatbot-options-request',
			array(
				'sanitize_callback' => array( $this, 'sanitize_callback_request' ),
				)
		);

		add_settings_section(
			'wp-chatbot-section-request',
			__( 'Generic Integration Settings', 'wp-chatbot' ),
			array( $this, 'display_section_info_request'),
			'wp-chatbot-options-integration'
		);

		add_settings_field(
			'endpoint-url',
			__( 'Request Url', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-integration',
			'wp-chatbot-section-request',
			array(
				'desc' => __( 'The url endpoint for the Chatbot', 'wp-chatbot' ),
				'id' => 'endpoint-url',
				'type' => 'text',
				'setting' => 'wp-chatbot-options-request',
				'classes' => 'large-text'
			)
		);

		add_settings_field(
			'request-method',
			__( 'Request method', 'wp-chatbot' ),
			array( $this, 'display_input_selection' ),
			'wp-chatbot-options-integration',
			'wp-chatbot-section-request',
			array(
				'desc' => __( 'GET or POST', 'wp-chatbot' ),
				'id' => 'request-method',
				'type' => 'radio',
				'values' => array(
					'GET' => __( 'GET request', 'wp-chatbot' ),
					'POST' => __( 'POST request', 'wp-chatbot' )
				),
				'setting' => 'wp-chatbot-options-request'
			)
		);

		add_settings_field(
			'request-param-num',
			__( 'Number of request parameters', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-integration',
			'wp-chatbot-section-request',
			array(
				'desc' => __( 'Num', 'wp-chatbot' ),
				'id' => 'request-param-num',
				'type' => 'number',
				'setting' => 'wp-chatbot-options-request'
			)
		);

		$options = get_option( 'wp-chatbot-options-request' );

		$num_request_param = isset( $options['request-param-num'] ) ? intval( $options['request-param-num'] ) : 4;

		// Params
		for ( $i = 1; $i <= $num_request_param; $i++ ) {

			$option_id = sprintf( 'request-param-%d', $i );

			add_settings_field(
				$option_id,
				sprintf( __( 'Request parameter #%d', 'wp-chatbot' ), $i ),
				array( $this, 'display_input_generic' ),
				'wp-chatbot-options-integration',
				'wp-chatbot-section-request',
				array(
					'desc' => __( 'Parameter name', 'wp-chatbot' ),
					'id' => $option_id,
					'type' => 'text',
					'setting' => 'wp-chatbot-options-request'
				)
			);

			add_settings_field(
				$option_id . '-val',
				sprintf( __( 'Request parameter #%d value', 'wp-chatbot' ), $i ),
				array( $this, 'display_input_generic' ),
				'wp-chatbot-options-integration',
				'wp-chatbot-section-request',
				array(
					'desc' => __( 'Parameter value', 'wp-chatbot' ),
					'id' => $option_id . '-val',
					'type' => 'text',
					'setting' => 'wp-chatbot-options-request',
					'classes' => 'regular-text'
				)
			);

			}

		// Headers
		add_settings_field(
			'request-headers-num',
			__( 'Number of request headers', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-integration',
			'wp-chatbot-section-request',
			array(
				'desc' => __( 'Num', 'wp-chatbot' ),
				'id' => 'request-headers-num',
				'type' => 'number',
				'setting' => 'wp-chatbot-options-request'
			)
		);
		$num_request_headers = isset( $options['request-headers-num'] ) ? intval( $options['request-headers-num'] ) : 1;

		// Headers
		for ( $i = 1; $i <= $num_request_headers; $i++ ) {

			$option_id = sprintf( 'request-headers-%d', $i );

			add_settings_field(
				$option_id,
				sprintf( __( 'Request header #%d', 'wp-chatbot' ), $i ),
				array( $this, 'display_input_generic' ),
				'wp-chatbot-options-integration',
				'wp-chatbot-section-request',
				array(
					'desc' => __( 'Header name', 'wp-chatbot' ),
					'id' => $option_id,
					'type' => 'text',
					'setting' => 'wp-chatbot-options-request'
				)
			);

			add_settings_field(
				$option_id . '-val',
				sprintf( __( 'Request header #%d value', 'wp-chatbot' ), $i ),
				array( $this, 'display_input_generic' ),
				'wp-chatbot-options-integration',
				'wp-chatbot-section-request',
				array(
					'desc' => __( 'Header value', 'wp-chatbot' ),
					'id' => $option_id . '-val',
					'type' => 'text',
					'setting' => 'wp-chatbot-options-request',
					'classes' => 'regular-text'
				)
			);

			}

		add_settings_field(
			'response-jsonpath',
			__( 'Response JSONpath', 'wp-chatbot' ),
			array( $this, 'display_input_generic' ),
			'wp-chatbot-options-integration',
			'wp-chatbot-section-request',
			array(
				'desc' => __( 'The <a href="http://goessner.net/articles/JsonPath/">JSONpath</a> of the message in the returned JSON.', 'wp-chatbot' ),
				'id' => 'response-jsonpath',
				'type' => 'text',
				'setting' => 'wp-chatbot-options-request',
				'classes' => 'regular-text'
			)
		);

	}

	/**
	 * General sanetizing of options for wp_chatbot_options_request
	 *
	 * @param array $option Array of options.
	 * @return array Sanetized options
	 */
	public function sanitize_callback_request( $option ) {

		foreach ( $option as $setting => $value ) {

			switch ( $setting ) {

				case 'endpoint-url':
					$option[ $setting ] = esc_url( $value );
					break;

				case 'request-method';
				  $option[ $setting ] = wp_chatbot_sanetize_requestmethod( $value );
					break;

				case 'response-jsonpath':
				  $option[ $setting ] = wp_chatbot_sanetize_jsonpath( $option['response-jsonpath'] );

					if ( ! wp_chatbot_validate_jsonpath( $option[ $setting ] ) ) {
						add_settings_error(
							'wp-chatbot-options-request',
							esc_attr( 'request-jsonpath-invalid' ),
							__('The jsonpath is invalid.','wp-chatbot')
						);
					}

					break;

				default:
				  $option[ $setting ] = sanitize_text_field( $value );
					break;

			}

		}

		return $option;
	}

	/**
	 * General options page
	 */
	public function display_options_page() {

		$page = 'wp-chatbot-options';

		$active_tab = isset( $_GET[ 'tab' ] ) ? $active_tab = $_GET[ 'tab' ] : 'general';

		$active_pagetab = $page . '-' . $active_tab;

		/**
		 * Filters the array of tabs to be added to the wp-chatbot-options page.
		 *
		 * To add tab - append slug => name pair.
		 */
		$tabs = apply_filters( 'wp_chatbot_options_tabs', array(
			'general' => __('General', 'wp-chatbot'),
			'integration' => __('Integration', 'wp-chatbot')
		) );

		?>
		<div class="wrap">

		<h1><?php _e('WP Chatbot Settings','wp-chatbot');?></h1>

		<h2 class="nav-tab-wrapper">
		<?php
			foreach ( $tabs as $tab => $name ) {
				printf( '<a href="?page=%s&amp;tab=%s" class="nav-tab %s">%s</a>',
								$page,
								$tab,
								$tab == $active_tab ? 'nav-tab-active' : '',
								$name );
			}
		?>
		</h2>


		<form method="post" action="options.php">

		<?php

			submit_button();

			settings_fields( $active_pagetab );
			do_settings_sections( $active_pagetab );

			submit_button();

		?>
		</form>
		</div>
		<?php
	}

	/**
	 * Display a test area
	 */
	public function display_request_test() {

		// test
		return False;

	}

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

	/** Callback for generic integration settings description
	 *
	 */
	 public function display_section_info_general(){
		 echo '<p>'.__('General settings for the WP Chatbot.','wp-chatbot' ) . '</p>';
	 }
	 public function display_section_info_request(){
		 echo '<p>'.__('The generic integration settings can be used for creating any request and response mapping.','wp-chatbot' ) . '</p>';
	 }
}
