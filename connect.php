<?php
/**
 * Description of connect
 *
 * @author Mark Chouinard
 */
class Connect {
    public $DBH;
    private static $DB_TABLE;
    private static $instance;
    private static $error;
    private static $field_errors = 0;
    private static $connection = FALSE;


    private function __construct() {
        $HOST = get_option('mac_pfd_db_host');
        
        $NAME = get_option('mac_pfd_db_name');
        if(empty($NAME)){
            $NAME = 'XXX';
        }
        $USER = get_option('mac_pfd_db_username');
        $PASS = get_option('mac_pfd_db_password');
        self::$DB_TABLE = get_option('mac_pfd_db_table');
        try{
            $this->DBH = new PDO("mysql:host=$HOST;dbname=$NAME", $USER, $PASS);
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            self::$error = 0;
            self::$connection = TRUE;
        }catch(PDOException $e){
            self::$error = 1;
            self::$field_errors++;
            return 'negative';
            //echo 'Error: '.$e->getMessage();
        }
    }
    
    public static function get_instance($renew = FALSE){
        if(!isset(self::$instance) || $renew !== FALSE){
                $object = __CLASS__;
                self::$instance = new $object;
            }
            return self::$instance->DBH;

    }
    
    protected static function __does_field_exist($field){
        if($DBH = self::get_instance()){
            $table = self::$DB_TABLE;
            if(self::__does_table_exist($table)){
                $sql = "SHOW COLUMNS FROM `$table` LIKE '$field'";
                if (count($DBH->query($sql)->fetchAll())) {
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }

        }else{
            return FALSE;
        }
    }
    
    public static function get_field_class($field){
        if(!empty($field)){
            if(self::__does_field_exist($field)){
                return 'affirmative';
            }else{
                self::$field_errors++;
                return 'negative';
            }
        }else{
            self::$field_errors++;
            return 'negative';
        }
    }
    
    public static function get_db_field_class(){
        $DBH = self::get_instance(TRUE);
        if($DBH){
        if(self::$error == 1){
            self::$field_errors++;
            return 'negative';
        }else{
            return 'affirmative';
        }
        }else{
            self::$field_errors++;
            return 'negative';
        }
    }
    
    public static function get_table_field_class($table){
        if(self::__does_table_exist($table) && !empty($table)){
            return 'affirmative';
        }else{
            self::$field_errors++;
            return 'negative';
        }
    }
    
    protected static function __does_table_exist($table){
        
            $DBH = self::get_instance(TRUE);
            if($DBH && !empty($table)){
                $sql = ("SHOW TABLES LIKE '$table'");
                if($DBH->query($sql)->fetch()){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        
    }
    public static function table_exist($table){
            $DBH = self::get_instance(TRUE);
            $sql = ("SHOW TABLES LIKE '$table'");
            if($DBH->query($sql)->fetch()){
                return TRUE;
            }else{
                return FALSE;
            }
        
    }
    
    public static function field_errors(){
        return self::$field_errors;
    }
    
    public static function connection(){
        return self::$connection;
    }
    
}

?>
