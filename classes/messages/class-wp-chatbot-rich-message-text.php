<?php
/**
 * @author Maniae
 */

if( ! class_exists( 'WP_Chatbot_Rich_Message_Text' ) ):

	class WP_Chatbot_Rich_Message_Text implements WP_Chatbot_Rich_Message {

		/**
		 * Type of message
		 */
		public $type;

		/**
		 * The message text
		 */
		public $speech;

		public function __construct( $message ){
			$this->type = 'textResponse';
			$this->speech = $message['speech'];
		}

		public function get_contents(){
			return get_object_vars( $this );
		}
	}

endif;

?>