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
class WP_Chatbot_Admin extends WP_Chatbot_Admin_Base {


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


	public function register_settings_apiai(){
		register_setting(
		 'wp-chatbot-options-integration',
		 'wp-chatbot-options-apiai'
	 	);

		 add_settings_section(
			 'wp-chatbot-section-apiai',
			 __( 'Api.ai Integration', 'wp-chatbot-apiai' ),
			 '__return_false',
			 'wp-chatbot-options-integration'
		 );


		 add_settings_field(
			 'client-token',
			 __( 'Client Token', 'wp-chatbot' ),
			 array( $this, 'display_input_generic' ),
			 'wp-chatbot-options-integration',
			 'wp-chatbot-section-apiai',
			 array(
				 'desc' => __( 'The <a href="https://docs.api.ai/docs/authentication">client access token</a> for your api.ai agent of choice. Do <b>NOT</b> include "Bearer ", only the token.', 'wp-chatbot' ),
				 'id' => 'client-token',
				 'type' => 'text',
				 'setting' => 'wp-chatbot-options-apiai',
				 'classes' => 'large-text'
			 )
		 );
	}

	/**
	 * Registers settings for admin panel
	 */
	public function register_settings() {


		register_setting(
			'wp-chatbot-options-general',
			'wp-chatbot-options-general'
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
			'WP_Chatbot_Request' => __( 'Generic request settings', 'wp-chatbot' ),
			'WP_Chatbot_ApiAi_Request' => __( 'Api.ai integration', 'wp-chatbot' ),
			'WP_Chatbot_Local_Callback' => __( 'Local function callback', 'wp-chatbot' )
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
			array($this, 'sanitize_callback_request')
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
?>
