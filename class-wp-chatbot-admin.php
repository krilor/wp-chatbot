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
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-chatbot-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-chatbot-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		add_submenu_page( 'options-general.php', // top level page slug
					  __( 'WP Chatbot Settings','wp-chatbot' ), // page title
					  'WP Chatbot', // menu title
					  'manage_options', // role
					  'wp-chatbot-options-general', // page slug
					  array( $this, 'display_options_page' ) ); // callback
	}

	/**
	 * Registers settings for admin panel
	 */
	public function register_settings() {

		// register setting
		register_setting(
			'wp-chatbot-options-general', // group/page slug slug
			'wp-chatbot-options-request', // setting name
			array(
				'sanitize_callback' => array( $this, 'sanitize_callback_request' ),
				)
		);

		register_setting(
			'wp-chatbot-options-general', // group/page slug slug
			'wp-chatbot-options-general', // setting name
			array(
				'sanetize_callback' => array( $this, 'santize_callback_general' ),
				)
		);

		add_settings_section(
			'wp-chatbot-section-general',	// ID used to identify this section and with which to register options
			__( 'General settings', 'wp-chatbot' ),		// Title to be displayed on the administration page
			'__return_false',	// Callback used to render the description of the section
			'wp-chatbot-options-general'		// Page on which to add this section of options
		);


		add_settings_field(
			'chatbot-title', // id
			__( 'Title', 'wp-chatbot' ), // Label
			array( $this, 'display_input_generic' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-section-general',	 // section
			array(	// args for callback
				'desc' => __( 'Chatbot Title', 'wp-chatbot' ),
				'id' => 'chatbot-title',
				'type' => 'text',
				'setting' => 'wp-chatbot-options-general'
			)
		);

		add_settings_field(
			'chatbot-livechat', // id
			__( 'Chatbot livechat', 'wp-chatbot' ), // Label
			array( $this, 'display_input_generic' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-section-general',	 // section
			array(	// args for callback
				'desc' => __( 'Add chatbot livechat button. Will disable the wp-chatbot shortcode.', 'wp-chatbot' ),
				'id' => 'chatbot-livechat',
				'type' => 'checkbox',
				'setting' => 'wp-chatbot-options-general'
			)
		);

		$integrationtypes = apply_filters( 'wp_chatbot_integration_types', array(
			'WP_Chatbot_Request' => __( 'Generic request settings', 'wp-chatbot' )
		) );


		add_settings_field(
			'integration-type', // id
			__( 'Integration type', 'wp-chatbot' ), // Label
			array( $this, 'display_input_selection' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-section-general',	 // section
			array(	// args for callback
				'desc' => __( 'Integration', 'wp-chatbot' ),
				'id' => 'integration-type',
				'type' => 'radio',
				'values' => $integrationtypes,
				'setting' => 'wp-chatbot-options-general'
			)
		);


		add_settings_section(
			'wp-chatbot-general-section',	// ID used to identify this section and with which to register options
			__( 'Request Settings', 'wp-chatbot' ),		// Title to be displayed on the administration page
			'__return_false',	// Callback used to render the description of the section
			'wp-chatbot-options-general'		// Page on which to add this section of options
		);

		add_settings_field(
			'endpoint-url', // id
			__( 'Request Url', 'wp-chatbot' ), // Label
			array( $this, 'display_input_generic' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-general-section', // section
			array(	// args for callback
				'desc' => __( 'The url endpoint for the Chatbot', 'wp-chatbot' ),
				'id' => 'endpoint-url',
				'type' => 'text',
			)
		);

		add_settings_field(
			'request-method', // id
			__( 'Request method', 'wp-chatbot' ), // Label
			array( $this, 'display_input_selection' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-general-section', // section
			array(	// args for callback
				'desc' => __( 'GET or POST', 'wp-chatbot' ),
				'id' => 'request-method',
				'type' => 'radio',
				'values' => array(
					'GET' => __( 'GET request', 'wp-chatbot' ),
					'POST' => __( 'POST request', 'wp-chatbot' )
				)
			)
		);

		add_settings_field(
			'request-param-num',						// ID used to identify the field throughout the plugin
			__( 'Number of request parameters', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
			'wp-chatbot-options-general',	// The page on which this option will be displayed
			'wp-chatbot-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( 'Num', 'wp-chatbot' ),
				'id' => 'request-param-num',
				'type' => 'number',
			)
		);

		$options = get_option( 'wp-chatbot-options-request' );

		$num_request_param = isset( $options['request-param-num'] ) ? intval( $options['request-param-num'] ) : 4;

		// Params
		for ( $i = 1; $i <= $num_request_param; $i++ ) {

			$option_id = sprintf( 'request-param-%d', $i );

			add_settings_field(
				$option_id,						// ID used to identify the field throughout the plugin
				sprintf( __( 'Request parameter #%d', 'wp-chatbot' ), $i ),							// The label to the left of the option interface element
				array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
				'wp-chatbot-options-general',	// The page on which this option will be displayed
				'wp-chatbot-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( 'Parameter name', 'wp-chatbot' ),
					'id' => $option_id,
					'type' => 'text',
				)
			);

			add_settings_field(
				$option_id . '-val',						// ID used to identify the field throughout the plugin
				sprintf( __( 'Request parameter #%d value', 'wp-chatbot' ), $i ),							// The label to the left of the option interface element
				array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
				'wp-chatbot-options-general',	// The page on which this option will be displayed
				'wp-chatbot-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( 'Parameter value', 'wp-chatbot' ),
					'id' => $option_id . '-val',
					'type' => 'text',
				)
			);

			} // for

		// Headers
		add_settings_field(
			'request-headers-num',						// ID used to identify the field throughout the plugin
			__( 'Number of request headers', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
			'wp-chatbot-options-general',	// The page on which this option will be displayed
			'wp-chatbot-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( 'Num', 'wp-chatbot' ),
				'id' => 'request-headers-num',
				'type' => 'number',

			)
		);
		$num_request_headers = isset( $options['request-headers-num'] ) ? intval( $options['request-headers-num'] ) : 1;

		// Headers
		for ( $i = 1; $i <= $num_request_headers; $i++ ) {

			$option_id = sprintf( 'request-headers-%d', $i );

			add_settings_field(
				$option_id,						// ID used to identify the field throughout the plugin
				sprintf( __( 'Request header #%d', 'wp-chatbot' ), $i ),							// The label to the left of the option interface element
				array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
				'wp-chatbot-options-general',	// The page on which this option will be displayed
				'wp-chatbot-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( 'Header name', 'wp-chatbot' ),
					'id' => $option_id,
					'type' => 'text',
				)
			);

			add_settings_field(
				$option_id . '-val',						// ID used to identify the field throughout the plugin
				sprintf( __( 'Request header #%d value', 'wp-chatbot' ), $i ),							// The label to the left of the option interface element
				array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
				'wp-chatbot-options-general',	// The page on which this option will be displayed
				'wp-chatbot-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( 'Header value', 'wp-chatbot' ),
					'id' => $option_id . '-val',
					'type' => 'text',
				)
			);

			} // for

		add_settings_field(
			'response-jsonpath',						// ID used to identify the field throughout the plugin
			__( 'Response JSONpath', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'display_input_generic' ),	// The name of the function responsible for rendering the option interface
			'wp-chatbot-options-general',	// The page on which this option will be displayed
			'wp-chatbot-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( 'The <a href="http://goessner.net/articles/JsonPath/">JSONpath</a> of the message in the returned JSON.', 'wp-chatbot' ),
				'id' => 'response-jsonpath',
				'type' => 'text',
			)
		);

		add_settings_section(
			'wp-chatbot-test-request-section',	// ID used to identify this section and with which to register options
			__( 'Test the settings', 'wp-chatbot' ),		// Title to be displayed on the administration page
			array( $this, 'display_request_test' ),	// Callback used to render the description of the section
			'wp-chatbot-options-general'		// Page on which to add this section of options
		);

	}

	/**
	 * General sanetizing of options for wp_chatbot_options_request
	 *
	 * @param array $option Array of options.
	 * @return array Sanetized options
	 */
	public function sanitize_callback_request( $option ) {
		// Response jsonpath

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

		?>
		<div class="wrap">
 		<h1><?php _e('WP Chatbot','wp-chatbot');?></h1>
		<form method="post" action="options.php">

		<?php

		settings_fields( 'wp-chatbot-options-general' );
		do_settings_sections( 'wp-chatbot-options-general' ); 	// pass slug name of page
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
			'setting' => 'wp-chatbot-options-request'
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
			'<input name="%s[%s]" id="%s" type="%s" value="%s" %s /> <span class="wp-chatbot-setting-desc">%s</span>',
			$args['setting'],
			$args['id'],
			$args['id'] + ( 'radio' == $args[ 'type' ] ? $args[ 'value' ] : '' ),
			$args['type'],
			$args['value'],
			$attrs,
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
			'setting' => 'wp-chatbot-options-request',
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
