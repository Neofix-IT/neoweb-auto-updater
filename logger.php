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

if (! defined('ABSPATH')) {
    exit;
}

    }

    public function log($message){
        if (defined('NEOWEB_UPDATER_PATH') && NEOWEB_UPDATER_PATH) {
            $now = current_datetime();
            $log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
            
            file_put_contents($log_file, $now->format('Y-m-d H:i:s') . ": " . print_r( $message, true ) ."\n", FILE_APPEND);

            // limit file size
            if(file_exists($log_file)){
                $log_limit = 101;   // Set +1 for (empty) newline at the EOF
                if( defined("NEOWEB_LOG_LENGTH") && NEOWEB_LOG_LENGTH ){
                    $log_limit = NEOWEB_LOG_LENGTH + 1; // Set +1 for (empty) newline at the EOF
                }

                $content = $this->get_last_file_lines($log_file, $log_limit);
                file_put_contents($log_file, $content);
            } 
        } else {
            error_log('Neoweb auto-updater NEOWEB_UPDATER_PATH not defined - logging impossible');
        }
    }

    function deactivate_actions(){
        // delete logfile
        $log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
        if ( file_exists($log_file)){
            unlink($log_file);
        }
    }

    static private function get_last_file_lines($file, $max_lines){
        $data = fopen($file, 'r');
        $pos = -1;  // Start position
    
        $output = '';
        $current_line = '';
        $current_linecount = 0;
        
        while (fseek($data, $pos, SEEK_END) !== -1) {
            $char = fgetc($data);
    
            $current_line = $char . $current_line;
    
            if ($char == "\n") {
                
                if( ++$current_linecount == $max_lines){
                    // Remove newline char (which corresponds to the line above)
                    $current_line = substr($current_line, 1);
                    break;
                } 
    
                $output = $current_line . $output;
                $current_line = '';
            }
            $pos--;
        }
        if( !empty($current_line) ){
            $output = $current_line . $output;
        }
    
        fclose($data);
        return $output;
    }
}

$logger = new NeowebLogger();
?>