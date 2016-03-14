# WebMan WordPress Theme Framework Changelog

## 1.3

* **Add**: WordPress 4.5 custom logo support
* **Add**: Support for WP ULike plugin in post meta
* **Update**: Removing obsolete `get_theme_slug()` method
* **Update**: Created dedicated constant for (parent) theme version
* **Update**: Moved and renamed WordPress native theme customizer settings and sections
* **Update**: PHP inline comments formating
* **Update**: Improved support for ThemeCheck.org check algorithm
* **Update**: Stylesheets
* **Fix**: Fixed SSL issues

#### Files changed:

	init.php
	css/about.css
	css/admin.css
	css/customize.css
	css/rtl-about.css
	css/rtl-admin.css
	css/rtl-customize.css
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-updater.php
	includes/classes/class-visual-editor.php
	includes/external/class-tgm-plugin-activation.php
	scss/about.scss
	scss/admin.scss
	scss/customize.scss
	scss/rtl-about.scss
	scss/rtl-admin.scss
	scss/rtl-customize.scss


## 1.2.1

* **Update**: Improving admin notice styles

#### Files changed:

	init.php
	includes/classes/class-core.php


## 1.2

* **Update**: Improving files loading
* **Update**: Porting theme "About" admin page functionality into simple version of the framework
* **Update**: Updated TGM Plugin Activation class

#### Files changed:

	init.php
	includes/admin.php
	includes/external/class-tgm-plugin-activation.php


## 1.1

* **Update**: Changing `locate_template()` for `require_once()` file loading
* **Update**: Adding links on post publish date meta
* **Update**: Removing theme slug constant
* **Update**: Adding dismissible admin notices
* **Fix**: Fixing TGM Plugin Activation admin notice position

#### Files changed:

	init.php
	readme.md
	includes/admin.php
	includes/customize.php
	includes/update-notice.php
	includes/visual-editor.php
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-updater.php
	includes/external/class-tgm-plugin-activation.php


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
	includes/classes/class-admin.php
	includes/classes/class-core.php
	includes/classes/class-customize.php
	includes/classes/class-updater.php


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
	includes/admin.php
	includes/customize.php
	includes/update-notifier.php
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


## 1.0.10

* **Update**: Removed custom color picker styling

#### Files changed:

	init.php
	scss/customize.scss


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
	includes/admin.php
	includes/customize.php
	includes/update-notifier.php
	includes/visual-editor.php
	includes/classes/class-customize.php


## 1.0.7

* **Update**: Renaming the `lib` folder to `library`

#### Files changed:

	init.php
	readme.md


## 1.0.6

* **Update**: SASS files optimized

#### Files changed:

	scss/about.scss
	scss/customize.scss


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
* **Update**: Adding JS comments
* **Fix**: Returning site logo "pre" hook output instead of echoing

#### Files changed:

	inc/classes/class-core.php
	inc/classes/class-customize.php
	js/customize.js


## 1.0

* Initial release - Resetting versioning as this was complete recode from previous framework versions (last one was v4.0.4).


*WebMan WordPress Theme Framework, (C) WebMan Design, Oliver Juhas, www.webmandesign.eu*