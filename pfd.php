<?php 
/*
Plugin Name: PowerPress Post From MySQL
Plugin URI: http://plugins.markchouinard.me/powerpress-plugin/
Description: Create PowerPress Posts From MySQL Database Table
Author: Mark Chouinard
Author URI: http://plugins.markchouinard.me/powerpress-plugin/
Version: 0.9.1
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
    <h1>PowerPress Post From MySQL Settings</h1>
    <h2>Database table and field names.</h2>
    <h3>This is the database where your podcast info is stored<br />It could be different than your WordPress database.</h3>
<form name="form1" method="post" action="">
    <input type="hidden" name="<?php echo $hidden_pfd_fn; ?>" value="Y">
    

    <p>Database Host
    <input type="text" name=<?php echo $mac_pfd_db_host_pfd_fn; ?> value="<?php echo $mac_pfd_db_host; ?>" size="25"</p>
    <p>Database Name
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
    
    
    <?php
    // The below code checks database connectivity and allows the selection of a range of records
    // to be posted.  If either Start Record or End Record fields are left blank they will default to
    // either the first or last record, respectively.
    // 
    // Connect to database - I wrote this to work with MySQL 
    $DB_HOST = get_option($mac_pfd_db_host_pfd_fn);
    if(!empty($DB_HOST)){
        $DB_NAME = get_option($mac_pfd_db_name_pfd_fn);
        $DB_USER = get_option($mac_pfd_db_username_pfd_fn);
        $DB_PASS = get_option($mac_pfd_db_password_pfd_fn);
        $DB_TABLE = get_option($mac_pfd_db_table_pfd_fn);

        try{
            $DBH = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
            }
            catch (PDOException $e){
            }
if($DBH){
        $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        $STH = $DBH->query("SELECT count(*) FROM ".$DB_TABLE);
        $count = $STH->fetchColumn();
        echo "<h2>There are {$count} records available.</h2>\r\n<br />";
        if($count > 0):
            ?>
            <label for="range_field">Name of field for range</label>
            <input type="text" name="range_field" size="23" value="id"><br />
            <label for="start">Start Record</label>
            <input type="text" name="start" size="8">
            <label for="end">End Record</label>
            <input type="text" name="end" size="8">
            <input type="hidden" name="max" value="<?php echo $count; ?>">
            <p class="submit">
                <input type="submit" name="process" class="button-secondary" value="Process" />
            </p>
            <?php
        endif;
}else{
    echo "<h2>No Database Connection</h2>";
    echo "<h3>Please check your settings</h3>";
}
    }else{
        echo "<h2>Please enter your database details</h2>";
    }
    ?>
    
    


    
</form>
</div>

<?php

    

}
