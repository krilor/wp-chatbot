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

		add_settings_section(
			'wp-chatbot-general-section',	// ID used to identify this section and with which to register options
			__( 'Request Settings', 'wp-chatbot' ),		// Title to be displayed on the administration page
			'__return_false',	// Callback used to render the description of the section
			'wp-chatbot-options-general'		// Page on which to add this section of options
		);

		add_settings_field(
			'endpoint-url', // id
			__( 'Request Url', 'wp-chatbot' ), // Label
			array( $this, 'display_setting_generic' ), // display callback
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
			array( $this, 'display_setting_generic' ), // display callback
			'wp-chatbot-options-general', // page
			'wp-chatbot-general-section', // section
			array(	// args for callback
				'desc' => __( 'GET or POST', 'wp-chatbot' ),
				'id' => 'request-method',
				'type' => 'text',
			)
		);

		add_settings_field(
			'request-param-num',						// ID used to identify the field throughout the plugin
			__( 'Number of request parameters', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
				array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
				array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
			array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
				array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
				array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
			array( $this, 'display_setting_generic' ),	// The name of the function responsible for rendering the option interface
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
	 * General sanetizing of options for Request
	 *
	 * @param array $option Array of options.
	 * @return array Sanetized options
	 */
	public function sanitize_callback_request( $option ) {

		// Response jsonpath
		if ( isset( $option['response-jsonpath'] ) ) {

			$option['response-jsonpath'] = wp_chatbot_sanetize_jsonpath( $option['response-jsonpath'] );

			if ( '' == $option['response-jsonpath'] ){
				add_settings_error(
					'wp-chatbot-options-request',
					esc_attr( 'request-jsonpath-empty' ),
					__('The jsonpath is empty. Without it, the returned response cannot be interpreted.','wp-chatbot')
				);
			} else if ( ! wp_chatbot_validate_jsonpath( $option['response-jsonpath'] ) ) {
				add_settings_error(
					'wp-chatbot-options-request',
					esc_attr( 'request-jsonpath-invalid' ),
					__('The jsonpath is invalid. <i>Go fix it</i>','wp-chatbot')
				);
			}
		}

		return $option;
	}

	/**
	 * General options page
	 */
	public function display_options_page() {
		// include( plugin_dir_path( __FILE__ ) . 'partials/options-page.php' );
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
		// include( plugin_dir_path( __FILE__ ) . 'partials/options-page.php' );
		do_shortcode('[wp-chatbot]');

	}

	/**
	 * Print input elements, generic callback.
	 */
	public function display_setting_generic( $args ) {
		$options = get_option( $this->plugin_name . '-options-request' );

		$defaults = array(
			'id' => null,
			'type' => 'text',
			'description' => '',
			'min' => 0,
			'max' => 20
		);

		$args = wp_parse_args( $args, $defaults );

		$args['value'] = ( isset( $args['id'] ) && isset( $options[ $args['id'] ] ) ) ? $options[ $args['id'] ] : '';

		$attrs = ' ';

		switch( $args['type'] ) {
			case 'number':
				$attrs .= sprintf( 'min="%d" max="%d"', $args['min'], $args['max'] );
				break;
		}

		printf(
			'<input name="wp-chatbot-options-request[%s]" id="%s" type="%s" value="%s" %s /> <span class="wp-chatbot-setting-desc">%s',
			$args['id'],
			$args['id'],
			$args['type'],
			$args['value'],
			$attrs,
			$args['desc']
		);
	}
}
