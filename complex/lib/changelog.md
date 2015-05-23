# WebMan WordPress Theme Framework Changelog

## 5.0

* **Update**: Code organized into PHP classes
* **Update**: Replaced pluggable functions functionality with "pre" hook
* **Update**: Removed obsolete constants and functions
* **Update**: Simplified code
* **Update**: Renamed textdomain
* **Update**: WordPress 4.1+ compatible only
* **Update**: Scripts: TGM Plugin Activation 2.4.2
* **Update**: Removed customizer skin creator/loader in favour for [Customizer Export/Import plugin](https://wordpress.org/plugins/customizer-export-import/)
* **Update**: Renamed hooks

#### Files changed:

	*.* (All)


## 4.0.4

* **Update**: TGMPA library

#### Files changed:

	inc/class-tgm-plugin-activation.php


## 4.0.3

* **Update**: TGMPA library `add_query_arg()` escaping fixed

#### Files changed:

	inc/class-tgm-plugin-activation.php


## 4.0.2

* **Update**: Adding support for `active_callback` in Customizer generator
* **Update**: Removed obsolete Customizer custom textarea control

#### Files changed:

	core.php
	customizer.php


## 4.0.1

* **Update**: Wrapping `get_permalink()` in `esc_url()`
* **Update**: Optimized Customizer custom option JS handling
* **Update**: Customizer sanitize functions improved
* **Update**: Visual editor script improved
* **Fix**: Localization

#### Files changed:

	core.php
	customizer.php
	inc/visual-editor.php


## 4.0

* **Add**: WordPress 4.1+ support
* **Add**: ZillaLikes native support
* **Add**: Post Views Count native support
* **Add**: Custom CSS variables replacer function
* **Update**: Improved and optimized code
* **Update**: Improved conditional loading of files
* **Update**: Constants
* **Update**: CSS minifier function
* **Update**: Theme upgrade function/action
* **Update**: Dropped IE8 support, using only modern CSS styles
* **Update**: Localization functions and strings
* **Update**: Customizer: options saving
* **Update**: Customizer: custom controls
* **Update**: Customizer: file paths for easier site transfer
* **Update**: Customizer: renamed `wmhook_theme_options_skin_array` to `wmhook_theme_options`
* **Update**: Theme update notifier: disabled by default
* **Update**: Theme update notifier: texts and functions
* **Update**: Visual editor addons are now optional
* **Update**: GZIP option from generating main CSS file
* **Update**: Obsolete hooks and filters
* **Update**: Obsolete functions (favicons, thumbnails, home query, not-found, search form, comments navigation, integer sanitization, gallery and excerpt modifications)
* **Update**: Renamed and/or removed files
* **Update**: This is a major update/simplification
* **Fix**: Hook names
* **Fix**: Customizer: stylesheet enqueuing
* **Fix**: Customizer: control type setup

#### Files changed:

	admin.php
	core.php
	customizer.php
	css/about.css
	css/admin.css
	css/customizer.css
	css/rtl-about.css
	css/rtl-admin.css
	css/rtl-admin-woocommerce.css
	inc/class-tgm-plugin-activation.php
	inc/hooks.php
	inc/update-notifier.php
	inc/visual-editor.php
	inc/controls/class-WM_Customizer_Hidden.php
	inc/controls/class-WM_Customizer_HTML.php
	inc/controls/class-WM_Customizer_Image.php
	inc/controls/class-WM_Customizer_Multiselect.php
	inc/controls/class-WM_Customizer_Radiocustom.php
	inc/controls/class-WM_Customizer_Range.php
	inc/controls/class-WM_Customizer_Textarea.php
	js/customizer-preview.js
	js/customizer.js


## 3.4

* **Update**: Improved admin notices

#### Files changed:

	admin.php
	core.php
	skinning.php


## 3.3

* **Update**: Theme customizer improved

#### Files changed:

	skinning.php
	includes/sanitize.php


## 3.2

* **Update**: WordPress 4.0 support
* **Update**: Theme customizer update
* **Update**: TGM-Plugin-Activation removed support for old WordPress versions

#### Files changed:

	skinning.php
	assets/css/theme-customizer.css
	includes/class-tgm-plugin-activation.php


## 3.1

* **Add**: Added filter to modify logo image attributes
* **Add**: Added custom action hook for stylesheet regeneration
* **Add**: Adding wm_get_stylesheet_directory() function
* **Update**: Theme Update Notifier modified
* **Update**: Renamed wm_minimize_css() function to wm_minify_css()
* **Update**: Skinning System update
* **Update**: `[gallery]` shortcode modification disabled by default
* **Update**: `[caption]` shortcode modification removed
* **Fix**: Fixed WooCommerce colors removing error

#### Files changed:

	admin.php
	core.php
	skinning.php
	assets/css/admin.css
	assets/css/rtl-admin.css
	assets/css/theme-customizer.php
	assets/img/contain.png
	assets/img/cover.png
	assets/img/default.png
	assets/img/no-repeat.png
	assets/img/repeat-x.png
	assets/img/repeat-y.png
	assets/img/repeat.png
	assets/js/customizer.js
	assets/js/wm-scripts.js
	includes/sanitize.php
	includes/controls/class-WM_Customizer_Hidden.php
	includes/controls/class-WM_Customizer_HTML.php
	includes/controls/class-WM_Customizer_Image.php
	includes/controls/class-WM_Customizer_Multicheckbox.php
	includes/controls/class-WM_Customizer_Radiocustom.php
	includes/controls/class-WM_Customizer_Slider.php
	includes/controls/class-WM_Customizer_Textarea.php
	updater/update-notifier.php


## 3.0

* Initial release for use with WebMan Amplifier plugin. (Previous versions were included in themes directly: v1.0 in Jaguar, v2.0 in Clifden and JazzMaster, v3.0 in !LesPaul.)


*WebMan WordPress Theme Framework, (C) 2015 WebMan, www.webmandesign.eu*