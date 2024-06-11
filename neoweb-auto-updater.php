<?php
/*
 * Plugin Name: Neoweb AutoUpdater
 * Plugin URI: https://neoweb.ch/
 * Description: Auto-updater for WP-core, translations, Pugins and Themes
 * Version: 1.0.0
 * Requires at least: 6.0
 * Required PHP: 7.5
 * Author: Neoweb
 * Author URI: https://neoweb.ch/
 * License: MIT
 * License URI: https://opensource.org/license/mit
 * 
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Plugin config
 * Please note: Current setting is running only on Monday - Thursday
 * Can be changed within func neoweb_auto_update_schedule()
 * 
*/

// TZ beeing used for NEOWEB_UPDATE_ENABLE_START_TIME & NEOWEB_UPDATE_ENABLE_START_TIME
define('NEOWEB_UPDATE_TZ', 'Europe/Zurich');

// Min required time in order to allow auto-updates
define('NEOWEB_UPDATE_ENABLE_START_TIME', '00:00');

// Max allowed time in order to allow auto-updates 
define('NEOWEB_UPDATE_ENABLE_STOP_TIME', '06:00');

// Timepoint for scheduled update by WP itself.
define('NEOWEB_PLANNED_UPDATE_TIMEPOINT', '02:00');

// Enable Test-update admin panel menu page
define('NEOWEB_UPDATER_ADMINPANEL_VISIBLE', true);



define( 'NEOWEB_UPDATER_PATH', plugin_dir_path( __FILE__ ) );

require_once 'scheduler.php';
require_once 'admin-panel.php';
require_once 'logger.php';

add_action('automatic_updates_complete', "neoweb_log_updates", 10, 1 );
function neoweb_log_updates($update_results) {
    neoweb_log('neoweb_auto_updater completed: ' . print_r($update_results, true) );
}

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

// Schedule auto updates outside of business hours
add_filter( 'automatic_updater_disabled', 'neoweb_auto_update_schedule', 10, 1 );

// Only allow auto-updates during Mon-Thu mornings so we'll have time during business hours to fix any problems that arise.
// See also wp-admin/includes/class-wp-automatic-updater.php
function neoweb_auto_update_schedule( $disable ) {
    // Defaults to disabled = false
    $disable = true;

    // Get current time using TZ Zurich, Switzerland
    $tz = new DateTimeZone(NEOWEB_UPDATE_TZ);
    $now = new DateTimeImmutable('now', $tz);

    neoweb_log('update-checked');

    // disable if after 7 am
    $is_after_start_time = $now >= new DateTimeImmutable(NEOWEB_UPDATE_ENABLE_START_TIME, $tz);
    $is_before_stop_time = $now < new DateTimeImmutable(NEOWEB_UPDATE_ENABLE_STOP_TIME, $tz);

    if( $is_after_start_time && $is_before_stop_time ) $disable = false;

    // Disable after thursday
    // https://www.php.net/manual/en/datetime.format.php
    // 1 (for Monday) through 7 (for Sunday)
    if ($now->format('N') >= 5) $disable = true;

    if( !$disable ){
        neoweb_log('update-allowed');
    }
    return $disable;
}

?>