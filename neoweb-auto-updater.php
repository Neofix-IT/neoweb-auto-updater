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
if (! defined('ABSPATH')) {
	exit;
}

define('NEOWEB_UPDATER_PATH', plugin_dir_path(__FILE__));
define('NEOWEB_UPDATER_PLUGINFILE_PATH', __FILE__);

// Log length
define('NEOWEB_LOG_LENGTH', 150);

require_once NEOWEB_UPDATER_PATH . 'modules/logger.php';
require_once NEOWEB_UPDATER_PATH . 'modules/scheduled_update.php';
require_once NEOWEB_UPDATER_PATH . 'modules/updater.php';
require_once NEOWEB_UPDATER_PATH . 'modules/github-updater.php';
require_once NEOWEB_UPDATER_PATH . 'modules/admin-panel.php';


class NeowebAutoUpdater
{
	private $logger;
	private $updater;
	private $scheduler;
	private $admin_panel;
	private $github_updater;

	function __construct()
	{
		$this->logger = new NeowebLogger();
		$this->updater = new NeowebUpdater($this->logger);
		$this->scheduler = new NeowebUpdateScheduler($this->updater, $this->logger);
		$this->admin_panel = new NeowebAdminPanel($this->updater, $this->logger);

		// require_once 'modules/github-updater.php';
		$this->github_updater = new GitHubAutoUpdater(
			'neoweb-auto-updater/neoweb-auto-updater.php',
			'Neofix-IT/neoweb-auto-updater'
		);
	}
}
new NeowebAutoUpdater();
