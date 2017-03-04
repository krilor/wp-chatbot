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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'classes/class-wp-chatbot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_wp_chatbot() {
	$plugin = new WP_Chatbot();
	$plugin->run();
}
run_wp_chatbot();
?>
