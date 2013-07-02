<?php


if(isset($_POST['status'])){
    $status = $_POST['status'];
}else{
    $status = 'publish';
}

if(!empty($_POST['sort_field'])){
    $sort_field = $_POST['sort_field'];
}else{
    $sort_field = 'id';
}
if(!empty($_POST['start'])){
    $start = $_POST['start'];
}else{
    $start = 1;
}

if(!empty($_POST['end'])){
    $end = $_POST['end'];
}else{
    $end = $_POST['max'];
}

if($start > $_POST['max']){
    $start = $_POST['max'];
}

if($end > $_POST['max']){
    $end = $_POST['max'];
}

$range = " WHERE {$sort_field} >= {$start} && {$sort_field} <= {$end}";

// Connect to database    
$DBH = connect::get_instance();
// Selects rows from the specified table based on Start and End Record inputs
$STH = $DBH->query("SELECT * FROM ".$mac_pfd_db_table.$range);
$STH->setFetchMode(PDO::FETCH_ASSOC);

$processed = 0; // 
$skipped = 0; // 

while($row = $STH->fetch()){
    $raw_title = $row[$mac_pfd_db_title_field];
    $title = esc_html($raw_title);
    $body = esc_html($row[$mac_pfd_db_body_field]);
    $post_date = $row[$mac_pfd_db_date_field]." 17:51:00";
    

    // Check for duplicate
    if(!get_page_by_title($raw_title, 'OBJECT', 'post')){
    //

        $category = $row[$mac_pfd_db_category_field];
        $cat_id = get_cat_ID($category);

        // Set up the value for the custom field 'enclosure' for PowerPress
        $enclosure_value = $row[$mac_pfd_db_url_field];
        $enclosure_value .= "\n";
        $enclosure_value .= $row[$mac_pfd_db_size_field];
        $enclosure_value .= "\n";
        $enclosure_value .= $row[$mac_pfd_db_type_field];

        // Set up the Post
        $post = array(
            'post_category' => array($cat_id),
            'post_content' => $body,
            'post_date' => $post_date,
            'post_title' => $title,
            'post_type' => 'post',
            'post_status' => $status,
        );

        // If the Post is successfully inserted the new Post_id will be returned
        // then use post id to handle image
        if($post_id = wp_insert_post($post)){

            $image_url = $row[$mac_pfd_db_image_field];
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
echo '<div class="updated">';

if($start > $end){
    echo '<h3>';
    echo __('Please verify your Start and End Record range', 'powerpress-posts-from-mysql');
    echo ':&nbsp;'.$sort_field."'s&nbsp;".($start).'&nbsp;-&nbsp;'.($end);
    echo '</h3>';
}else{
    echo '<strong><p>';
    printf(_n('%d podcast posted.', '%d podcasts posted.', $processed, 'powerpress-posts-from-mysql'), $processed);
    echo '</p><p>';
    printf(_n('%d podcast skipped as duplicate.', '%d podcasts skipped as duplicates.', $skipped, 'powerpress-posts-from-mysql'), $skipped);
    echo '</strong></p><p>';
    printf(__('Podcast %s numbers: %d through %d', 'powerpress-posts-from-mysql'), $sort_field, $start, $end);
    echo '</p><p>';
    printf(__('Status: %s', 'powerpress-posts-from-mysql'), $status);
    echo '</p>';
}
echo '</div>';