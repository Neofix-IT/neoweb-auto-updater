<?php
/*
 * Plugin Name: Neoweb AutoUpdater
 * Plugin URI: https://neoweb.ch/
 * Description: Auto-updater for WP-core, translations, Pugins and Themes
 * Version: 1.0.1
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

define( 'NEOWEB_UPDATER_PATH', plugin_dir_path( __FILE__ ) );
define( 'NEOWEB_UPDATER_PLUGINFILE_PATH', __FILE__ );

// Enable Test-update admin panel menu page
define('NEOWEB_UPDATER_ADMINPANEL_VISIBLE', true);
define('NEOWEB_LOG_LENGTH', 150);

require_once 'scheduled_update.php';
require_once 'admin-panel.php';

?>