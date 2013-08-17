<?php
/**
 * Plugin Name: PowerPress Posts From MySQL
 * Plugin URI:  http://plugins.markchouinard.me
 * Description: Create PowerPress posts from data stored in a MySQL table
 * Version:     0.9.8 
 * Author:      Mark Chouinard
 * Author URI:  http://markchouinard.me
 * License:     GPLv2+
 * Text Domain: ppfm
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2013 Mark Chouinard (email : mark@chouinard.me)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */


class ppfmPlugin{

	protected $menu_page;
	protected $db_page;
	protected $fields_page;
	protected $podcasts;
	
	public function __construct(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class.local_podcast.php';
		require_once plugin_dir_path(__FILE__) . 'includes/class.ppfm_list_table.php';
		add_action ( 'admin_menu', array($this, 'ppfm_add_page') );
		add_action ('admin_init', array($this, 'ppfm_plugin_field_page_init') );
		add_action('admin_init', array($this, 'ppfm_plugin_admin_init') );
		$start = LocalPodcast::get_instance();
	}


	public function ppfm_add_page(){
		$this->menu_page = add_menu_page( __('PowerPress posts from MySQL', 'ppfm'), __('PowerPress from MySQL', 'ppfm'), 'manage_options', 'ppfm_plugin', array($this, 'ppfm_podcasts_page') );
		$this->db_page = add_submenu_page( 'ppfm_plugin', __('MySQL Connection', 'ppfm'), __('MySQL Connection', 'ppfm'), 'manage_options', 'ppfm_plugin_db_connect', array($this, 'ppfm_plugin_db_connect_page') );
		$this->fields_page = add_submenu_page( 'ppfm_plugin', __('MySQL Fields', 'ppfm'), __('MySQL Fields', 'ppfm'), 'manage_options', 'ppfm_plugin_db_fields', array($this, 'ppfm_plugin_db_fields_page') );
		add_action('load-' . $this->db_page, array($this, 'ppfm_db_setup') );
		add_action('load-'.$this->fields_page, array( $this, 'ppfm_fields_setup') );
		add_action( 'load-' . $this->menu_page, array( $this, 'ppfm_screen_options' ) );
	}


	function ppfm_screen_options() {
		$option = 'per_page';
		$args = array( 
			'label' => 'Podcasts',
			'default' => 5,
			'option' => 'podcasts_per_page'
		);
		add_screen_option( $option, $args );
	}

	function ppfm_plugin_db_connect_page(){
		$h2 = __('PowerPress Posts From MySQL Connection', 'ppfm');
		?>
		<div class="wrap">
			<h2><?php echo $h2; ?></h2>
			<?php settings_errors( ); ?>
			<form action="options.php" method="post">
				<?php settings_fields('ppfm_db_options'); ?>
				<?php do_settings_sections( 'ppfm_plugin'); ?>
				<?php do_settings_sections( 'ppfm_guid' ); ?>
				<?php submit_button( ); ?>
			</form>
		</div>
		<?php
	}

	function ppfm_plugin_db_fields_page(){
		$h2 = __('PowerPress Posts From MySQL Fields', 'ppfm');
		?>
		<div class="wrap">
			<?php
			if ( count( LocalPodcast::$db_options ) <5 ) {
				$h2 = __('Please fill in or verify your database connection details', 'ppfm');
				echo '<h2> ' . $h2 . '</h2>';
				echo '<a href="/wp-admin/admin.php?page=ppfm_plugin_db_connect" >MySQL Connection page</a>';
				return;
			}
			?>
			<h2><?php echo $h2; ?></h2>
			<?php settings_errors( ); ?>
			<form action="options.php" method="post">
				<?php settings_fields( 'ppfm_field_options'); ?>
				<?php do_settings_sections( 'ppfm_plugin_db_fields' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	function ppfm_podcasts_page(){

		if (!current_user_can('manage_options'))
		{
			wp_die(__('You do not have sufficient permissions to access this page.', 'ppfm'));
		}
		echo '<div class="wrap">';

// echo '<pre>';
// print_r( LocalPodcast::$db_options );
// echo '</pre>';
// die( 'ppfm 107' );

// if ( LocalPodcast::$dbh ){
// 	echo 'yes';die('ppfm 118');
// }

		if( !defined ( 'POWERPRESS_VERSION' )){
			$h2 = __( 'This plugin requires the Blubrry PowerPress plugin.  Please install or Activate it now', 'ppfm' );
			echo '<h2>' . $h2 . '</h2>';
			echo '<a href="http://create.blubrry.com/resources/powerpress/" >Blubrry PowerPress</a>';
			return;
		}

	// if ( !LocalPodcast::$dbh ){
	// 	$h2 = __('Please fill in or verify your database connection details', 'ppfm');
	// 	echo '<h2> ' . $h2 . '</h2>';
	// 	echo '<a href="/wp-admin/admin.php?page=ppfm_plugin_db_connect" >MySQL Connection page</a>';
	// 	return;
	// }

		if ( !LocalPodcast::$dbh || empty( LocalPodcast::$db_options[ 'db_table' ] ) || !LocalPodcast::does_table_exist( LocalPodcast::$db_options[ 'db_table' ] ) ) {
			$h2 = __('Please fill in or verify your database connection details', 'ppfm');
			echo '<h2> ' . $h2 . '</h2>';
			echo '<a href="/wp-admin/admin.php?page=ppfm_plugin_db_connect" >MySQL Connection page</a>';
			return;
		}

		if ( count( LocalPodcast::$field_options ) < 10 ){
			$h2 = __( 'Please fill in or verify your database field details', 'ppfm' );
			echo '<h2>' . $h2 . '</h2>';
			echo '<a href="/wp-admin/admin.php?page=ppfm_plugin_db_fields" >MySQL Fields Page</a>';
			return;
		}

		settings_errors( );

		$this->podcasts = LocalPodcast::get_podcasts();
		$table = new PPFM_List_Table($this->podcasts, LocalPodcast::$field_options);
		$table->prepare_items();
		$count = LocalPodcast::count_podcasts();
		$h2 = sprintf(_n('%d Available Podcast', '%d Available Podcasts', $count, 'ppfm'), $count);
		?>
		<!-- <div class="wrap"> -->
		<div id="icon-users" class="icon32"><br /></div>
		<h2><?php echo $h2; ?></h2>
		<?php
		

		?>
		<form id="podcasts-filter" action='options.php' method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
			<!-- Now we can render the completed list table -->
			<?php $table->display() ?>
		</form>
	</div>
	<?php
}

function ppfm_fields_setup(){
	$guid_text = __( 'As long as your podcasts are stored in a MySQL table with a primary key, they will all have a unique ID.  The Primary Key value will be appended to the GUID string.  If the custom string field is left blank, the Primary Key value will be appended to the site URL. The ID field cannot be left blank.  If the GUID string is changed after posting, the plugin will not be able to tell what has and has not been posted.', 'ppfm' );
	$field_names_text = __( 'These refer to the databse field names that correspond to your podcasts', 'ppfm' );
	$screen = get_current_screen();
	if( $screen->id != $this->fields_page){
		return;
	}
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_fields',
		'title' => __('Field Names', 'ppfm'),
		'content' => "<p>$field_names_text</p>"
		));
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_guid',
		'title' => __('GUID / Primary Key', 'ppfm'),
		'content' => "<p>$guid_text</p>"
		));
}

function ppfm_db_setup(){
	$host_text = __( 'This will most likely be "localhost", but this plugin does work with remote database connections.', 'ppfm' );
	$port_text = __( 'On Unix anyway, if the host is set to localhost the connection is made through a socket, so this won\'t matter.  If your host is set to 127.0.0.1 this needs to be set to your correct port (default 3306)', 'ppfm' );
	$db_name_text = __( 'This is the name of the database where your podcast information is stored.  It need not be the same as your WordPress database.', 'ppfm' );
	$db_user_text = __( 'This refers to the login credentials for the database where your podcast information is stored.  They may or may not be the same as your WordPress login.', 'ppfm' );
	$db_table_text = __( 'This is the name of the database table where your podcast information is stored.  It should not be one of your WordPress tables.', 'ppfm' );
	

	$screen = get_current_screen();
	if ( $screen->id != $this->db_page ){
		return;
	}
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_host',
		'title' => __('Database Host', 'ppfm'),
		'content' => "<p>$host_text</p>"
		));
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_Port',
		'title' => __('Database Port', 'ppfm'),
		'content' => "<p>$port_text</p>"
		));
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_name',
		'title' => __('Database Name', 'ppfm'),
		'content' => "<p>$db_name_text</p>"
		));
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_user',
		'title' => __('User & Password', 'ppfm'),
		'content' => "<p>$db_user_text</p>"
		));
	$screen->add_help_tab( array(
		'id' => 'ppfm_db_table',
		'title' => __('Database Table', 'ppfm'),
		'content' => "<p>$db_table_text</p>"
		));
	
}


function ppfm_plugin_field_page_init(){
	register_setting(
		'ppfm_field_options',
		'ppfm_field_options',
		array( $this, 'ppfm_plugin_validate_field')
		);
	add_settings_section(
		'ppfm_plugin_fields',
		__( 'Database Field Settings', 'ppfm'),
		array( $this, 'ppfm_plugin_fields_description'),
		'ppfm_plugin_db_fields'
		);
	add_settings_section(
		'ppfm_plugin_guid',
		__( 'Custom GUID Settings', 'ppfm' ),
		array( $this, 'ppfm_plugin_guid_description' ),
		'ppfm_plugin_db_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_title',
		__( 'Post title field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_title_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_category',
		__( 'Post category field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_category_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_body',
		__( 'Post body field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_body_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_image',
		__( 'Post image location field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_image_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_url',
		__( 'Podcast url field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_url_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_size',
		__( 'Podcast size field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_size_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_type',
		__( 'Podcast Media type field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_type_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_post_date',
		__( 'Post date field', 'ppfm'),
		array( $this, 'ppfm_plugin_post_date_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_fields'
		);
	add_settings_field(
		'ppfm_plugin_guid',
		__( 'Custom GUID String', 'ppfm'),
		array( $this, 'ppfm_plugin_guid_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_guid'
		);
	add_settings_field(
		'ppfm_plugin_db_id',
		__( 'MySQL Primary Key Field', 'ppfm'),
		array( $this, 'ppfm_plugin_db_id_input' ),
		'ppfm_plugin_db_fields',
		'ppfm_plugin_guid'
		);
}


function ppfm_plugin_admin_init(){
	register_setting(
		'ppfm_db_options',
		'ppfm_db_options'
		// array( $this, 'ppfm_plugin_db_validate')
		);	
	add_settings_section(
		'ppfm_plugin_db',
		__( 'Database Connection Settings', 'ppfm'),
		array( $this, 'ppfm_plugin_db_description'),
		'ppfm_plugin'
		);
	add_settings_field(
		'ppfm_plugin_db_host',
		__( 'Database Host', 'ppfm'),
		array( $this, 'ppfm_plugin_db_host_input' ),
		'ppfm_plugin',
		'ppfm_plugin_db'
		);
	// add_settings_field(
	// 	'ppfm_plugin_db_port',
	// 	__( 'Database Port', 'ppfm'),
	// 	array( $this, 'ppfm_plugin_db_port_input' ),
	// 	'ppfm_plugin',
	// 	'ppfm_plugin_db'
	// );
	add_settings_field(
		'ppfm_plugin_db_name',
		__( 'Database Name', 'ppfm'),
		array( $this, 'ppfm_plugin_db_name_input' ),
		'ppfm_plugin',
		'ppfm_plugin_db'
		);
	add_settings_field(
		'ppfm_plugin_db_user',
		__( 'Database User', 'ppfm'),
		array( $this, 'ppfm_plugin_db_user_input' ),
		'ppfm_plugin',
		'ppfm_plugin_db'
		);
	add_settings_field(
		'ppfm_plugin_db_password',
		__( 'Database Password', 'ppfm'),
		array( $this, 'ppfm_plugin_db_password_input' ),
		'ppfm_plugin',
		'ppfm_plugin_db'
		);
	add_settings_field(
		'ppfm_plugin_db_table',
		__( 'Database Table', 'ppfm'),
		array( $this, 'ppfm_plugin_db_table_input' ),
		'ppfm_plugin',
		'ppfm_plugin_db'
		);
	
}

function ppfm_plugin_db_description(){
	echo '<p>';
	_e('Enter your database connection details', 'ppfm');
	echo '</p>';
}
function ppfm_plugin_guid_description(){
	echo '<p>';
	_e( 'Enter a Enter a custom GUID string ( optional )', 'ppfm' );
	echo '</p>';
}

function ppfm_plugin_db_validate($item){
	$good = array();
	foreach ( $item as $k=>$v){
		// Check to see if MySQL ID field exists
		// echo '<pre>';
		// print_r( $item );
		// echo '</pre>';
		// die('ppfm 408');
		if ( $k == 'primary_key' ){
			if( FALSE === LocalPodcast::does_field_exist( $v )){
				$v = '';
			}
		}
		if ( $k == 'db_table' ){
			if ( !LocalPodcast::does_table_exist( $v )){
				$v = '';
			}
		}
		// Check to see if all fields have been filled, except GUID as that can be blank.
		if ( $v !== "" || $k == 'db_guid' ){
			$esc_v = sanitize_text_field($v);
			$good[$k] = $esc_v;
		} else {
			$err_msg = sprintf( __('Please verify your %s field is complete and correct', 'ppfm'), $k );
			add_settings_error(
				'ppfm_plugin_db_error',
				'ppfm_db_error',
				$err_msg,
				'error'
				);
		}
	}
	return $good;
}

function ppfm_plugin_fields_description(){
	echo '<p>';
	_e('Enter your database field names', 'ppfm');
	echo '</p>';
}

function ppfm_plugin_validate_field($input){
	$exist = array();
	foreach( $input as $k=>$v){
		$esc_v = sanitize_text_field($v);
		if( LocalPodcast::does_field_exist($esc_v) && !empty($esc_v) || $k == 'db_guid' ){
			$exist[$k] = $esc_v;
		} else {
			$err_msg = sprintf(__('Please verify your %s field is complete and correct', 'ppfm'), $k );
			add_settings_error(
				'ppfm_plugin_post_title',
				'ppfm_field_error',
				$err_msg,
				'error'
				);
		}
	}
	return $exist;;
}

function ppfm_plugin_db_host_input(){
	if ( isset( LocalPodcast::$db_options['db_host']) ){
		$db_host = LocalPodcast::$db_options['db_host'];
	} else {
		$db_host = "";
	}	
	echo "<input id='db_host' name='ppfm_db_options[db_host]' type='text' value='$db_host' />";
}
// function ppfm_plugin_db_port_input(){
// 	if ( isset (LocalPodcast::$db_options['db_port'] )){
// 		$db_port = LocalPodcast::$db_options['db_port'];
// 	} else {
// 		$db_port = '';
// 	}
// 	echo "<input id='db_port' name='ppfm_db_options[db_port]' type='text' value='$db_port' />";
// }
function ppfm_plugin_db_name_input(){
	if ( isset (LocalPodcast::$db_options['db_name'] )){
		$db_name = LocalPodcast::$db_options['db_name'];
	} else {
		$db_name = '';
	}
	echo "<input id='db_name' name='ppfm_db_options[db_name]' type='text' value='$db_name' />";
}
function ppfm_plugin_db_user_input(){
	if ( isset ( LocalPodcast::$db_options['db_user'])){
		$db_user = LocalPodcast::$db_options['db_user'];
	} else {
		$db_user = '';
	}
	echo "<input id='db_user' name='ppfm_db_options[db_user]' type='text' value='$db_user' />";
}
function ppfm_plugin_db_password_input(){
	if ( isset (LocalPodcast::$db_options['db_password'] )){
		$db_password = LocalPodcast::$db_options['db_password'];
	} else {
		$db_password = '';
	}
	echo "<input id='db_password' name='ppfm_db_options[db_password]' type='text' value='$db_password' />";
}
function ppfm_plugin_db_table_input(){
	if ( isset (LocalPodcast::$db_options['db_table'] )){
		$db_table = LocalPodcast::$db_options['db_table'];
	} else {
		$db_table = '';
	}
	echo "<input id='db_table' name='ppfm_db_options[db_table]' type='text' value='$db_table' />";
}
function ppfm_plugin_guid_input(){
	if (isset( LocalPodcast::$field_options['db_guid'] )){
		$db_guid = LocalPodcast::$field_options['db_guid'];
	} else {
		$db_guid = '';
	}
	echo "<input id='db_guid' name='ppfm_field_options[db_guid]' type='text' value='$db_guid' />";
}
function ppfm_plugin_db_id_input(){
	if ( isset (LocalPodcast::$field_options['primary_key'] )){
		$primary_key = LocalPodcast::$field_options['primary_key'];
	} else {
		$primary_key = '';
	}
	echo "<input id='primary_key' name='ppfm_field_options[primary_key]' type='text' value='$primary_key' />";
}
function ppfm_plugin_post_title_input(){
	if( isset( LocalPodcast::$field_options['post_title'] )){
		$post_title = LocalPodcast::$field_options['post_title'];
	} else {
		$post_title = '';
	}	
	echo "<input id='post_title' name='ppfm_field_options[post_title]' type='text' value='$post_title' />";
}
function ppfm_plugin_post_category_input(){
	if ( isset ( LocalPodcast::$field_options['post_category'])){
		$post_category = LocalPodcast::$field_options['post_category'];
	} else {
		$post_category = '';
	}
	echo "<input id='post_category' name='ppfm_field_options[post_category]' type='text' value='$post_category' />";
}
function ppfm_plugin_post_body_input(){
	if ( isset ( LocalPodcast::$field_options['post_body'])){
		$post_body = LocalPodcast::$field_options['post_body'];
	} else {
		$post_body = '';
	}
	echo "<input id='post_body' name='ppfm_field_options[post_body]' type='text' value='$post_body' />";
}
function ppfm_plugin_post_image_input(){
	if ( isset ( LocalPodcast::$field_options['post_image'])){
		$post_image = LocalPodcast::$field_options['post_image'];
	} else {
		$post_image = '';
	}
	echo "<input id='post_image' name='ppfm_field_options[post_image]' type='text' value='$post_image' />";
}
function ppfm_plugin_post_url_input(){
	if ( isset ( LocalPodcast::$field_options['post_url'])){
		$post_url = LocalPodcast::$field_options['post_url'];
	} else {
		$post_url = '';
	}
	echo "<input id='post_url' name='ppfm_field_options[post_url]' type='text' value='$post_url' />";
}
function ppfm_plugin_post_size_input(){
	if ( isset ( LocalPodcast::$field_options['post_size'])){
		$post_size = LocalPodcast::$field_options['post_size'];
	} else {
		$post_size = '';
	}
	echo "<input id='post_size' name='ppfm_field_options[post_size]' type='text' value='$post_size' />";
}
function ppfm_plugin_post_type_input(){
	if ( isset ( LocalPodcast::$field_options['post_type'])){
		$post_type = LocalPodcast::$field_options['post_type'];
	} else {
		$post_type = '';
	}
	echo "<input id='post_type' name='ppfm_field_options[post_type]' type='text' value='$post_type' />";
}
function ppfm_plugin_post_date_input(){
	if ( isset (LocalPodcast::$field_options['post_date'] )){
		$post_date = LocalPodcast::$field_options['post_date'];
	} else {
		$post_date = '';
	}
	echo "<input id='post_date' name='ppfm_field_options[post_date]' type='text' value='$post_date' />";
}




}

$iLoveMySon = new ppfmPlugin();

