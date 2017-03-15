
###2. $_SESSION
$_SESSION is used instead of wp option to get consistency about the outcome
1. $_SESSION makes the messages only available to the corresponding user.
2. $_SESSION is faster than wp option.
3. $_SESSION won't save the messages when the user isn't active.


###3. Update PHPDocs
Updated the functions to give a better description of what they actually do and what parameters they require.



#UPDATE 1.1 9th of March 2017

1. New delete method
2. New find method

##1. Delete Method

Example
```php
	WPFlashMessages::delete_flash_message_from_queue( $message, $class );
```