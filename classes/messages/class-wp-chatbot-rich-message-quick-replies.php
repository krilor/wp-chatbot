<?php
/**
 * @author Maniae
 */

if( ! class_exists( 'WP_Chatbot_Rich_Message_Quick_Replies' ) ):

	class WP_Chatbot_Rich_Message_Quick_Replies implements WP_Chatbot_Rich_Message {

		/**
		 * Type of message
		 */
		public $type;

		/**
		 * The message text
		 */
		public $title;

		/**
		 * The array of strings corresponding to quick replies
		 */
		public $replies;

		public function __construct( $message ){
			$this->type = 'quickReplies';
			$this->title = $message['title'];
			$this->replies = $message['replies'];
		}

		public function get_contents(){
			return get_object_vars( $this );
		}
	}


endif;

?>