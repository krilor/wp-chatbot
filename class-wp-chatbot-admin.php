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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
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
                      __('WP Chatbot Settings','wp-chatbot'), // page title
                      'WP Chatbot', // menu title
                      'manage_options', //role
                      $this->plugin_name . '-options-general', // page slug
                      array( $this, 'options_page_general_callback' ) ); //callback
  }

  /**
   * General options page
   */
  public function options_page_general_callback() {
    //include( plugin_dir_path( __FILE__ ) . 'partials/options-page.php' );
    ?>
    <form method="POST" action="options.php">
    <?php

      settings_fields( $this->plugin_name . '-options-general' ); // group used in register settings
      do_settings_sections( $this->plugin_name . '-options-general' ); 	//pass slug name of page
      submit_button();

    ?>
    </form>
    <?php
  }

  public function register_settings() {

    /*if( false == get_option( $this->plugin_name . '-options-general' ) ) {
  		add_option( $this->plugin_name . '-options-api', $this->default_options() );
  	} */

    // register setting
    register_setting(
      $this->plugin_name . '-options-general', // group/page slug slug
      $this->plugin_name . '-options-api', // setting name
			array(
				'sanetize_callback' => array( $this, 'santize_callback_api' )
				)
    );

    add_settings_section(
      $this->plugin_name .'-general-section',	// ID used to identify this section and with which to register options
      __( 'API Settings', $this->plugin_name ),		// Title to be displayed on the administration page
      '__return_false',	// Callback used to render the description of the section
      $this->plugin_name . '-options-general'		// Page on which to add this section of options
    );

		add_settings_field(
			'endpoint-url', // id
			__( 'API Url', $this->plugin_name ), // Label
			array( $this, 'general_setting_callback' ), // display callback
			$this->plugin_name . '-options-general', // page
			$this->plugin_name . '-general-section', // section
			array(	// args for callback
  			'desc' => __( 'The url endpoint for the Chatbot', $this->plugin_name ),
        'id' => 'endpoint-url',
				'type' => 'text'
  		)
		);

		add_settings_field(
			'request-method', // id
			__( 'Request method', $this->plugin_name ), // Label
			array( $this, 'general_setting_callback' ), // display callback
			$this->plugin_name . '-options-general', // page
			$this->plugin_name . '-general-section', // section
			array(	// args for callback
				'desc' => __( 'GET or POST', $this->plugin_name ),
				'id' => 'request-method',
				'type' => 'text'
			)
		);

		add_settings_field(
			'request-param-num',						// ID used to identify the field throughout the plugin
			__( 'Number of request parameters', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
			$this->plugin_name . '-options-general',	// The page on which this option will be displayed
			$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( 'Num', $this->plugin_name ),
				'id' => 'request-param-num',
				'type' => 'number'
			)
		);


		$options = get_option( $this->plugin_name . '-options-api');

		$num_request_param = isset( $options['request-param-num'] ) ? intval($options['request-param-num']) : 4;


		// Params
		for ( $i = 1; $i <= $num_request_param; $i++ ) {

			$option_id = sprintf( 'request-param-%d', $i );

			add_settings_field(
				$option_id,						// ID used to identify the field throughout the plugin
				__( sprintf( 'Request parameter #%d', $i ), 'wp-chatbot' ),							// The label to the left of the option interface element
				array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
				$this->plugin_name . '-options-general',	// The page on which this option will be displayed
				$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( sprintf( 'Parameter name', $i ), $this->plugin_name ),
					'id' => $option_id,
					'type' => 'text'
				)
			);

			add_settings_field(
				$option_id . '-val',						// ID used to identify the field throughout the plugin
				__( sprintf( 'Request parameter #%d value', $i ), 'wp-chatbot' ),							// The label to the left of the option interface element
				array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
				$this->plugin_name . '-options-general',	// The page on which this option will be displayed
				$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( sprintf( 'Parameter value', $i ), $this->plugin_name ),
					'id' => $option_id . '-val',
					'type' => 'text'
				)
			);

		} // for

		// Headers
		add_settings_field(
			'request-headers-num',						// ID used to identify the field throughout the plugin
			__( 'Number of request headers', 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
			$this->plugin_name . '-options-general',	// The page on which this option will be displayed
			$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( 'Num', $this->plugin_name ),
				'id' => 'request-headers-num',
				'type' => 'number'
			)
		);
		$num_request_headers = isset( $options['request-headers-num'] ) ? intval($options['request-headers-num']) : 1;

		// Headers
		for ( $i = 1; $i <= $num_request_headers; $i++ ) {

			$option_id = sprintf( 'request-headers-%d', $i );

			add_settings_field(
				$option_id,						// ID used to identify the field throughout the plugin
				__( sprintf( 'Request header #%d', $i ), 'wp-chatbot' ),							// The label to the left of the option interface element
				array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
				$this->plugin_name . '-options-general',	// The page on which this option will be displayed
				$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( sprintf( 'Header name', $i ), $this->plugin_name ),
					'id' => $option_id,
					'type' => 'text'
				)
			);

			add_settings_field(
				$option_id . '-val',						// ID used to identify the field throughout the plugin
				__( sprintf( 'Request header #%d value', $i ), 'wp-chatbot' ),							// The label to the left of the option interface element
				array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
				$this->plugin_name . '-options-general',	// The page on which this option will be displayed
				$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
				array(								// The array of arguments to pass to the callback. In this case, just a description.
					'desc' => __( sprintf( 'Header value', $i ), $this->plugin_name ),
					'id' => $option_id . '-val',
					'type' => 'text'
				)
			);

		} // for

		add_settings_field(
			'response-jsonpath',						// ID used to identify the field throughout the plugin
			__( sprintf( 'Response JSONpath', $i ), 'wp-chatbot' ),							// The label to the left of the option interface element
			array( $this, 'general_setting_callback' ),	// The name of the function responsible for rendering the option interface
			$this->plugin_name . '-options-general',	// The page on which this option will be displayed
			$this->plugin_name . '-general-section',			// The name of the section to which this field belongs
			array(								// The array of arguments to pass to the callback. In this case, just a description.
				'desc' => __( sprintf( 'The <a href="http://goessner.net/articles/JsonPath/">JSONpath</a> of the message in the returned JSON.', $i ), $this->plugin_name ),
				'id' => 'response-jsonpath',
				'type' => 'text'
			)
		);

  }

	/**
	 * Print general input elements
	 */
  public function general_setting_callback ( $args ) {
    $options = get_option( $this->plugin_name . '-options-api' );

		$defaults = array(
			'id' => null,
			'type' => 'text',
			'description' => ''
		);

		$args = wp_parse_args( $args, $defaults );

		$args['value'] = isset( $args['id'] ) && isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : '';


    printf(
			'<input name="%s-options-api[%s]" id="%s" type="%s" value="%s" /> <span class="%s-setting-desc">%s',
			$this->plugin_name,
			$args['id'],
			$args['id'],
			$args['type'],
			$args['value'],
			$this->plugin_name,
			$args['desc']
		);
  }

  public function default_options() {
    return array( 'api-key' => '' );
  }

	public function sanetize_options_api( $option ){
		#TODO: Actually sanetize the option
		return $option;
	}

}
