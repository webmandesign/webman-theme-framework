# WordPress Theme Framework

**WordPress Theme Framework** main library by [*WebMan Design*](http://www.webmandesign.eu)

This framework, like WordPress, is licensed under the GPL.
Use it to make something cool, have fun, and share what you've learned with others.

## Versions comparison

* **Complex** - Generates main CSS stylesheet. Contains additional functions/methods (in comparison to "simple" WordPress Theme Framework). Suited for more complex themes.
* **Simple** - Outputs custom styles directly into HTML head. Contains less functions/methods than "complex" WordPress Theme Framework. Suited for simpler themes.

## Theme integration

1. Copy the desired `library` folder into your theme's root
2. Include the library the first thing in your theme's `functions.php` file with `locate_template( 'library/init.php', true );`

> This theme framework is best suited for integration with starter themes by WebMan Design.

*(C) Copyright 2016 WebMan Design, Oliver Juhas*
