<?php /*
	 Plugin Name: WP Flash Messages
	 Plugin URI: http://webpresencepartners.com
	 Description: Easily Show Flash Messages in WP Admin UPDATE Using Session i.o get_option and a new function to prevent duplicates
	 Version: 1.2
	 Author: Daniel Grundel, Web Presence Partners, Brandon Kroes
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
		add_action( 'admin_notices', array( &$this, 'show_flash_messages' ) );
	}

	/**
	 * @param $message
	 * @param string $class
	 *
	 * @return bool | false if queuing failed | true if queuing succeeded
	 * @throws Exception
	 */
	public static function queue_flash_message( $message, $class = '' ) {
		if ( empty( $message ) ) {
			throw new \Exception( 'message is empty' );
		}

		$message = sanitize_text_field( $message );

		if ( isset( $class ) ) {
			$class = sanitize_text_field( $class );
		}

		$default_allowed_classes = array( 'error', 'updated', 'alert-danger' );
		$allowed_classes         = apply_filters( 'flash_messages_allowed_classes', $default_allowed_classes );
		$default_class           = apply_filters( 'flash_messages_default_class', 'updated' );

		if ( ! in_array( $class, $allowed_classes ) ) {
			$class = $default_class;
		}

		//If the message with the corresponding class isn't found it will be added to array
		if ( false == self::check_flash_message_exists( $message, $class ) ) {
			$_SESSION['wp_flash_messages'][ $class ][] = $message;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if the attempted message already exists in the wp_flash_message array
	 * Will prevent a message from appearing twice after a double load
	 *
	 * @param string $message will be used to assign the message variable
	 * @param string $class will be used to group messages
	 *
	 * @return bool | false if flash message doesn't exist | true if message exists
	 * @throws Exception
	 */
	public static function check_flash_message_exists( $message, $class = '' ) {
		//Message must be filled, class can be empty
		if ( empty( $message ) ) {
			throw new \Exception( 'message is empty' );
		}

		$message = sanitize_text_field( $message );


		if ( isset( $_SESSION['wp_flash_messages'] ) ) {
			$array = $_SESSION['wp_flash_messages'];

			//Search through all existing classes
			if ( empty( $class ) ) {
				foreach ( $array as $class ) {
					foreach ( $class as $row => $row_message ) {
						if ( $row_message == $message ) {
							return true;
						}
					}
				}

				return false;
			}

			//Search through a specific class, class can be defined here due to the nature of the search
			$class = sanitize_text_field( $class );
			foreach ( $array[ $class ] as $row => $row_message ) {
				if ( $row_message == $message ) {
					return true;
				}
			}

			return false;
		}

		return false;
	}

	/**
	 * Deleting a user based
	 *
	 * @param $message
	 * @param string $class
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function delete_flash_message_from_queue( $message, $class = '' ) {

		if ( empty( $message ) ) {
			throw new \Exception( 'message is empty' );
		}
		if ( empty( $class ) ) {
			throw new \Exception( 'class is empty' );
		}

		$message = sanitize_text_field( $message );
		$class   = sanitize_text_field( $class );

		$found = self::find_flash_message_from_queue( $message, $class );

		//$found = (int)$found;

		if ( ! is_bool( $found ) ) {
			unset( $_SESSION['wp_flash_messages'][ $class ][ $found ] );
			/*
			* Due to the nature of unset, there won't be a return if it succeeded or not hence the double find
			 */
			if ( is_bool( self::find_flash_message_from_queue( $message, $class ) ) ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param $message
	 * @param string $class
	 *
	 * @return bool|int
	 * @throws Exception
	 */
	public static function find_flash_message_from_queue( $message, $class = '' ) {

		if ( empty( $message ) ) {
			throw new \Exception( 'message is empty' );
		}
		if ( empty( $class ) ) {
			throw new \Exception( 'class is empty' );
		}

		$message = sanitize_text_field( $message );
		$class   = sanitize_text_field( $class );

		$record = false;
		for ( $i = 0; $i < count( $_SESSION['wp_flash_messages'][ $class ] ); $i ++ ) {
			if ( $_SESSION['wp_flash_messages'][ $class ][ $i ] = $message ) {
				$record = $i;
			}
		}

		if ( empty( $record ) ) {
			return false;
		}

		//Returns at which index at the corresponding class it is.
		return $record;
	}


	/*
	   * Loops through all the flash messages and gives the messages sorted by class
	   * Initially there will be a check if the session already posses the wp_flash_messages
	   * If it doesn't contain the rest of the function serves no purpose
	   * This function needs to be called on every page you want the notifications to be displayed.
	   */

	public static function show_flash_messages() {
		if ( isset( $_SESSION['wp_flash_messages'] ) ) {
			$flash_messages = maybe_unserialize( $_SESSION['wp_flash_messages'] );

			//If is_array returns false there aren't any messages set
			if ( is_array( $flash_messages ) ) {
				foreach ( $flash_messages as $class => $messages ) {
					foreach ( $messages as $message ) {
						?>
            <div class="<?php echo $class; ?>"><p><?php echo $message; ?></p></div><?php
					}
				}
				//clear flash messages
				unset( $_SESSION['wp_flash_messages'] );
			}
		}
	}

	/**
	 * Checks if the array is the array contains any classes, a class must be connected to a message
	 * @return bool | if the array is empty : true | if the array is filled : false
	 */
	public static function check_if_empty() {
		//If the value isn't set the array is undefined therefore empty
		if ( isset( $_SESSION['wp_flash_messages'] ) ) {

			//if the array isn't empty false will returned
			if ( ! empty( $_SESSION['wp_flash_messages'] ) ) {

				return false;
			}

			return true;
		}

		return true;
	}
}
