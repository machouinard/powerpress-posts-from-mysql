<?php

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/************************** CREATE A PACKAGE CLASS **************************/
class PPFM_List_Table extends WP_List_Table {

    private $podcasts;

    function __construct(){
        require_once plugin_dir_path( __FILE__ ) . 'class.local-podcast.php';
        require_once plugin_dir_path( __FILE__ ) . 'class.podcast.php';
        global $status, $page, $current_user;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'podcast',
            'plural'    => 'podcasts',
            'ajax'      => false        //does not support ajax. yet.
            ) );

    }

    /**************************************************************************
    *Styling the columns
    **************************************************************************/


    /** ************************************************************************
     * For more detailed insight into how columns are handled, take a look at
     * WP_List_Table::single_row_columns()
     *
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
        	case 'posted':
            return null;
            case 'title':
            case 'url':
            return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
            }
        }


    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     *
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     *
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (podcast title only)
     **************************************************************************/
    function column_title($item){

        // Build actions
        $actions = self::build_actions( $item );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item[LocalPodcast::$field_options['post_title']],
            /*$2%s*/ $item[LocalPodcast::$field_options['primary_key']],
            /*$3%s*/ $this->row_actions($actions)
            );
    }

    function column_url($item){
    	return $item[LocalPodcast::$field_options['post_url']];
    }

    function column_posted($item){
        $db_id = $item[ 'id' ];
        if( LocalPodcast::guid_exists_by_db_id( $db_id )){
          return '√';
      }
  }

    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     *
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("podcast")
            /*$2%s*/ $item[LocalPodcast::$field_options['primary_key']]                //The value of the checkbox should be the record's id
            );
    }


    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     *
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     *
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'     => '<input type="checkbox" />', //Render a checkbox instead of text
            'posted' => 'Posted',
            'title'  => 'Title',
            'url'    => 'URL'
            );
        return $columns;
    }

    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
     * you will need to register it here. This should return an array where the
     * key is the column that needs to be sortable, and the value is db column to
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     *
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     *
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'  => array('title',false),
            'url'    => array('url',false)
            );
        return $sortable_columns;
    }


    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     *
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     *
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     *
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'create' => 'Post',
            'draft'  => 'Draft',
            'remove' => 'Remove'
            );
        return $actions;
    }


    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     *
     * @see $this->prepare_items()
     *
     *
     *
     **************************************************************************/
    function process_bulk_action() {


    //Detect when a bulk action is being triggered...

        if ( 'create' == $this->current_action()){
            if (is_array($_GET['podcast'])){
                wp_verify_nonce( 'bulk-actions' );
                foreach ( $_GET['podcast'] as $id ) {
                    $podcast = new Podcast($id);
    //does GUID exist?
                    if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ){
    // YES - what's the status?
                        $status = get_post_status( $postID );
    // Draft - update post status to publish
                        if ( $status == 'draft' ) {
                            if( is_wp_error (Podcast::update_podcast_status( $postID, 'publish' ))) {
                                echo 'shit. 255 ppfm_list_table';die();
                            }
                        }
                    } else {
    // NO - publish()
                        $result = $podcast->publish();
                        if( is_wp_error( $result )){
                            wp_die( $result->get_error_message(), __( 'Publish Error', 'ppfm' ) );
                        }
                    }
                }
            } else {
               wp_verify_nonce( 'ppfm_nonce_url_check' );
               $id = $_GET[ 'podcast' ];
               $podcast = new Podcast( $id );
    //does GUID exist?
               if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ){
    // YES - what's the status?
                $status = get_post_status( $postID );
    // Draft - update post status to publish
                if ( $status == 'draft' ) {
                    if( is_wp_error (Podcast::update_podcast_status( $postID, 'publish' ))) {
                        echo 'shit. 255 ppfm_list_table';die();
                    }
                }
            } else {
    // NO - publish()
                $result = $podcast->publish();
                if( is_wp_error( $result )){
                    wp_die( $result->get_error_message(), __( 'Publish Error', 'ppfm' ) );
                }
            }
        }
    }
    if ( 'draft' == $this->current_action()){
        if( is_array($_GET['podcast'])){
            wp_verify_nonce( 'bulk-actions' );
            foreach ( $_GET[ 'podcast' ] as $id ) {
                $podcast = new Podcast($id);
    // Does GUID exist?
                if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ){
    // YES - what's the status?
                    $status = get_post_status( $postID );
                    if ( !($status == 'draft' ) ){
                        if( is_wp_error (Podcast::update_podcast_status( $postID, 'draft' ))) {
                            wp_die( $result->get_error_message(), __( 'Status Update Error', 'ppfm' ) );
                        }
                    }
                } else {
                    $result = $podcast->publish_draft();
                    if( is_wp_error( $result )){
                        wp_die( $result->get_error_message(), __( 'Publish Error', 'ppfm' ) );
                    }
                }
            }
        } else {
            wp_verify_nonce( 'ppfm_nonce_url_check' );
            $id = $_GET['podcast'];
            $podcast = new Podcast($id);
    // Does GUID exist?
            if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ){
    // YES - what's the status?
                $status = get_post_status( $postID );
                if ( $status == 'draft' ) {
                    // wp_die('240 ppfm_list: ' . $status);
                    if( is_wp_error (Podcast::update_podcast_status( $postID, 'publish' ))) {
                        wp_die( $result->get_error_message(), __( 'Status Update Error', 'ppfm' ) );
                    }
                } else {
                    if( is_wp_error (Podcast::update_podcast_status( $postID, 'draft' ))) {
                        wp_die( $result->get_error_message(), __( 'Status Update Error', 'ppfm' ) );
                    }
                }
            } else {
                $result = $podcast->publish_draft();
                if( is_wp_error( $result )){
                    wp_die( $result->get_error_message(), __( 'Publish Error', 'ppfm' ) );
                }
            }
        }
    }
    if ( 'remove' == $this->current_action()){
        if( is_array($_GET['podcast'])){
            wp_verify_nonce( 'bulk-actions' );
            foreach ( $_GET[ 'podcast' ] as $id ) {
                $podcast = new Podcast($id );
    // Does GUID exist?
                if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ) {
                    $result = $podcast->remove();
                    if( is_wp_error( $result )){
                        wp_die( $result->get_error_message(), __( 'Deletion Error', 'ppfm' ) );
                    }
                }

            }
        } else {
            wp_verify_nonce( 'ppfm_nonce_url_check' );
            $id = $_GET['podcast'];

    // Does GUID exist?
            if( $postID = LocalPodcast::guid_exists_by_db_id( $id ) ) {
                $podcast = new Podcast($id );
                $result  = $podcast->remove();
                if( is_wp_error( $result )){
                    wp_die( $result->get_error_message(), __( 'Deletion Error', 'ppfm' ) );
                }
            }
        }
    }

}


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     *
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items($search=null) {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = $this->get_items_per_page('podcasts_per_page', 5);


        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();


        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        // $this->_column_headers = array($columns, $hidden, $sortable);
        $this->_column_headers = $this->get_column_info();

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();


        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        // $data = $this->podcasts;
        $data = LocalPodcast::get_podcasts($search);

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        // function usort_reorder was here for some reason-- Moved outside of prepare_items() *******
        //
        usort($data, array( $this, 'usort_reorder'));


        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         *
         * In a real-world situation, this is where you would place your query.
         *
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/


        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($data);


        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);



        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;


        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
    }

    function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order   = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result  = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }

    // Build actions array
        static function build_actions( $item ) {

            $actions        = array();
            $draft          = sprintf('?page=%s&action=%s&podcast=%s',$_REQUEST['page'],'draft',$item[LocalPodcast::$field_options['primary_key']]);
            $remove         = sprintf('?page=%s&action=%s&podcast=%s',$_REQUEST['page'],'remove',$item[LocalPodcast::$field_options['primary_key']]);
            $create         = sprintf('?page=%s&action=%s&podcast=%s',$_REQUEST['page'],'create',$item[LocalPodcast::$field_options['primary_key']]);

            $draft_url      = '<a href=' . wp_nonce_url( $draft, "ppfm_nonce_url_check" ) .'>Post Draft</a>';
            $remove_url     = '<a href=' . wp_nonce_url( $remove, "ppfm_nonce_url_check" ) . '>Remove</a>';
            $create_url     = '<a href=' . wp_nonce_url( $create, "ppfm_nonce_url_check" ) .'>Post</a>';
            $to_draft_url   = '<a href=' . wp_nonce_url( $draft, "ppfm_nonce_url_check" ) .'>Switch to draft</a>';
            $to_publish_url = '<a href=' . wp_nonce_url( $draft, "ppfm_nonce_url_check" ) .'>Switch to published</a>';


            $db_id = $item[ 'id' ];
        // build guid for this db_id
            $guid = LocalPodcast::create_guid( $db_id );
        // does a post with this guid exist?
            if ( $post_id = LocalPodcast::guid_exists( $guid ) ){
            // if YES, what is the post status?
                $status = get_post_status( $post_id );
            // if publish, create draft and remove links
                if ( $status == 'publish' ) {
                    $actions[ 'draft' ]  = $to_draft_url;
                    $actions[ 'remove' ] = $remove_url;
                // else if draft, create update and remove links
                } else {
                    $actions[ 'draft' ]  = $to_publish_url;
                    $actions[ 'remove' ] = $remove_url;
                }
            } else {
            // if NO, create publish publish and draft links
                $actions[ 'create' ] = $create_url;
                $actions[ ' draft' ] = $draft_url;
            }

            return $actions;
        }


    }





