wp-flash-messages
=================

Simple flash message functionality for your WordPress plugin.

Use the function **queue_flash_message()** to enqueue a flash message that will be displayed on the next admin page load.

**queue_flash_message()** takes the following form:

```
<?php queue_flash_message( $message, $class = 'updated' ); ?>
```

The two parameters are:

- **$message** - The string to be displayed. HTML is okay, but keep in mind that your $message will be wrapped in a **p** and a **div** tag. (See output example below.)
- **$class** - the CSS class to be applied to the div element.

The output will look like this:

```html
<div class="$class">
	<p>$message</p>
</div>
```

*This is the same markup that WordPress generates for its own flash messages.*

By default, only the two built-in messages classes of **updated** and **error** are allowed, but you can modify the array of allowed classes using the **flash_messages_allowed_classes** filter, like so:

```
function my_flash_classes($allowed_classes) {
    $allowed_classes[] = 'notice'; //adds 'notice' class to allowed array
    return $allowed_classes;
}
add_filter('flash_messages_allowed_classes', 'my_flash_classes');
```

If an invalid class name is used when queueing a message, the default class **updated** is used instead. Of course, you can also change this with a filter:

```
function my_flash_default_class($default_class_name) {
    return 'error'; //makes 'error' the default class name
}
add_filter('flash_messages_default_class', 'my_flash_default_class');
```

# UPDATE 1.1 7th of March 2017

1. Prevent duplicate messages in same class
2. Using **$_SESSION** instead of **wp option**
3. Improved PHPDocs


### 1. Duplicate issue

In the previous version of *wp-flash-messages* it was possible to add the same message to the same corresponding class.
The static function *check_flash_message_exists* is used to check if a message already exists in the same class, but won't search the entire array for the value.


#### Avoidable situation

This is no longer possible, any attempt to add will first check if the previous value is already known
```php
Array
(
    ['updated'] => Array
        (
            [0] => "A basic flash message"
            [1] => "A basic flash message"
        )
)
```

#### However
This still is possible, due to limited scope of the *check_flash_message_exists*
```php
Array
(
    ['updated'] => Array
        (
            [0] => "A basic flash message"
        )
        
    ['error'] => Array
        (
            [0] => "A basic flash message"
        )
)
```

### 2. $_SESSION
$_SESSION is used instead of wp option to get consistency about the outcome
1. $_SESSION makes the messages only available to the corresponding user.
2. $_SESSION is faster than wp option.
3. $_SESSION won't save the messages when the user isn't active.


### 3. Update PHPDocs
Updated the functions to give a better description of what they actually do and what parameters they require.
