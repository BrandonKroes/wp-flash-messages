<?php /*
    Plugin Name: WP Flash Messages
    Plugin URI: http://webpresencepartners.com
    Description: Easily Show Flash Messages in WP Admin UPDATE Using Session i.o get_option and a new function to prevent duplicates
    Version: 1.1
    Author: Daniel Grundel, Web Presence Partners
    Author URI: http://webpresencepartners.com
*/

/**
 * Class WPFlashMessages
 */
class WPFlashMessages {

	/**
	 * WPFlashMessages constructor.
	 */
	public function __construct() {
		add_action('admin_notices', array(&$this, 'show_flash_messages'));
	}


	/**
	 * @param $message
	 * @param string $class
	 *
	 * @return bool | false if queuing failed | true if queuing succeeded
	 */
	public static function queue_flash_message($message, $class = '') {

		$default_allowed_classes = array('error', 'updated', 'alert-danger');
		$allowed_classes = apply_filters('flash_messages_allowed_classes', $default_allowed_classes);
		$default_class = apply_filters('flash_messages_default_class', 'updated');

		if(!in_array($class, $allowed_classes)) $class = $default_class;

		$flash_messages = maybe_unserialize(get_option('wp_flash_messages', array()));

		//If the message with the corresponding class isn't found it will be added to array
		if(false == self::check_flash_message_exists( $message, $class)) {
			$_SESSION['wp_flash_messages'][ $class ][] = $message;
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Checks if the attempted message already exists in the wp_flash_message array
	 * Will prevent a message from appearing twice after a double load
	 * @param string $message will be used to assign the message variable
	 * @param string $class will be used to group messages
	 *
	 * @return bool | false if flash message doesn't exist | true if message exists
	 */
	public static function check_flash_message_exists ($message, $class) {
		if ( isset( $_SESSION['wp_flash_messages'] ) ) {
			$array = $_SESSION['wp_flash_messages'];

				foreach ( $array[ $class ] as $row => $row_message ) {
					if ( $row_message == $message ) {
						return true;
					}
			}
			return false;
		}
		return false;
	}


	/*
	 * Loops through all the flash messages and gives the messages sorted by class
	 * Initially there will be a check if the session already posses the wp_flash_messages
	 * If it doesn't contain the rest of the function serves no purpose
	 * This function needs to be called on every page you want the notifications to be displayed.
	 */
	public static function show_flash_messages() {
		if(isset($_SESSION['wp_flash_messages'])) {
			$flash_messages = maybe_unserialize( $_SESSION['wp_flash_messages'] );
			if ( is_array( $flash_messages ) ) {
				foreach ( $flash_messages as $class => $messages ) {
					foreach ( $messages as $message ) {
						?>
					<div class="<?php echo $class; ?>"><p><?php echo $message; ?></p></div><?php
					}
				}
			}
			//clear flash messages
			unset( $_SESSION['wp_flash_messages'] );
		}
	}
}

new WPFlashMessages();
//convenience function
if( class_exists('WPFlashMessages') && !function_exists('queue_flash_message') ) {
	function queue_flash_message($message, $class = null) {
		WPFlashMessages::queue_flash_message($message, $class);
	}
}
