<?php

class LocalPodcast {
	private static $instance;
	// private static $podcasts;
	static $db_options;
	static $field_options;
	static $table;
	static $dbh;
	

	private function __construct( ){
		self::$db_options = get_option( 'ppfm_db_options');
		self::$field_options = get_option('ppfm_field_options');
		self::$table = self::$db_options[ 'db_table' ];
		$pass = self::$db_options[ 'db_password' ];
		$user = self::$db_options[ 'db_user' ];
		$host = empty( self::$db_options[ 'db_host' ] ) ? 'localhost' : self::$db_options[ 'db_host' ];
		$name = self::$db_options[ 'db_name' ];
		if ( !empty( $pass) && !empty( $user ) && !empty( $name )){
			self::$dbh = new wpdb( $user, $pass, $name, $host );
		}
		
	}

	static function get_instance( ){
		if( empty( self::$instance )){
			self::$instance = new LocalPodcast();
		}
		return self::$instance;
	}

	static function is_configured(){
		if ( count( self::$db_options ) < 7 ){
			return FALSE;
		}
		return TRUE;
	}

	static function get_podcasts( $search=null ) {
		// print_r(self::$field_options);die(' 41 local_podcast');
		$title_col = self::$field_options['post_title'];
		$url_col = self::$field_options['post_url'];
		$table = self::$table;
		if ( $search == null){
			$podcasts = self::$dbh->get_results( "SELECT * FROM $table", ARRAY_A );
		} else {
			$podcasts = self::$dbh->get_results( "SELECT * FROM $table WHERE `$title_col` LIKE '%{$search}%'", ARRAY_A);
		}
		
		if ( $podcasts ){
			return $podcasts;
		}
		return array();
	}

	static function count_podcasts( ) {
		$table = self::$table;
		$count = self::$dbh->get_var( "SELECT count(*) FROM $table" );
		if ( $count ){
			return $count;
		}
		return 0;
	}


	static function find_podcast_by_id( $id ) {
		$table = self::$table;
		$sql = "SELECT * FROM $table WHERE id = $id";
		// echo $sql;die('local.podcast 64');
		$podcast = self::$dbh->get_row( $sql , ARRAY_A );
		if ( $podcast ) {
			return $podcast;
		}
		return FALSE;
	}

	static function guid_exists_by_db_id( $db_id ){
		return self::guid_exists( self::create_guid( $db_id ) );
	}

	static function guid_exists( $guid ) {
		global $wpdb;
		$table = $wpdb->prefix . "posts";
		$id = $wpdb->get_var( "SELECT id FROM $table WHERE guid = '$guid' " );
		if ( ! $id ){
			return FALSE;
		}
		return $id;
	}

	static function create_guid( $db_id ){
    		// echo '<pre>';
    		// print_r( self::$db_options );
    		// echo '</pre>';
    		// die( 'local.podcast 90');
		if ( empty( self::$db_options['db_guid'])){
			$guid_string = site_url( ) . '.' . $db_id;
		} else {
			$guid_string = 'http://' . self::$db_options['db_guid'] . '.' . $db_id;
		}
		return $guid_string;
	}

	static function find_wp_id_by_guid( $guid ){
		global $wpdb;
		$table = $wpdb->prefix . 'posts';
		// echo 'table: ' . $table;die( 'local_podcast 102' );
		// echo '<br />guid: ' . $guid;//die(' local_podcast 103' );
		$ID = $wpdb->get_var( "SELECT ID FROM $table WHERE guid = '$guid'" );
		// echo '<br />$ID: ' . $ID;die( 'local_podcast 104' );
		return $ID;
	}

	static function does_field_exist($field){
		self::$dbh->hide_errors();
		$table = self::$table;
		$col = self::$dbh->get_col( "SELECT $field FROM ppfm" );
		self::$dbh->show_errors();
		if ( !empty( $col) ) {
			return TRUE;
		}
		return FALSE;
		
        }

    static function does_table_exist( $table ){
        if ( self::$dbh ){
		$sql = ("SHOW TABLES LIKE '$table'");
		$result = self::$dbh->get_var( $sql );
		if ( $result == $table ){
			return TRUE;
		 }
		return FALSE;
	}
	return TRUE;
    }



    	

}

