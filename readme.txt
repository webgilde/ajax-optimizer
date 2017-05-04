=== AJAX Optimizer ===
Tags: AJAX, disable plugins, speed, performance
Requires at least: 4.7
Tested up to: 4.7.4
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to disable specific plugins for AJAX requests to speed up your sites.

== Description ==

By default, WordPress loads all plugins during AJAX requests in the frontend. This plugin allows to disable some of them to speed up those requests and your site in general.

Please make sure to read the Installation instructions before installing and activating the plugin.

We measured an increase in response time for AJAX requests from 30 to 60%.

= Best practices =

* normally, you can disable all plugins which don’t send AJAX requests
* all existing and newly installed plugins are enabled by default
* remove the plugin completely if you don’t need it

== Installation ==

1. Upload `ajax-optimizer` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Plugins > AJAX Optimizer` to activate the optimization and select the plugins which you want to disable for AJAX calls.

= about the mu-plugin =

AJAX Optimizer needs a so called mu-plugin to work. It will try to automatically create it as `wp-content/mu-plugins/ajax-optimizer-mu.php`.

You should check on `Plugins > AJAX Optimizer` if the plugin was created and if not, click on `Activate Optimizer` to create it.

If this does not work, then please copy this file manually from `wp-content/plugins/ajax-optimizer/mu`.

The mu-plugin will automatically be removed when the plugin is deleted (not when disabled), but you can remove it manually in the rare case that this did not work.
The same accounts for multisites.

The button to create the mu-plugin on **multisites** can only be found on the setup page of the main blog.

== Changelog ==

= 0.2 =
* Added Multisite support. It allows to select plugins for each site individually

= 0.1 =
* Initial version.