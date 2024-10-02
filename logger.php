<?php
/*
 * Logger for Neoweb auto updater
 * 
 * PHP 7.4+
 * 
 * LICENSE: MIT
 * 
 * @author: Neocode
 * @version 1.0.0
 * 
*/ 

class NeowebLoger{
    function __construct(){
        register_deactivation_hook( NEOWEB_UPDATER_PLUGINFILE_PATH ,  array($this, "deactivate_actions") );
    }

    public function log($message){
        if (defined('NEOWEB_UPDATER_PATH') && NEOWEB_UPDATER_PATH) {
            $now = current_datetime();
            $log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
            
            file_put_contents($log_file, $now->format('Y-m-d H:i:s') . ": " . print_r( $message, true ) ."\n", FILE_APPEND);

        } else {
            error_log('Neoweb auto-updater NEOWEB_UPDATER_PATH not defined - logging impossible');
        }
    }

    private function deactivate_actions(){
        // delete logfile
        $log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
        if ( file_exists($log_file)){
            unlink($log_file);
        }
    }
}

$logger = new NeowebLoger();
?>