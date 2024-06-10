<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Override default arbitrary twicedaily timing of wp_schedule_update_checks (in wp-includes/update.php) to choose a specific time that potentially satisfies the above filter, so we won't continually miss our window by sheer bad luck.
// Note that we still need a page load at an acceptable time to actually trigger the task; see also https://developer.wordpress.org/plugins/cron/
function neoweb_schedule_auto_update($event) {
    switch ($event->hook) {
            case 'wp_version_check':
                $event->schedule = "daily";
                $desired_time = new DateTime(NEOWEB_PLANNED_UPDATE_TIMEPOINT, new DateTimeZone(NEOWEB_UPDATE_TZ));
                if ($desired_time->getTimestamp() < time()) $desired_time->modify('+1 day');
                $event->timestamp = $desired_time->getTimestamp();
            case 'wp_update_plugins':
                $event->schedule = "daily";
                $desired_time = new DateTime(NEOWEB_PLANNED_UPDATE_TIMEPOINT, new DateTimeZone(NEOWEB_UPDATE_TZ));
                if ($desired_time->getTimestamp() < time()) $desired_time->modify('+1 day');
                $event->timestamp = $desired_time->getTimestamp();
            case 'wp_update_themes':
                $event->schedule = "daily";
                $desired_time = new DateTime(NEOWEB_PLANNED_UPDATE_TIMEPOINT, new DateTimeZone(NEOWEB_UPDATE_TZ));
                if ($desired_time->getTimestamp() < time()) $desired_time->modify('+1 day');
                $event->timestamp = $desired_time->getTimestamp();
                    
    }
    return $event;
}

add_filter( 'schedule_event', 'neoweb_schedule_auto_update' );

?>