<?php

class Podcast {
	private $db_id;
	private $post_title;
	private $post_category;
	private $post_body;
	private $post_image_locatation;
	private $post_url;
	private $post_size;
	private $post_type;
	private $post_date;
	private $error;
	private $guid_string;
	
	function __construct( $id ){
		// echo 'id: '.$id;die( 'class.podcast 17');
		// require_once plugin_dir_path( __FILE__ ) . 'ez_sql_core.php';
		// require_once plugin_dir_path( __FILE__ ) . 'ez_sql_mysql.php';
		require_once plugin_dir_path( __FILE__ ) . 'class.local_podcast.php';
		$podcast = LocalPodcast::find_podcast_by_id( $id );
		// echo '<pre>';
		// print_r($podcast);
		// echo '</pre>';
		// die('class.podcast 25');
		$this->post_image_location = $podcast['image_location'];
		$this->db_id = $id;
		$this->post_title = $podcast[LocalPodcast::$field_options['post_title']];
		$this->post_category = $podcast[LocalPodcast::$field_options['post_category']];
		$this->post_body = $podcast[LocalPodcast::$field_options['post_body']];
		// $this->post_image = $podcast[LocalPodcast::$field_options['post_image']];
		$this->post_url = $podcast[LocalPodcast::$field_options['post_url']];
		$this->post_size = $podcast[LocalPodcast::$field_options['post_size']];
		$this->post_type = $podcast[LocalPodcast::$field_options['post_type']];
		$this->post_date = $podcast[LocalPodcast::$field_options['post_date']];
		$this->guid_string = LocalPodcast::create_guid( $id );
		
	}

	function details(){
		return array(
			'db_id' => $this->db_id,
			'post_title' => $this->post_title,
			'post_category' => $this->post_category,
			'post_body' => $this->post_body,
			'post_image_location' => $this->post_image_location,
			'post_url' => $this->post_url,
			'post_size' => $this->post_size,
			'post_type' => $this->post_type,
			'post_date' => $this->post_date,
			'guid' => $this->guid_string
		);
		
	}


	public function publish(  ){
		return $this->_publish_post( );
	}

	public function publish_draft(){
		// can we find a record with this guid?
		if ( $id = LocalPodcast::guid_exists( $this->guid_string ) ){
			// what's its status?
			$status = get_post_status( $id );
			if ( $status == 'publish' ){
				$post = array(
					'ID' => $id,
					'post_status' => 'draft'
				);
			} else {
				$post = array(
					'ID' => $id,
					'post_status' => 'publish'
				);
			}
			
			if ( !wp_update_post( $post )){
				$return = new WP_Error( 'wp_update_post_failure', __('wp_update_post() failed.', 'ppfm' ) );
			}
		} else {
			return $this->_publish_post( 'draft' );
		}
	}

	public function remove(){
		// echo $this->guid_string;die('class.podcast 87' );
		// here we need to remove the post, its related image(s) and anything else that was created
		// echo 'guid: ' . $this->guid_string;die( 'class.podcast 89' );
		$ID = LocalPodcast::find_wp_id_by_guid( $this->guid_string );
		
		$args = array(
		'numberposts' => -1,
		'order' => 'ASC',
		'post_mime_type' => 'image',
		'post_parent' => $ID,
		'post_status' => null,
		'post_type' => 'attachment',
	);
		$children = get_children( $args );
		foreach( $children as $child ) {
			$child_ID = $child->ID;
			wp_delete_post( $child_ID, TRUE );
		}
		if ( ! wp_delete_post( $ID, TRUE ) ) {
			$return = new WP_Error( 'wp_delete_post_failure', __( 'wp_delete_post() failed.', 'ppfm' ) );
		}
	}

	private function _publish_post( $status="publish" ){

		// Setup PowerPress enclosure
		$enclosure_value = $this->post_url;
		$enclosure_value .= "\n";
		$enclosure_value .= $this->post_size;
		$enclosure_value .= "\n";
		$enclosure_value .= $this->post_type;

		$cat_id = get_cat_ID($this->post_category);

		// Setup the post
		$post = array(
			'post_category' => array( $cat_id ),
			'post_content' => $this->post_body,
			'post_date' => $this->post_date,
			'post_title' => $this->post_title,
			'post_type' => 'post',
			'post_status' => $status,
			'guid' => $this->guid_string
		);
// echo '<pre>';
// print_r( $post );		
// echo '</pre>';
// die('class.podcast 131');
		// If post is successfully inserted, the new post_id will be returned
		// and we can use that to handle the image and the podcast
		if ( $post_id = wp_insert_post($post)){
			
			$type = exif_imagetype($this->post_image_location);
			$type = image_type_to_mime_type( $type );

			$temp_image = file_get_contents( $this->post_image_location );
			// here we trick media_handle_sideload into thinking it is working with an upload file in the
			// form of $_FILE
			$img_array = array(
				'name' => basename( $this->post_image_location ),
				'type' => $type,
				'tmp_name' => $this->post_image_location,
				'error' => 0,
				'size' => filesize( $this->post_image_location )
			);
// echo '<pre>';
// print_r( $img_array);
// echo '</pre>';
// die('class.podcast 155');
			// media_handle_sideload returns the id that we can use to set the featured image
			$thumb_id = media_handle_sideload( $img_array, $post_id, $this->post_title );
			if ( is_wp_error( $thumb_id ) ){
				$return = new WP_Error( 'media_handle_sideload_failure', __('media_handle_sideload() failed.', 'ppfm' ) );
			}
			set_post_thumbnail( $post_id, $thumb_id );
			file_put_contents( $this->post_image_location, $temp_image );

			if( ! update_post_meta( $post_id, 'enclosure', $enclosure_value )){
				$return = new WP_Error( 'update_post_meta_failure', __( 'update_post_meta() failed.', 'ppfm' ) );
			}
			
		} else {
			$error = new WP_Error('insert_post_failure', __( 'wp_insert_post() failed.', 'ppfm' ) );
			return $error;
		}
	}


}