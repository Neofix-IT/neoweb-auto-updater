<?php

/*
 * Neoweb AutoUpdater adminpanel
 * 
 * Allowing
 * 
*/

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

class NeowebAdminPanel
{
	private $updater;
	private $logger;

	function __construct($updater, $logger)
	{
		$this->updater = $updater;
		$this->logger = $logger;

		add_action('admin_menu', array($this, "add_admin_panel_page"));
		add_action('rest_api_init', array($this, "init_admin_rest_endpoints"));
	}

	function add_admin_panel_page()
	{
		add_menu_page(
			'Neoweb AutoUpdater',
			'AutoUpdater',
			'update_core',
			'auto-updater', // hook/slug of page
			array($this, "render_admin_panel"), // function to render page
			'dashicons-update',
			30,
		);
	}

	function render_admin_panel()
	{
		include NEOWEB_UPDATER_PATH . "/templates/admin_panel.php";
	}

	function init_admin_rest_endpoints()
	{
		register_rest_route('neoweb/v1', '/run-update', array(
			'methods' => 'GET',
			'callback' => array($this, "run_update"),
			'permission_callback' => array($this, "has_updater_permission"),
		));

		register_rest_route('neoweb/v1', '/clear-log', array(
			'methods' => 'GET',
			'callback' => array($this, "clear_log"),
			'permission_callback' => array($this, "has_updater_permission"),
		));
	}

	function run_update()
	{
		try {
			$this->logger->log("Starte Updates via Adminpanel.");

			$this->updater->update();

			$this->logger->log("Updates via Adminpanel erfolgreich beendet");
			return "Updates erfolgreich durchgeführt";
		} catch (Exception $e) {
			$this->logger->log("Fehler beim Update via Adminpanel: " . $e->getMessage());
		}

		return 'Unerwarteter Fehler bei den Updates';
	}

	function clear_log()
	{
		try {
			$log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
			if (unlink($log_file)) {
				return "Logs gelöscht";
			}
		} catch (Exception $e) {
			$this->logger->log("Fehler beim Löschen der Logdatei via Adminpanel: " . $e->getMessage());
		}

		return 'Unerwarteter Fehler beim Löschen der Logs';
	}

	function has_updater_permission()
	{
		$core = current_user_can('update_core');
		$plugins = current_user_can('update_plugins');
		$themes =  current_user_can('update_themes');

		return $core && $plugins && $themes;
	}
}
