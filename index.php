<?php
/*
Plugin Name: Powerpress Posts From MySQL
Plugin URI: http://plugins.markchouinard.me
Description: Create Powerpress podcast posts from data stored in a MySQL table
Author: Mark Chouinard
Author URI: http://markchouinard.me
Version: 0.9.7.5
Requires: Blubrry PowerPress
Text Domain: powerpress-posts-from-mysql
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * Copyright (c) `date "+%Y"` Mark Chouinard. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

require_once 'connect.php';

add_action('admin_menu', 'mac_pfd_create_menu');
function mac_pfd_create_menu(){
	add_management_page(__('PFD Settings Page', 'powerpress-posts-from-mysql'), __('Post From MySQL', 'powerpress-posts-from-mysql'), 'manage_options', __FILE__, 'mac_pfd_settings_page');
}

wp_register_style('mac_pfd_style', plugins_url('css/style.css', __FILE__), array(), '0.9.7.5', 'all');
wp_enqueue_style('mac_pfd_style');
wp_enqueue_script('jquery');

/*  http://codex.wordpress.org/I18n_for_WordPress_Developers */    
function my_plugin_init(){
    $plugin_dir = basename(dirname(__FILE__));
    load_plugin_textdomain('powerpress-posts-from-mysql', false, $plugin_dir.'/languages/');
}    
add_action('plugins_loaded', 'my_plugin_init');



function mac_pfd_settings_page(){
	//must check that the user has the required capability 
	if (!current_user_can('manage_options')){
	       wp_die(__('You do not have sufficient permissions to access this page.', 'powerpress-posts-from-mysql'));
	    }



	/* Check to see if PowerPress is installed and activated.  This plugin is useless without it! */
	if(defined('POWERPRESS_VERSION') ){
		    $mac_pfd_db_host_pfd_fn = 'mac_pfd_db_host';
		    $mac_pfd_db_name_pfd_fn = 'mac_pfd_db_name';
		    $mac_pfd_db_username_pfd_fn = 'mac_pfd_db_username';
		    $mac_pfd_db_password_pfd_fn = 'mac_pfd_db_password';
		    $mac_pfd_db_table_pfd_fn = 'mac_pfd_db_table';
		    $mac_pfd_db_title_field_fn = 'mac_pfd_db_title_field';
		    $mac_pfd_db_body_field_fn = 'mac_pfd_db_body_field';
		    $mac_pfd_db_image_field_fn = 'mac_pfd_db_image_field';
		    $mac_pfd_db_url_field_fn = 'mac_pfd_db_url_field';
		    $mac_pfd_db_date_field_fn = 'mac_pfd_db_date_field';
		    $mac_pfd_db_category_field_fn = 'mac_pfd_db_category_field';
		    $mac_pfd_db_size_field_fn = 'mac_pfd_db_size_field';
		    $mac_pfd_db_type_field_fn = 'mac_pfd_db_type_field';
		    $mac_pfd_primary_field_fn = 'mac_pfd_primary_field';



		    $mac_pfd_db_host = get_option($mac_pfd_db_host_pfd_fn);
		    $mac_pfd_db_name = get_option($mac_pfd_db_name_pfd_fn);
		    $mac_pfd_db_username = get_option($mac_pfd_db_username_pfd_fn);
		    $mac_pfd_db_password = get_option($mac_pfd_db_password_pfd_fn);
		    $mac_pfd_db_table = get_option($mac_pfd_db_table_pfd_fn);
		    $mac_pfd_db_title_field = get_option($mac_pfd_db_title_field_fn);
		    $mac_pfd_db_body_field = get_option($mac_pfd_db_body_field_fn);
		    $mac_pfd_db_image_field = get_option($mac_pfd_db_image_field_fn);
		    $mac_pfd_db_url_field = get_option($mac_pfd_db_url_field_fn);
		    $mac_pfd_db_date_field = get_option($mac_pfd_db_date_field_fn);
		    $mac_pfd_db_category_field = get_option($mac_pfd_db_category_field_fn);
		    $mac_pfd_db_size_field = get_option($mac_pfd_db_size_field_fn);
		    $mac_pfd_db_type_field = get_option($mac_pfd_db_type_field_fn);
		    $mac_pfd_primary_field = get_option($mac_pfd_primary_field_fn);

		    // If the Process button was pressed, call process.php to handle that
		    if(isset($_POST['process'])){
		        include 'process.php';
		    }

		    if(isset($_POST['Submit'])){
		        $mac_pfd_db_host = $_POST[$mac_pfd_db_host_pfd_fn];

		        $mac_pfd_db_name = $_POST[$mac_pfd_db_name_pfd_fn];
		        $mac_pfd_db_username = $_POST[$mac_pfd_db_username_pfd_fn];
		        $mac_pfd_db_password = $_POST[$mac_pfd_db_password_pfd_fn];
		        $mac_pfd_db_table = $_POST[$mac_pfd_db_table_pfd_fn];
		        $mac_pfd_db_title_field = $_POST[$mac_pfd_db_title_field_fn];
		        $mac_pfd_db_body_field = $_POST[$mac_pfd_db_body_field_fn];
		        $mac_pfd_db_image_field = $_POST[$mac_pfd_db_image_field_fn];
		        $mac_pfd_db_url_field = $_POST[$mac_pfd_db_url_field_fn];
		        $mac_pfd_db_date_field = $_POST[$mac_pfd_db_date_field_fn];
		        $mac_pfd_db_category_field = $_POST[$mac_pfd_db_category_field_fn];
		        $mac_pfd_db_size_field = $_POST[$mac_pfd_db_size_field_fn];
		        $mac_pfd_db_type_field = $_POST[$mac_pfd_db_type_field_fn];
		        $mac_pfd_primary_field = $_POST[$mac_pfd_primary_field_fn];

		        update_option($mac_pfd_db_host_pfd_fn, $mac_pfd_db_host);
		        update_option($mac_pfd_db_name_pfd_fn, $mac_pfd_db_name);
		        update_option($mac_pfd_db_username_pfd_fn, $mac_pfd_db_username);
		        update_option($mac_pfd_db_password_pfd_fn, $mac_pfd_db_password);
		        update_option($mac_pfd_db_table_pfd_fn, $mac_pfd_db_table);
		        update_option($mac_pfd_db_title_field_fn, $mac_pfd_db_title_field);
		        update_option($mac_pfd_db_body_field_fn, $mac_pfd_db_body_field);
		        update_option($mac_pfd_db_image_field_fn, $mac_pfd_db_image_field);
		        update_option($mac_pfd_db_url_field_fn, $mac_pfd_db_url_field);
		        update_option($mac_pfd_db_date_field_fn, $mac_pfd_db_date_field);
		        update_option($mac_pfd_db_category_field_fn, $mac_pfd_db_category_field);
		        update_option($mac_pfd_db_size_field_fn, $mac_pfd_db_size_field);
		        update_option($mac_pfd_db_type_field_fn, $mac_pfd_db_type_field);
		        update_option($mac_pfd_primary_field_fn, $mac_pfd_primary_field);
		        Connect::get_instance(TRUE);
		        // Display a message that settings were saved/updated
		        echo '<div class="updated"><p><strong>';
		        echo __('Settings Saved', 'powerpress-posts-from-mysql');
		        echo '</strong></p></div>';		        
		    }
		    screen_icon();
?>
<br /><br />
<div class="wrap">
    <h1 class="ppfm-settings"><?php echo __('PowerPress Posts From MySQL Settings', 'powerpress-posts-from-mysql'); ?> <span class="mini-text">v.0.9.7.5</span></h1>
    
    <h2><?php echo __('Database Connection Info', 'powerpress-posts-from-mysql'); ?></h2>
    
    <fieldset>
    <legend><?php echo __('This is the database where your podcast info is stored.', 'powerpress-posts-from-mysql'); ?></legend>
	<form name="settings" class="ppfm-settings" method="post" action="">
	    <div id="details"></div>
	    <p><label for="<?php echo $mac_pfd_db_host_pfd_fn; ?>"><?php echo __('Database Host (leave blank for localhost)', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_host_pfd_fn; ?>" class="<?php echo Connect::get_db_field_class(); ?>" value="<?php echo $mac_pfd_db_host; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_name_pfd_fn; ?>"><?php echo __('Database Name', 'powerpress-posts-from-mysql'); ?></label>
	        <input type="text" name="<?php echo $mac_pfd_db_name_pfd_fn; ?>" class="<?php echo Connect::get_db_field_class(); ?>" value="<?php echo $mac_pfd_db_name; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_username_pfd_fn; ?>"><?php echo __('Database Username', 'powerpress-posts-from-mysql'); ?></label>
	    	<input type="text" name="<?php echo $mac_pfd_db_username_pfd_fn; ?>" class="<?php echo Connect::get_db_field_class(); ?>" value="<?php echo $mac_pfd_db_username; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_password_pfd_fn; ?>"><?php echo __('Database Password', 'powerpress-posts-from-mysql'); ?></label>
	    	<input type="text" name="<?php echo $mac_pfd_db_password_pfd_fn; ?>" class="<?php echo Connect::get_db_field_class(); ?>" value="<?php echo $mac_pfd_db_password; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_table_pfd_fn; ?>"><?php echo __('Database Table Name', 'powerpress-posts-from-mysql'); ?></label>
	    	<input type="text" name="<?php echo $mac_pfd_db_table_pfd_fn; ?>" class="<?php echo Connect::get_table_field_class($mac_pfd_db_table); ?>" value="<?php echo $mac_pfd_db_table; ?>" size="25"/></p>
	    <legend><?php echo __('Database Table Field Names', 'powerpress-posts-from-mysql'); ?></legend><br />
	    <p><label for="<?php echo $mac_pfd_primary_field_fn; ?>"><?php echo __('Primary Key Field Name', 'powerpress-posts-from-mysql'); ?></label>
	        <input type="text" name="<?php echo $mac_pfd_primary_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_primary_field); ?>" value="<?php echo $mac_pfd_primary_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_title_field_fn; ?>"><?php echo __('Post Title Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_title_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_title_field); ?>" value="<?php echo $mac_pfd_db_title_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_category_field_fn; ?>"><?php echo __('Post Category Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_category_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_category_field); ?>" value="<?php echo $mac_pfd_db_category_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_body_field_fn; ?>"><?php echo __('Post Body Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_body_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_body_field); ?>" value="<?php echo $mac_pfd_db_body_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_image_field_fn; ?>"><?php echo __('Post Image Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_image_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_image_field); ?>" value="<?php echo $mac_pfd_db_image_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_url_field_fn; ?>"><?php echo __('Media URL Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_url_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_url_field); ?>" value="<?php echo $mac_pfd_db_url_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_size_field_fn; ?>"><?php echo __('Media Size Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_size_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_size_field); ?>" value="<?php echo $mac_pfd_db_size_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_type_field_fn; ?>"><?php echo __('Media Type Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_type_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_type_field); ?>" value="<?php echo $mac_pfd_db_type_field; ?>" size="25"/></p>
	    <p><label for="<?php echo $mac_pfd_db_date_field_fn; ?>"><?php echo __('Date Posted Field Name', 'powerpress-posts-from-mysql'); ?></label>
	    <input type="text" name="<?php echo $mac_pfd_db_date_field_fn; ?>" class="<?php echo Connect::get_field_class($mac_pfd_db_date_field); ?>" value="<?php echo $mac_pfd_db_date_field; ?>" size="25"/></p>
	       </fieldset> 
	    <p class="submit">
	        <input type="hidden" name="sort_field" value="<?php echo $mac_pfd_primary_field; ?>" />
	        <input type="submit" id="Submit" name="Submit" class="button-primary" value="<?php echo __('Save Changes', 'powerpress-posts-from-mysql'); ?>" />
	    </p>
    <?php
    // The below code checks database connectivity and allows the selection of a range of records
    // to be posted.  If either Start Record or End Record fields are left blank they will default to
    // either the first or last record, respectively (based on Primary Key field).
    // 
    // Connect to database 
    $DB_HOST = get_option($mac_pfd_db_host_pfd_fn);
    //if(!empty($DB_HOST)){
    if(Connect::connection()){
        $DB_TABLE = get_option($mac_pfd_db_table_pfd_fn);
        $DBH = Connect::get_instance(TRUE);
        if($DBH){
                $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

                @$STH = $DBH->query("SELECT count(*) FROM ".$DB_TABLE);
                if($STH){
                    $count = $STH->fetchColumn();
                    
                    if($count > 0 && Connect::field_errors() == 0):
                        ?>
                        <script>
                            jQuery("#Submit").toggleClass("button-primary button-secondary");
                            jQuery("#Submit").attr('value', 'Update Settings');
                        </script>
                        <fieldset class="ppfm-settings">
                            <legend><?php printf(_n('There is %d record available', 'There are %d records available', $count, 'powerpress-posts-from-mysql'), $count); ?></legend>
                        <input type="hidden" name="primary_field" value="<?php echo $mac_pfd_primary_field ?>"><br />
                        <p>

                            <?php printf(__('Starting %s', 'powerpress-posts-from-mysql'),  $mac_pfd_primary_field);  ?>
                        <input type="text" name="start" size="14" /></p>
                        <p>

                            <?php printf(__('Ending %s', 'powerpress-posts-from-mysql'), $mac_pfd_primary_field); ?>
                        <input type="text" name="end" size="14" /></p>
                        <hr class="ppfm-settings">
                        <p><?php echo __('publish', 'powerpress-posts-from-mysql'); ?><input type="radio" name="status" value="publish" checked="checked" /></p>
                        <p><?php echo __('draft', 'powerpress-posts-from-mysql'); ?><input type="radio" name="status" value="draft" /></p>
                        </fieldset>
                        <?php if(!empty($mac_pfd_primary_field)): ?>
                        <p><em><?php echo __('Start and End Records correspond to values in the Primary Key Field', 'powerpress-posts-from-mysql'); ?> <strong>'<?php echo $mac_pfd_primary_field ?>'</strong>.</em><br />
                        <em>
                        <?php printf(__('if left blank, they will default to first and last %s, respectively.', 'powerpress-posts-from-mysql'), $mac_pfd_primary_field); ?>
                        </em></p>
                        <?php else: ?>
                        <p><div class="updated"><h2><?php echo __('Please Enter Primary Key Field Name', 'powerpress-posts-from-mysql'); ?></h2></div></p>
                        <?php endif; ?>
                        <input type="hidden" name="max" value="<?php echo $count; ?>">
                        <p class="submit">
                            <input type="submit" name="process" class="button-primary" value="<?php echo __('Process', 'powerpress-posts-from-mysql'); ?>" />
                        </p>
                        <?php
                    elseif(Connect::field_errors() !== 0):
                        echo '<div class="updated"><h2>';
                        echo __('Please Verify Field Names', 'powerpress-posts-from-mysql');
                        echo '</h2></div>';
                    elseif($count == 0):
                        echo '<div class="updated"><h2>';
                        echo __('No Records Were Found', 'powerpress-posts-from-mysql');
                        echo '</h2></div>';
                    endif;
                }else{
            echo '<div class="updated"><h2>';
            echo __('Please Check Database Table Name', 'powerpress-posts-from-mysql');
            echo '</h2></div>';
        }
        }else{
            echo "<div class='updated'><h2>";
            echo __('No Database Connection', 'powerpress-posts-from-mysql');
            echo "</h2>";
            echo "<h3>";
            echo __('Please check your settings', 'powerpress-posts-from-mysql');
            echo "</h3></div>";
        }
        
    }else{
        echo '<script>
            jQuery("form.ppfm-settings p input").attr("class", "negative");
</script>';
        Connect::get_instance(TRUE);
        echo "<div class='updated'><h2>";
        echo __('Please enter or verify your database details', 'powerpress-posts-from-mysql');
        echo "</h2></div>";
    }
    
    
?>
    
</form>
</div>
<?php
	}else{
        echo "<div class='updated'><h2>";
        echo __('Please install and activate', 'powerpress-posts-from-mysql');
        echo '&nbsp;<a href="http://wordpress.org/extend/plugins/powerpress/" target="_blank">BluBrry PowerPress</a>';
    }
}  // End mac_pfd_settings_page

function does_field_exist($field){
    $DB_HOST = get_option('mac_pfd_db_host');
    $DB_NAME = get_option('mac_pfd_db_name');
    $DB_USER = get_option('mac_pfd_db_username');
    $DB_PASS = get_option('mac_pfd_db_password');
    $DB_TABLE = get_option('mac_pfd_db_table');
        try{
            $DBH = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
            $sql = "SHOW COLUMNS FROM `$DB_TABLE` LIKE '$field'";
            if (count($DBH->query($sql)->fetchAll())) {
                return TRUE;
            }else{
                return FALSE;
            }
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }    
}
