<?php
if(!empty($_POST['range_field'])){
    $range_field = $_POST['range_field'];
}else{
    $range_field = 'id';
}
if(!empty($_POST['start'])){
    $start = $_POST['start'] - 1;
}else{
    $start = 0;
}

if(!empty($_POST['end'])){
    $end = $_POST['end'] + 1;
}else{
    $end = $_POST['max'] + 1;
}

if($start > $_POST['max']){
    $start = $_POST['max'];
}

if($end > $_POST['max']){
    $end = $_POST['max'] + 1;
}

$range = " WHERE {$range_field} > {$start} && {$range_field} < {$end}";



    $DB_HOST = get_option($mac_pfd_db_host_pfd_fn);
    $DB_NAME = get_option($mac_pfd_db_name_pfd_fn);
    $DB_USER = get_option($mac_pfd_db_username_pfd_fn);
    $DB_PASS = get_option($mac_pfd_db_password_pfd_fn);
    $DB_TABLE = get_option($mac_pfd_db_table_pfd_fn);
    $TITLE_FIELD = get_option($mac_pfd_db_title_field_fn);
    $BODY_FIELD = get_option($mac_pfd_db_body_field_fn);
    $IMAGE_FIELD = get_option($mac_pfd_db_image_field_fn);
    $URL_FIELD = get_option($mac_pfd_db_url_field_fn);
    $DATE_FIELD = get_option($mac_pfd_db_date_field_fn);
    $CATEGORY_FIELD = get_option($mac_pfd_db_category_field_fn);
    $SIZE_FIELD = get_option($mac_pfd_db_size_field_fn);
    $TYPE_FIELD = get_option($mac_pfd_db_type_field_fn);

// Connect to database - I wrote this to work with MySQL    
try{
$DBH = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
}
 catch (PDOException $e){
}

$DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

// Selects all rows from the specified table holding the podcast information
$STH = $DBH->query("SELECT * FROM ".$DB_TABLE.$range);
$STH->setFetchMode(PDO::FETCH_ASSOC);


$processed = 0; // 
$skipped = 0; // 

while($row = $STH->fetch()){

    $title = html_entity_decode($row[$TITLE_FIELD]);
    $body = html_entity_decode($row[$BODY_FIELD]);
    $post_date = $row[$DATE_FIELD]." 17:51:00";
    

    // Check for duplicate
    if(!get_page_by_title($title, 'OBJECT', 'post')){
    //

        $category = $row[$CATEGORY_FIELD];
        $cat_id = get_cat_ID($category);

        // split name into keywords - this is temporary - may not be useful or need to be changed
        preg_match('~\d{2,3}:\s(.*)~', $title, $matches);
        $keywords = preg_replace('~\s~', ', ', $matches[1]);

        // Set up the value for the custom field 'enclosure' for PowerPress
        $enclosure_value = $row[$URL_FIELD];
        $enclosure_value .= "\n";
        $enclosure_value .= $row[$SIZE_FIELD];
        $enclosure_value .= "\n";
        $enclosure_value .= $row[$TYPE_FIELD];

        // Set up the Post
        $post = array(
            'post_category' => array($cat_id),
            'post_content' => $body,
            'post_date' => $post_date,
            'post_title' => $title,
            'post_type' => 'post',
            'post_status' => 'publish',
        );

        // If the Post is successfully inserted the new Post_id will be returned
        // then use post id to handle image
        if($post_id = wp_insert_post($post)){

            $image_url = $row[$IMAGE_FIELD];
            if($image_url !== NULL){
                $upload_dir = wp_upload_dir();
                $image_data = file_get_contents($image_url);
                $filename = basename($image_url);
                if(wp_mkdir_p($upload_dir['path']))
                    $file = $upload_dir['path'] . '/' . $filename;
                else
                    $file = $upload_dir['basedir'] . '/' . $filename;
                file_put_contents($file, $image_data);

                $wp_filetype = wp_check_filetype($filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                set_post_thumbnail( $post_id, $attach_id );
                update_post_meta($post_id, 'enclosure', $enclosure_value);

            }
            $processed++;
    }
  }else{
      $skipped++;
  }
}
echo '<div class="updated"><p><strong>'.$processed.' podcasts posted.  '.$skipped.' skipped as duplicate.</strong></p></div>';