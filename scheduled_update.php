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

        // Force auto-update schedule
        add_filter( 'automatic_updater_disabled', array( $this, "conditional_force_enable_update" ), 10, 1 );

        // Disable Wordpress health Auto-updater check
        add_filter('site_status_tests', array( $this, 'ignore_auto_update_check' ), 10, 1);

        add_action('automatic_updates_complete', array( $this, 'neoweb_log_updates' ), 10, 1 );

        // Remove cron event after plugin is deactivated
        register_deactivation_hook( NEOWEB_UPDATER_PLUGINFILE_PATH ,  array( $this, 'disable_cron') );
    }

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

    function run_updates(){
        neoweb_log("Start neoweb auto-update");

        // Force auto-update enabled using "automatic_updater_disabled" filter
        global $neoweb_enable_update_override;
        $neoweb_enable_update_override = true;
        neoweb_log('set global override');

        try{
            // request-update.php
            include_once( ABSPATH . '/wp-includes/update.php' );
			wp_maybe_auto_update();
        } finally {
            neoweb_log('reset global override');
            $neoweb_enable_update_override = false;
        }
    }

    function neoweb_log_updates($update_results) {
        neoweb_log('Updates completed');
        
        if( isset($update_results['plugin']) ){
            $plugins_info = 'Updated Plugin(s):';
            foreach ( $update_results['plugin'] as $plugin ) {
                $plugins_info .= ' "' . $plugin->name . ' ' . $plugin->item->new_version . '"';
            }
            neoweb_log($plugins_info);
        }

        if( isset($update_results['theme']) ){
            $themes_info = 'Updated Theme(s):';
            foreach ( $update_results['theme'] as $theme ) {
                $themes_info .= ' "' . $theme->name . ' ' . $theme->item->new_version . '"';
            }
            neoweb_log($themes_info);
        }

        if( isset($update_results['core']) ){
            $core_info = 'Updated WP Core Version(s):';
            foreach ( $update_results['core'] as $core ) {
                $core_info .= ' "' . $core->name . '"';
            }
            neoweb_log($core_info);
        }
    }

    function conditional_force_enable_update($disabled){
        global $neoweb_enable_update_override;

        if( $neoweb_enable_update_override ){
            neoweb_log('Updater disabled check: Updates enabled');
        } else {
            neoweb_log('Updater disabled check: Update disabled');
        }

        // If override is enabled, then set auto-updater disabled=false
        // Important: Inverted logic here :)
        return !$neoweb_enable_update_override;
    }

    function ignore_auto_update_check($tests){
        unset( $tests['async']['background_updates'] );
        return $tests;
    }
}

$neoWebUpdater = new NeowebUpdateScheduler;

?>