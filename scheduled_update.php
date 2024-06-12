<?php

require_once "logger.php";

$neoweb_enable_update_override = false;

class NeowebUpdateScheduler{

    // Scheduled time of the update (Using WP-timezone)
    const SCHEDULED_TIME = "02:00";

    function __construct(){
        get_option('timezone_string');
        $this->set_autoupdate_preferences();

        $this->init_cron();


    // Schedule Cron job in order to update at specified point of time
    function init_cron(){
        if ( ! wp_next_scheduled( 'neoweb_run_updates' ) ) {
            $timezome = get_option('timezone_string');
            $local_datetime = new DateTime(self::SCHEDULED_TIME , wp_timezone());
            $local_datetime->setTimezone(new DateTimeZone('UTC'));
            $timestamp = $local_datetime->getTimestamp();
    
            // Schedule the event
            wp_schedule_event($timestamp, 'daily', 'neoweb_run_updates' );
        }

        // Add event action
        add_action( 'neoweb_run_updates', array ( $this, 'run_updates' ));
    }

    function disable_cron(){
        wp_clear_scheduled_hook( 'neoweb_run_updates' );
    }

    // Preset, which updates will be auto-updated
    function set_autoupdate_preferences(){
        // Plugins
        add_filter( 'auto_update_plugin', '__return_true' );

        // Themes
        add_filter( 'auto_update_theme', '__return_true' );

        // Translation updates
        add_filter( 'auto_update_translation', '__return_true' );

        // Core
        add_filter( 'allow_dev_auto_core_updates', '__return_false' ); // Development updates
        add_filter( 'allow_minor_auto_core_updates', '__return_true' ); // Minor updates
        add_filter( 'allow_major_auto_core_updates', '__return_true' ); // Major updates
        // Allow all core (overwrites dev/minor/core setting if set to true)
        // reference: https://developer.wordpress.org/reference/functions/core_auto_updates_settings/
        // add_filter( 'auto_update_core', 'neoweb_auto_update_schedule', 10, 2 );
    }

}

$neoWebUpdater = new NeowebUpdateScheduler;

?>