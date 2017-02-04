<?php
/**
 * Bootstrap to load the plugins files
 *
 * @package WP Chatbot
 */

require_once 'wordpress-tests-lib/includes/functions.php';

function _manually_load_environment() {

  // TODO Actually load the plugin as is update option load plugin
  require dirname( dirname( __FILE__ ) ) . '/wp-chatbot.php';

}
tests_add_filter( 'muplugins_loaded', '_manually_load_environment' );

require_once 'wordpress-tests-lib/includes/bootstrap.php';


//require_once('../validation.php'); // Valdation functions
?>
