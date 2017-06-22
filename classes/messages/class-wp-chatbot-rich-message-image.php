<?php
/**
 * @author Maniae
 */

if( ! class_exists( 'WP_Chatbot_Rich_Message_Image' ) ):

	class WP_Chatbot_Rich_Message_Image implements WP_Chatbot_Rich_Message {

		/**
		 * Type of message
		 */
		public $type;

		/**
		 * The image url
		 */
		public $imageUrl;


		public function __construct( $message ){
			$this->type = 'image';
			$this->imageUrl = $message['imageUrl'];
		}

		public function get_contents(){
			return get_object_vars( $this );
		}
	}


endif;

?>