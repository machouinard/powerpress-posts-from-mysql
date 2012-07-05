=== PowerPress Posts From MySQL ===
Contributors: machouinard
Donate link: http://markchouinard.com
Tags: powerpress, podcasting, mysql
Requires at least: 3.0
Tested up to: 3.3.2
Stable tag: 0.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will create WordPress posts for podcasting with PowerPress using information from a MySQL table.

== Description ==

Information required from the database:
Host, Database Name, Database Table Name, Database Username and Database Password.
Also field names from the database which will be used for the following:
Title, Category, Post Body, Featured Image(URL to an image), Media URL, Media size, Media type and Date posted.



== Installation ==

This section describes how to install the plugin and get it working.
(make sure you have created the category in your blog before running or all the podcasts will be uncategorized)

1. Upload the folder containing `pfd.php` and `process.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. Configure your database settings in `Post From MySQL` under the `Tools` menu in the Dashboard and click `Save Changes`
4. After you've saved your settings, click `Process` and sit tight.


== Frequently Asked Questions ==

= I think something's not right.  Can you fix it? =

I'll do my best.  Post a message at <http://plugins.markchouinard.me/powerpress-plugin/> explaining what's up and I'll get to work making it right.

= Does the MySQL table have to be on the same DB Host as my WordPress install? =

I don't believe so, but I honestly haven't tested that.  Yet.

== Screenshots ==

1. Settings Page from Post From MySQL in the Tools Menu
2. This is the second screen shot

== Changelog ==



== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`
