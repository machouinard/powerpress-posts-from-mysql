=== PowerPress Posts From MySQL ===
Contributors: machouinard
Donate link: http://markchouinard.com
Tags: powerpress, podcasting, mysql
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: 0.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Have a bunch of podcasts stored on your server and aren't excited about creating each post manually? This will help.

== Description ==

By populating a MySQL database table with all the information about the podcasts you can automatically create the posts needed for podcasting with the [Blubrry PowerPress plugin](http://wordpress.org/extend/plugins/powerpress/ "WordPress Podcasting").

Information required from the database:

* Host
* Database Name
* Database Table Name
* Database Username
* Database Password

Also field names from the database which will be used for the following:

* Primary Key Field
* Title
* Category
* Post Body
* Featured Image(URL to an image)
* Media URL
* Media size
* Media type
* Date posted

<strong>Note:</strong> I've rewritten this from the ground up and decided to learn Git while doing so.  You can find the repo at [https://github.com/machouinard/ppfm](https://github.com/machouinard/ppfm "PowerPress Posts From MySQL").

== Installation ==

This section describes how to install the plugin and get it working.
(make sure you have created the category in your blog before running or all the podcasts will be uncategorized)

1. Upload the folder containing pfd.php and process.php to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Configure your database settings in Post From MySQL under the Tools menu in the Dashboard and click Save Changes
4. After you've saved your settings and a connection is made, the total number of records will be displayed.

<strong>NOTE! For this plugin to work correctly, it requires the MySQL table to use a primary key. * see <em>[How should I set up the database table](http://wordpress.org/extend/plugins/powerpress-posts-from-mysql/faq/ "Frequently Asked Questions")?</em>in the FAQ for more information.</strong>


== Frequently Asked Questions ==

= How should I set up the database table? =

* Create fields that correspond to those on the MySQL Fields page of the plugin ( Post Title, Post Category, Post Body, Post/Featured Image, Podcast URL, Podcast Size, Podcast Media Type, Post Date ).
* Make sure to include a primary key.

= I think something's not right.  Can I fix it myself? =

By all means, have at it!  I decided to learn to use Git, so all the code is available at GitHub.  You can find the repo at [https://github.com/machouinard/powerpress-posts-from-mysql](https://github.com/machouinard/powerpress-posts-from-mysql "PowerPress Posts From MySQL").  Latest version is 0.9.8

= I think something's missing.  You botched something essential. =

Post the issue at the project's [GitHub page](https://github.com/machouinard/powerpress-posts-from-mysql/issues?page=1&state=open "GitHub page").


= Does the MySQL table have to be on the same DB Host as my WordPress install? =

No.
<<<<<<< HEAD
=======

== Screenshots ==
>>>>>>> origin/master


== Changelog ==
v 0.9.8

* (note: Most of this work was done months ago.  I got busy and forgot about it)
* Rewritten from the ground up
* Learned a lot about WordPress
* Learned a lot about Git, too.

v 0.9.4

* Added check to ensure BluBrry PowerPress is installed and activated
* More CSS and HTML changes in an attempt to pretty this thing up a bit
* Changed code to allow for localization
* Used Google Translate to create .mo files for:
* French
* Spanish - Spain/Ecuador
* Italian
* Danish - Denmark
* German
* Turkish

v 0.9.2

* Included a Primary Key field in the settings page
* Added ability to set the status of posts as either Published or Draft
* Made some aesthetic changes to the settings page using some CSS and jQuery
* Removed some unused code and comments from process.php

v 0.9.1

* Added database connectivity checking.
* Added check to prevent posting same podcast twice based on the podcast/post title.
* Added ability to post from a range of records in the table based on a specific database field.
* Added display of total records in table.



