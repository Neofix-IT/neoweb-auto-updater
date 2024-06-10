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
define('NEOWEB_UPDATER_ADMINPANEL_VISIBLE', false);



require_once 'scheduler.php';
require_once 'logger.php';
