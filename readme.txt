=== Debug Bar Screen Info ===
Contributors: bradvin, jrf
Tags: debug bar, debug, screen id, get_current_screen, WP_Screen, Screen Object
Requires at least: 3.5.1
Tested up to: 4.5
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show screen info of the current admin page in a new tab within the debug bar

== Description ==

> This plugin is an add-on for *Debug Bar*.
> You must install and activate [Debug Bar](https://wordpress.org/plugins/debug-bar/) first, for this plugin to work.

Adds a new tab to the debug bar which shows all the screen info for the current page in the admin backend.

Please note that this plugin should be used solely for debugging and/or on a development environment and is not intended for use on a production site.


***********************************

If you like this plugin, please [rate and/or review](https://wordpress.org/support/view/plugin-reviews/debug-bar-screen-info) it. If you have ideas on how to make the plugin even better or if you have found any bugs, please report these in the [Support Forum](https://wordpress.org/support/plugin/debug-bar-screen-info) or in the [GitHub repository](https://github.com/fooplugins/debug-bar-screen-info/issues).


== Frequently Asked Questions ==

= Can it be used on live site ? =
This plugin is only meant to be used for development purposes, but shouldn't cause any issues if run on a production site.

= Why should I be interested in the screen info ? =
Because you can use it to conditionally include files, add help tabs, enqueue scripts and styles etc for the admin pages. Lean loading FTW ;-)

= Where does the screen info come from ? =
The screen info is retrieved via the `get_current_screen()` function and contains a `WP_Screen` object.
> Role of WP_Screen
>
> This is a concrete class that is instantiated in the WordPress $screen global. It is primarily used to create and customize WordPress admin screens (as of WordPress 3.3).

[More information in the Codex](http://codex.wordpress.org/Class_Reference/WP_Screen)

= Why won't the plugin activate ? =
Have you read what it says in the beautiful red bar at the top of your plugins page ? As it says there, the Debug Bar plugin needs to be active for this plugin to work. If the Debug Bar plugin is not active, this plugin will automatically de-activate itself.


== Installation ==

1. Install Debug Bar if not already installed (https://wordpress.org/plugins/debug-bar/)
1. Extract the .zip file for this plugin and upload its contents to the `/wp-content/plugins/` directory. Alternatively, you can install directly from the Plugin directory within your WordPress Install.
1. Activate the plugin through the 'Plugins' menu in WordPress.


== Screenshots ==

1. New Screen Info Panel


== Changelog ==

= 1.1.5 =
* Hard-code the text-domain for better compatibility with [GlotPress](https://translate.wordpress.org/projects/wp-plugins/debug-bar-screen-info).
* Make loading of text-domain compatible with use of the plugin in the `must-use` plugins directory.
* Updated the pretty print class to v1.6.0.
* Tested & found compatible with WP 4.5
* Minor tidying up

= 1.1.4 =
* Updated the pretty print class.
* Tested & found compatible with WP 4.4
* Minor tidying up

= 1.1.3 =
* Fix compatibility with the [Plugin Dependencies](https://wordpress.org/plugins/plugin-dependencies/) plugin
* Updated the pretty print class which now allows for limiting of the recursion depth.
* Tested & found compatible with WP 4.2
* Minor tidying up
* Updated language files

= 1.1.2 =
* Fix for parentage not being set as admin bar is loaded too early - [issue #5](https://github.com/fooplugins/debug-bar-screen-info/issues/5). Thanks [grappler](https://github.com/grappler) for reporting.

= 1.1.1 =
* Variable values are now pretty printed with type indication by default.

= 1.1.0 =
* Merge with [Jrf](http://profiles.wordpress.org/jrf/)'s version of the same:
	- Now displays all available properties
	- Displays current screen id + property count at top of panel and link to WP_Screen at the bottom
	- Limits visibility to admin site as it's not relevant on the front-end
	- Auto-disables itself when Debug Bar is not activated
	- Added some css styling
	- Made text strings translatable, added .pot file and a Dutch translation


= 1.0.0 =
* First release


== Upgrade Notice ==

= 1.1.3 =
* Upgrade highly recommended - multi-plugin compatibility issue

= 1.1.1 =
* Upgrade highly recommended - multi-plugin compatibility issue

= 1.1.0 =
* Merge with [Jrf](http://profiles.wordpress.org/jrf/)'s version of the same