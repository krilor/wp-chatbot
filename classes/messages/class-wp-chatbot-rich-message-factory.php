<?php
/**
 * @author Maniae
 */

if( ! class_exists( 'WP_Chatbot_Rich_Message_Factory' ) ):

	class WP_Chatbot_Rich_Message_Factory {

		/**
		 * Array mapping api.ai types to the corresponding class
		 * 0 for the Text response message type
		 * 1 for the Card message type
		 * 2 for the Quick replies message type
		 * 3 for the Image message type
		 * 4 is the Custom Payload message type, inside which the other types are found
		 * See <a href="https://docs.api.ai/docs/query">https://docs.api.ai/docs/query</a>
		 */
		private static $map = array(
				0 => 'WP_Chatbot_Rich_Message_Text',
				// No implementation of Card message yet
				2 => 'WP_Chatbot_Rich_Message_Quick_Replies',
				3 => 'WP_Chatbot_Rich_Message_Image',
		);

		public static function create( $message ) {
			$type = $message['type']; // 0 or 4
			if ($type == 4) {
					$message = $message['payload'];
					$type = $message['type']; // 0, 1, 2, or 3
			}
			$rich_message = new self::$map[ $type ]( $message );
			return $rich_message;
		}
	}


endif;

?>