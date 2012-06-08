<?php 
/*
Plugin Name: PowerPress Post From MySQL
Plugin URI: http://plugins.markchouinard.me/powerpress-plugin/
Description: Create PowerPress Posts From MySQL Database Table
Author: Mark Chouinard
Author URI: http://markchouinard.me/
Version: 0.9
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

add_action('admin_menu', 'mac_pfd_create_menu');

function mac_pfd_create_menu(){
	
	add_management_page('PFD Settings Page', 'Post From MySQL', 'manage_options', __FILE__, 'mac_pfd_settings_page');
        
}

function mac_pfd_settings_page(){
        //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die('You do not have sufficient permissions to access this page.');
    }

    

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
    
    $hidden_pfd_fn = 'pfd_submit_hidden';
    
    // If the Process button was pressed, call process.php to handle that
    if(isset($_POST['process'])){
        include 'process.php';
    }
    
    
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

    
    // If Save Settings was pressed, write data from form to options table
    if(isset($_POST[$hidden_pfd_fn]) && $_POST[$hidden_pfd_fn] == "Y"){
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

        
        // Display a message about settings being updated
        echo '<div class="updated"><p><strong>Settings Saved</strong></p></div>';
    }
    screen_icon(); 
?>

<div class="wrap">
    <h2>PowerPress Post From MySQL Settings</h2>
    <h3>Database table and field names.</h3>
<form name="form1" method="post" action="">
    <input type="hidden" name="<?php echo $hidden_pfd_fn; ?>" value="Y">
    

    <p>Database Host
    <input type="text" name=<?php echo $mac_pfd_db_host_pfd_fn; ?> value="<?php echo $mac_pfd_db_host; ?>" size="25"</p>
    <p>Database Name<br />(not WP database)
    <input type="text" name=<?php echo $mac_pfd_db_name_pfd_fn; ?> value="<?php echo $mac_pfd_db_name; ?>" size="25"</p>
    <p>Database Username
    <input type="text" name=<?php echo $mac_pfd_db_username_pfd_fn; ?> value="<?php echo $mac_pfd_db_username; ?>" size="25"</p>
    <p>Database Password
    <input type="text" name=<?php echo $mac_pfd_db_password_pfd_fn; ?> value="<?php echo $mac_pfd_db_password; ?>" size="25"</p>
    
    <p>Database Table Name
    <input type="text" name=<?php echo $mac_pfd_db_table_pfd_fn; ?> value="<?php echo $mac_pfd_db_table; ?>" size="25"</p>
    <p>Post Title Field Name
    <input type="text" name=<?php echo $mac_pfd_db_title_field_fn; ?> value="<?php echo $mac_pfd_db_title_field; ?>" size="25"</p>
        <p>Post Category Field Name
    <input type="text" name=<?php echo $mac_pfd_db_category_field_fn; ?> value="<?php echo $mac_pfd_db_category_field; ?>" size="25"</p>
    <p>Post Body Field Name
    <input type="text" name=<?php echo $mac_pfd_db_body_field_fn; ?> value="<?php echo $mac_pfd_db_body_field; ?>" size="25"</p>
    <p>Post Image Field Name
    <input type="text" name=<?php echo $mac_pfd_db_image_field_fn; ?> value="<?php echo $mac_pfd_db_image_field; ?>" size="25"</p>
    <p>Media URL Field Name
    <input type="text" name=<?php echo $mac_pfd_db_url_field_fn; ?> value="<?php echo $mac_pfd_db_url_field; ?>" size="25"</p>


    <p>Media Size Field Name
    <input type="text" name=<?php echo $mac_pfd_db_size_field_fn; ?> value="<?php echo $mac_pfd_db_size_field; ?>" size="25"</p>
    <p>Media Type Field Name
    <input type="text" name=<?php echo $mac_pfd_db_type_field_fn; ?> value="<?php echo $mac_pfd_db_type_field; ?>" size="25"</p>
        <p>Date Posted Field Name
    <input type="text" name=<?php echo $mac_pfd_db_date_field_fn; ?> value="<?php echo $mac_pfd_db_date_field; ?>" size="25"</p>

    
    <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="Save Changes" />
    </p>
    <p class="submit">
        <input type="submit" name="process" class="button-secondary" value="Process" />
    </p>

    
</form>
</div>

<?php

    

}
