=== PowerPress Posts From MySQL ===
Contributors: machouinard
Donate link: http://markchouinard.com
Tags: powerpress, podcasting, mysql
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 0.9.4
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

<strong>Note:</strong> I'm in the process of rewriting this from the ground up.  I figured while I'm at it, I might as well learn to use Git and GitHub.  You can find the repo at [https://github.com/machouinard/ppfm](https://github.com/machouinard/ppfm "PowerPress Posts From MySQL").

== Installation ==

This section describes how to install the plugin and get it working.
(make sure you have created the category in your blog before running or all the podcasts will be uncategorized)

1. Upload the folder containing pfd.php and process.php to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Configure your database settings in Post From MySQL under the Tools menu in the Dashboard and click Save Changes
4. After you've saved your settings and a connection is made, the total number of records will be displayed.

<strong>NOTE! For this plugin to work correctly, it requires a field to refer to records by. * see <em>[How should I set up the database table](http://wordpress.org/extend/plugins/powerpress-posts-from-mysql/faq/ "Frequently Asked Questions")?</em>in the FAQ for more information.  The approach I've found best is to ensure all records are inserted into the table using an auto-incremented primary key such as <em>id</em>.</strong> 


== Frequently Asked Questions ==

= How should I set up the database table? =

* Create fields that correspond to those on the settings page.
* Make sure to include a field that can be used for referring to the records.  The default name for said field is <em>id</em>. <strong>It is <em>imperative</em> that this field be auto-incremented starting at 1</strong>.
* Populate the table with the podcast data using any method you feel comfortable.
* In my case the podcasts themselves were numbered from 1 to 465.  I wrote a PHP script to insert a new record for each podcast into the table.  I also included an 'id' field which I set as primary and auto-increment so after all the podcasts were entered into the table, both 'id' and 'number' fields ranged from 1 to 465. This may seem redundant, but I included the 'id' field to ensure continuity in case the podcast numbers became non-sequential at some point in the future.  As long as all records have an auto-incremented id field starting at 1 you should be able to run the plugin on multiple occasions adding only records that have been inserted since the previous run. And having a primary auto-incremented field is pretty standard to begin with, no?

= Can I help you translate this plugin? =

Sure! I've created a few using Google translate, but I'm not sure how accurate they are. 
The POT file is included in the plugin's languages directory. Feel free to use that and email me the .po file or contact me to discuss further 

= I think something's not right.  Can you fix it? =

I'll do my best.  Post a message at <http://plugins.markchouinard.me/powerpress-plugin/> explaining what's up and I'll get to work making it right.

= I think something's not right.  Can I fix it myself? =

By all means, have at it!  I decided to learn to use Git, so all the development is being done at GitHub.  You can find the repo at [https://github.com/machouinard/ppfm](https://github.com/machouinard/ppfm "PowerPress Posts From MySQL").  Latest version is 0.9.8

= I think something's missing.  You missed something essential. =

Again, post a message at <http://plugins.markchouinard.me/powerpress-plugin/> explaining what you would like to be added and I'll get right on that, Rose.

= Does the MySQL table have to be on the same DB Host as my WordPress install? =

I don't believe so, but I honestly haven't tested that.  Yet.

== Screenshots ==

1. Post From MySQL Settings Page in the Tools Menu
2. Once the database values are entered, a connection is made and you will be shown the total number of records the specified table holds.
3. If any of the values are incorrect you will be notified that there is no database connectivity.  Please check your settings and re-enter.
4. If the table name is either missing or incorrect, you will be notified of that as well.  Please check the data and re-enter.
5. Once all the settings have been saved and verified you will be ready to create your podcasts.  This is where you can specify the Start and End Records to be used. Here I used id's 340 - 360 (21 records).  If either the Start Record or End Record fields are left blank they will default to the first and last record, respectively. If both fields are left blank, podcast posts will be created for every record. You also have the option to mark the post status as either Published or Draft.
6. After clicking process, the posts will be created and you will be shown the number of podcasts posted and the number of podcasts not posted due to a duplicate title being found. The post status you previously chose will also be shown.  If you have a better idea about checking for duplicate podcasts please let me know.
The range used to select the records is also displayed.
7. Here I have lowered the Start number by 15 to demonstrate what happens when duplicate posts have been found...
8. The 21 records previously published (340 - 360) have been skipped while the 15 new records (325 - 339) have been succesfully posted.

== Changelog ==
v 0.9.8

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


== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing


`<?php code(); // goes in backticks ?>`
