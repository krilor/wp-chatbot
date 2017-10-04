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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'css/wp-chatbot-pub.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'js/wp-chatbot-pub.js', array( 'jquery' ), $this->version, true );
		// wp_enqueue_script('animate-min', 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/104946/animate.min.css', array(), $this->version, true);
		/* Localized JS variables */
		wp_localize_script( 'wp-chatbot', 'wp_chatbot', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));
	}

	/**
	 * Return chatbot html
	 */
	public function chat_interface( $atts ) {
		$options = get_option( 'wp-chatbot-options-general' );
		$title = $options['chatbot-title'];
		$imageSrc = $options['image-url'];
		$introText = $options['intro-text'];
		$html = '<div class="chatbot-wrapper">';
			$html .= '<div class="chatbot-top"><div class="title">'.$title.'</div><div class="css-icon close"></div></div>';
			$html .= '<input type="hidden" id="image-url-input" value="'.$imageSrc.'" />';

			// $html .= '<div class="info-box">Random html<div class="css-icon arrow-up"></div></div>';
			$html .= '<div class="chatbot-inner">';
				$html .=  '<div id="intro-text-div" style="text-align: center; margin: 10%; color: #565a5e">'.$introText.'</div>';
				$html .= '<div class="chatbot-content" id="wp-chatbot-content"></div>';
			$html .= '</div>';
			$html .= '<div class="chatbot-bottom" id="bottom">';
				$html .= '<input type="text" class="input" id="input"></textarea>';
				$html .= '<div class="send css-icon" id="send"></div>';
			$html .= '</div>';
		$html .= '</div>';
		return $html;
	}

	/**
	 * Return shortcode html
	 */
	public function chat_interface_shortcode( $atts ) {

		$html = '<div class="chatbot-wrapper-shortcode">';
		$html .= $this->chat_interface( $atts );
		$html .= '</div>';

		return $html;
	}


	/**
	 * Return livechat html
	 */
	public function chat_interface_livechat( $atts ) {



		$html = '<div class="chatbot-wrapper-livechat">';
		$html .= $this->chat_interface( $atts );
		$html .= '</div>';
		$html .= '<div id="chatbot-launcher-icon"><div class="css-icon chat-solid"></div></div>';

		echo $html;
	}

	/**
	 * Ajax callback that passes message to API and back
	 */
	public function wp_chatbot_converse() {

		$message = sanitize_text_field( $_POST['message'] );

		$wcc = new WP_Chatbot_Conversation();
		$response = $wcc->say( $message );

		wp_send_json( apply_filters( 'wp_chatbot_response_output', array_merge( $response, array(
			'user_id' => $wcc->user,
			'conv_id' => $wcc->conversation,
			'response_to' => $message
		))));

	}
}
?>
