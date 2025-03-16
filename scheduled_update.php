<?php

class NeowebUpdateScheduler
{
    // Scheduled time of the update (Using WP-timezone)
    const SCHEDULED_TIME = "02:00";

    private $updater;

    function __construct($updater)
    {
        $this->updater = $updater;

        $this->init_cron();

        // Remove cron event after plugin is deactivated
        register_deactivation_hook(NEOWEB_UPDATER_PLUGINFILE_PATH,  array($this, 'disable_cron'));
    }

    // Schedule Cron job in order to update at specified point of time
    function init_cron()
    {
        if (! wp_next_scheduled('neoweb_run_updates')) {
            $timezome = get_option('timezone_string');
            $local_datetime = new DateTime(self::SCHEDULED_TIME, wp_timezone());
            $local_datetime->setTimezone(new DateTimeZone('UTC'));
            $timestamp = $local_datetime->getTimestamp();

            // Schedule the event
            wp_schedule_event($timestamp, 'daily', 'neoweb_run_updates');
        }

        // Add event action
        add_action('neoweb_run_updates', array($this->updater, 'update'));
    }

    function disable_cron()
    {
        wp_clear_scheduled_hook('neoweb_run_updates');
    }
}
