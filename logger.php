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

function neoweb_log($message){

    if (defined('NEOWEB_UPDATER_PATH')) {
        $tz = new DateTimeZone(NEOWEB_UPDATE_TZ);
        $now = new DateTimeImmutable('now', $tz);
        $log_file = NEOWEB_UPDATER_PATH . "/log.txt";
        
        file_put_contents($log_file, $now->format('Y-m-d H:i:s') . ": " . print_r( $message, true ) ."\n", FILE_APPEND);
    } else {
        error_log('Neoweb auto-updater NEOWEB_UPDATER_PATH not defined - logging impossible');
    }
    return;
}
?>