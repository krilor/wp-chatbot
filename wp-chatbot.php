<?php
/**
 * WP Chatbot
 *
 * @package     WPChatbot
 * @author      Kristoffer Lorentsen
 * @copyright   2017 Kristoffer Lorentsen
 * @license     GPL-3.0
 *
 * @wordpress-plugin
 * Plugin Name: WP Chatbot
 * Plugin URI: https://wordpress.org/plugins/wp-chatbot/
 * Description: Simple, yet powerful plugin to include any chatbot on your site
 * Version: 0.1.0
 * Author: Kristoffer Lorentsen
 * Author URI: http://lorut.no
 * Text Domain: wp-chatbot
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Base classes
*/
require_once plugin_dir_path( __FILE__ ) . 'classes/base/class-wp-chatbot-base.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/base/class-wp-chatbot-admin-base.php';
require_once plugin_dir_path( __FILE__ ) . 'classes/base/class-wp-chatbot-message.php';


/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class WP_Chatbot extends WP_Chatbot_Base {
	/* Singleton */

	public $admin;
	public $public;

	/**
	 * Main plugin class instance
	 *
	 * Modelled after EDD, to insure that there is only one instance of the plugin at the time. Prevents globals. Follows singleton pattern.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ){

			$class = self::class;

			self::$instance = new $class;

		}

		return self::$instance;

	}


	public function __construct() {

		parent::__construct();

		$this->require( '/assets/jsonpath.php' );

		$this->require( '/includes/validate-and-sanetize.php' );
		$this->require( '/includes/utils.php' );

		$this->require( '/classes/class-wp-chatbot-admin.php' ) ;
		$this->require( '/classes/class-wp-chatbot-public.php' );
		$this->require( '/classes/class-wp-chatbot-request.php' );
		$this->require( '/classes/class-wp-chatbot-conversation.php' );

		$this->admin = new WP_Chatbot_Admin();
		$this->public = new WP_Chatbot_Public( $this->slug, $this->version );

		$this->define_admin_hooks();
		$this->define_public_hooks();

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );

		// Menus
		add_action( 'admin_menu', array( $this->admin, 'admin_menu' ) );
		add_action( 'admin_init', array( $this->admin, 'register_settings' ) );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this->public, 'enqueue_scripts' ) );

		// Ajax callbacks for conversation
		add_action( 'wp_ajax_nopriv_wp_chatbot_converse', array( $this->public, 'wp_chatbot_converse' ) );
		add_action( 'wp_ajax_wp_chatbot_converse', array( $this->public, 'wp_chatbot_converse' ) );


		$options_general = get_option( 'wp-chatbot-options-general' );

		if ( isset( $options_general[ 'chatbot-livechat']) and $options_general[ 'chatbot-livechat'] == '1' ) {
			add_action( 'wp_footer', array( $this->public, 'chat_interface_livechat' ) );
		} else {
			add_shortcode( 'wp-chatbot', array( $this->public, 'chat_interface_shortcode' ));
		}

	}

}

/**
 * Begins execution of the plugin.
 *
 * Can also be used to get the plugin singleton
 *
 * @since    0.1.0
 */
function wp_chatbot() {

	return WP_Chatbot::instance();

}
wp_chatbot();
?>
