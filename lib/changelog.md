# Changelog

## 4.0

* This is a major update/simplification
* ADDED: WordPress 4.1+ support
* ADDED: ZillaLikes native support
* UPDATED: Improved and optimized code
* UPDATED: Improved conditional loading of files
* UPDATED: Constants
* UPDATED: CSS minifier function
* UPDATED: Theme upgrade function/action
* UPDATED: Dropped IE8 support, using only modern CSS styles
* UPDATED: Localization functions and strings
* UPDATED: Customizer: options saving
* UPDATED: Customizer: custom controls
* UPDATED: Customizer: file paths for easier site transfer
* UPDATED: Customizer: renamed `wmhook_theme_options_skin_array` to `wmhook_theme_options`
* UPDATED: Theme update notifier: disabled by default
* UPDATED: Theme update notifier: texts and functions
* UPDATED: Visual editor addons are now optional
* FIXED: Hook names
* FIXED: Customizer: stylesheet enqueuing
* FIXED: Customizer: control type setup
* REMOVED: GZIP option from generating main CSS file
* REMOVED: Obsolete hooks and filters
* REMOVED: Obsolete functions (favicons, thumbnails, not-found, search form, comments navigation, integer sanitization, gallery and excerpt modifications)
* REMOVED: Renamed and/or removed files

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

* Improved admin notices

#### Files changed:
	admin.php
	core.php
	skinning.php


## 3.3

* Theme customizer improved

#### Files changed:
	skinning.php
	includes/sanitize.php


## 3.2

* WordPress 4.0 support
* Theme customizer update
* TGM-Plugin-Activation removed support for old WordPress versions

#### Files changed:
	skinning.php
	assets/css/theme-customizer.css
	includes/class-tgm-plugin-activation.php


## 3.1

* Theme Update Notifier modified
* Added filter to modify logo image attributes
* Added custom action hook for stylesheet regeneration
* Renamed wm_minimize_css() function to wm_minify_css()
* Skinning System update
* Fixed WooCommerce colors removing error
* Adding wm_get_stylesheet_directory() function
* `[gallery]` shortcode modification disabled by default
* `[caption]` shortcode modification removed

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

* Initial release for use with WebMan Amplifier plugin.


*WebMan WordPress Theme Framework, (C) 2015 WebMan, www.webmandesign.eu*