<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class WP_Chatbot_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-chatbot-pub.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-chatbot-pub.js', array( 'jquery' ), $this->version, true );
		// wp_enqueue_script('animate-min', 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/104946/animate.min.css', array(), $this->version, true);
		/* Localized JS variables */
		wp_localize_script( 'wp-chatbot', 'wp_chatbot', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));
	}

	/**
	 * Add all shortcodes that are
	 */
	public function chat_interface_shortcode( $atts ) {

		$html = '<div class="wrapper">';
		$html .= '<div class="inner" id="inner">';
		$html .= '<div class="content" id="wp-chatbot-content"></div>';
		$html .= '</div>';
		$html .= '<div class="bottom" id="bottom">';
		$html .= '<input type="text" class="input" id="input"></textarea>';
		$html .= '<button class="send" id="send">' . __( 'Send','wp-chatbot' ) . '</button>';
		$html .= '</div>';
		$html .= '</div>';
		// '<div class="wp-chatbot-interface"><div class="wp-chatbot-text"></div><input id="wp-chatbot-input" type="text"/><button class="wp-chatbot-button">CHAT</button></div>';
		return $html;
	}

	/**
	 * Ajax callback that passes message to API and back
	 */
	public function wp_chatbot_converse() {

		$message = sanitize_text_field( $_POST['message'] );

		/* Sessions and cookies */
		if ( ! session_id() ) {
		session_start();
		}

		$user_var = 'wp_chatbot_user';
		$conv_var = 'wp_chatbot_conv';
		// substr( md5( microtime() ), 0, 40 ) is used to generate a unique-ish id-string
		if ( ! isset( $_SESSION[ $conv_var ] ) ) {
			$conv = substr( md5( microtime() - rand( 0, 50000 ) ), 0, 40 );
			$_SESSION[ $conv_var ] = $conv;
			} else {
			$conv = $_SESSION[ $conv_var ];
			}

		  if ( ! isset( $_COOKIE[ $user_var ] ) ) {
			$user = substr( md5( microtime() - rand( 0, 50000 ) ), 0, 40 );
			} else {
		  $user = $_COOKIE[ $user_var ];
			}
		  setcookie( $user_var, $user , time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		  $wpcr = new WP_Chatbot_Request();
		  $response = $wpcr->request( $message, $user, $conv ); // TODO: Check response and add filter

		echo json_encode( array(
			'response' => $response,
			'response_to' => $message,
			'user_id' => $user,
			'conv_id' => $conv,
		  ));

		  wp_die();
	}
}
