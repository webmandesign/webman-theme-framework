# WebMan WordPress Theme Framework (Simple) Changelog

## 1.7

* **Update**: Improving customizer preview JS, also allowing use of both `css` and `custom`

#### Files changed:

	init.php
	includes/classes/class-customize.php


## 1.6.3

* **Fix**: Customizer empty label PHP error

#### Files changed:

	init.php
	includes/classes/class-customize.php


## 1.6.2

* **Add**: Child theme generator (Use Child Theme script)

#### Files changed:

	init.php
	includes/admin.php
	includes/vendor/tgmpa/class-tgm-plugin-activation.php


## 1.6.1

* **Update**: Improving security by using `wp_strip_all_tags` and `wp_kses` instead of `strip_tags`
* **Update**: Welcome page styles (preventing page overflowing)

#### Files changed:

	init.php
	includes/classes/class-core.php
	scss/styles/_welcome.scss


## 1.6

* **Add**: Functionality for conditional CSS comments
* **Add**: Added support for `input_attrs` customizer option setup
* **Update**: Skip to content link text updated
* **Update**: Optimized customizer class
* **Update**: TGM Plugin Activation 2.6.1
* **Update**: Welcome screen styles

#### Files changed:

	init.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/vendor/tgmpa/class-tgm-plugin-activation.php
	scss/styles/_welcome.scss


## 1.5

* **Add**: Customizer checkbox field custom styles
* **Add**: Customizer `css_output` theme options setting
* **Update**: Renaming them "About" page to "Welcome" page
* **Update**: Files loading order
* **Update**: Not using ID selectors in CSS
* **Update**: Improved RTL stylesheets loading
* **Update**: Using `__CLASS__` wherever possible
* **Update**: Using theme Schema.org class instead of function
* **Update**: Removed obsolete hook priority setup
* **Update**: Changed priority of core class loading
* **Fix**: Allowing theme to set an image logo as predefined one (if text logo needed, user need to set custom logo image and then remove it)

#### Files changed:

	init.php
	includes/admin.php
	includes/customize.php
	includes/visual-editor.php
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-visual-editor.php
	scss/welcome-rtl.scss
	scss/welcome.scss
	scss/styles/_welcome.scss


## 1.4

* **Add**: Including TGM Plugin Activation script into simple framework
* **Update**: Changed custom CSS variables format to `[[theme_mod_name]]`, `[[theme_mod_name(alpha_value)]]` respectively
* **Update**: Renamed `external` folder name to `vendor`
* **Update**: Moving TGM Plugin Activation script into `includes/vendor/tgmpa` folder
* **Update**: Custom `get_theme_mod()` made compatible with WordPress native function
* **Update**: Improved customizer preview JS
* **Update**: Escaping `get_stylesheet_directory_uri()` output
* **Fix**: TinyMCE Format button custom formats array sorting
* **Fix**: Localization functions

#### Files changed:

	init.php
	includes/admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-visual-editor.php


## 1.3.3

* **Update**: Improving SSL URL fixer
* **Update**: Removing obsolete PHP comment

#### Files changed:

	init.php
	includes/classes/class-core.php


## 1.3.1

* **Update**: Improved logo function for better compatibility with customizer partial refresh
* **Update**: Removing obsolete version numbers

#### Files changed:

	init.php
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-visual-editor.php


## 1.3

* **Add**: WordPress 4.5 custom logo support
* **Add**: Support for WP ULike plugin in post meta
* **Update**: Removing obsolete `get_theme_slug()` method
* **Update**: Created dedicated constant for (parent) theme version
* **Update**: Moved and renamed WordPress native theme customizer settings and sections
* **Update**: PHP inline comments formating
* **Update**: Stylesheets
* **Fix**: Fixed SSL issues

#### Files changed:

	init.php
	css/about.css
	css/rtl-about.css
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-visual-editor.php
	scss/about.scss
	scss/rtl-about.scss


## 1.2

* **Add**: Theme "About" admin page functionality

#### Files changed:

	init.php
	css/about.css
	css/rtl-about.css
	includes/admin.php
	includes/classes/class-admin.php
	scss/about.scss
	scss/rtl-about.scss


## 1.1

* **Update**: Changing `locate_template()` for `require_once()` file loading
* **Update**: Adding links on post publish date meta
* **Update**: Removing theme slug constant
* **Update**: Adding dismissible admin notices

#### Files changed:

	init.php
	readme.md
	includes/customize.php
	includes/visual-editor.php
	includes/classes/class-core.php
	includes/classes/class-customize.php


## 1.0.16

* **Update**: Removing obsolete file reference
* **Fix**: Fixing typos

#### Files changed:

	init.php
	includes/customize.php
	includes/classes/class-customize.php


## 1.0.15

* **Add**: Added method to get the (parent) theme folder name
* **Fix**: Compatibility with child themes
* **Fix**: Stripping HTML tags in Post Views Count plugin output to prevent errors in theme HTML

#### Files changed:

	init.php
	includes/classes/class-core.php
	includes/classes/class-customize.php


## 1.0.14

* **Update**: Renaming the `customizer_js` to `preview_js` and improving the function

#### Files changed:

	init.php
	includes/classes/class-customize.php


## 1.0.13

* **Update**: Renaming the `include` folder to `includes`
* **Update**: Updating `{%= prefix_constant %}_INCLUDE_DIR` constant name to `{%= prefix_constant %}_INCLUDES_DIR`
* **Update**: Library files paths
* **Update**: Theme files paths

#### Files changed:

	init.php
	includes/customize.php
	includes/visual-editor.php
	includes/classes/class-customize.php


## 1.0.12

* **Update**: Compatibility with Readability.com

#### Files changed:

	init.php
	includes/classes/class-core.php


## 1.0.11

* **Fix**: Customizer custom option preview JS

#### Files changed:

	init.php
	includes/customize.php


## 1.0.9

* **Update**: Improved flexibility of accessibility skip link

#### Files changed:

	init.php
	includes/classes/class-core.php


## 1.0.8

* **Update**: Renaming the `inc` folder to `include`
* **Update**: Updating `{%= prefix_constant %}_INCLUDE_DIR` constant
* **Update**: Library files paths
* **Update**: Theme files paths

#### Files changed:

	init.php
	includes/customize.php
	includes/visual-editor.php
	includes/classes/class-customize.php


## 1.0.7

* **Update**: Renaming the `lib` folder to `library`
* **Update**: Renaming `{%= prefix_constant %}_INC_DIR` constant to `{%= prefix_constant %}_INCLUDES_DIR`

#### Files changed:

	init.php
	readme.md
	inc/customize.php


## 1.0.5

* **Fix**: Time post meta info markup

#### Files changed:

	inc/classes/class-core.php


## 1.0.4

* **Add**: Adding description text for post meta info

#### Files changed:

	inc/classes/class-core.php


## 1.0.3

* **Add**: Support for WordPress 4.4

#### Files changed:

	inc/classes/class-core.php


## 1.0.2

* **Update**: Localization

#### Files changed:

	inc/classes/class-core.php


## 1.0.1

* **Add**: Support for Jetpack logo refresh in customizer
* **Update**: Removing obsolete variables
* **Fix**: Returning site logo "pre" hook output instead of echoing

#### Files changed:

	inc/classes/class-core.php
	inc/classes/class-customize.php


## 1.0

* Initial release - Resetting versioning as this was complete recode from previous framework versions (last one was v4.0.4).


*WebMan WordPress Theme Framework (Simple), (C) WebMan Design, Oliver Juhas, www.webmandesign.eu*