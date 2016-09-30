=== WP Simple Calendar ===
Contributors: 9seeds, vegasgeek, themightymo, brandondove
Donate Link: http://9seeds.com/donate/
Tags: calendar
Requires at least: 3.0
Tested up to: 3.7.1
Stable tag: 1.1.1
License: GPLv2 or later

A simple grid view calendar

== Description ==

A simple grid view calendar with minimal functionality, but easy to add in your own customizations

== Installation ==

1. Upload `wp-simple-calendar` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in your WordPress dashboard

== Frequently Asked Questions ==

= How do I display the calender? =
The calendar can be displayed in either a grid view or a list view using shortcodes in your posts and pages.

For grid view:
`[wpscgrid category="category-slug" location="location-slug"]`

For list view:
`[wpsclist quantity="5" category="category-slug"]`

== Screenshots ==

== Changelog ==

= 1.1.1 =
* Created a more efficient and accurate method of querying for events in the wpsclist shortcode
* Added an Event Start Date column to the admin screen

= 1.1.0 =
* Updated Colorbox javascript
* Fixed the wpsclist shortcode so that it works
* Fix a date/time bug (using WordPress current_time() function instead of PHP date() function to get current date)
* Cleaned up some invalid HTML code
* Shortened table headings to make the grid view more mobile display friendly
* Fixed IE javascript bug that broke previous / next buttons on grid view calendar

= 1.0.2 =
* Added ajax for previous / next month navigation
* Fixed a bug on pop-up if no location was selected

= 1.0.1 =
* Fixed issue with single page pulling incorrect location (thanks numeeja)
* Updated single page to use site's date format (thanks numeeja)

= 1.0 =
* Initial release
