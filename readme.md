# WordPress Theme Framework

**WordPress Theme Framework** main library by [*WebMan Design*](http://www.webmandesign.eu)

This framework, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.


## Theme integration

1. Copy the `library` folder into your theme's root
2. Include the library the first thing in your theme's `functions.php` file with `require_once( 'library/init.php' );`

> This theme framework is best suited for integration with starter themes by WebMan Design. And **requires at least WordPress 4.4**.

If you want to use a theme Update Notifier (do not use the script with WordPress.org repository hosted themes) integrate it as follows:

1. Copy the `update-notifier` folder into your theme's `includes` folder
2. In your theme's `functions.php` file load the update notifier with `require_once( 'includes/update-notifier.php' );`


*(C) Copyright WebMan Design, Oliver Juhas*
