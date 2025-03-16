<?php

/**
 * File: updater.php
 * Description: Sets update preferences and  handles the update process. Does surpress default update schedules.
 * Author: Martin Heini
 * Author URI: https://neocode.ch
 * Version: 1.0
 * License: MIT
 * Created: 2025-03-15
 * Updated: 2025-03-15
 */

if (! defined('ABSPATH')) {
    exit;
}

class NeowebUpdater
{
    private $logger;
    private $override = false;
    function __construct($logger)
    {
        $this->logger = $logger;

        $this->set_autoupdate_preferences();

        // Force auto-update schedule
        add_filter('automatic_updater_disabled', array($this, "force_override_update_behavior"), 10, 1);

        // Disable Wordpress health Auto-updater check
        add_filter('site_status_tests', array($this, 'ignore_auto_update_check'), 10, 1);

        add_action('automatic_updates_complete', array($this, 'neoweb_log_updates'), 10, 1);
    }

    // Preset, which updates will be auto-updated
    function set_autoupdate_preferences()
    {
        // Set Plugins, Theme and Translation plugins to auto-update
        add_filter('auto_update_plugin', '__return_true');
        add_filter('auto_update_theme', '__return_true');
        add_filter('auto_update_translation', '__return_true');

        // Core
        add_filter('allow_dev_auto_core_updates', '__return_false'); // Development updates
        add_filter('allow_minor_auto_core_updates', '__return_true'); // Minor updates
        add_filter('allow_major_auto_core_updates', '__return_true'); // Major updates
        // Allow all core (overwrites dev/minor/core setting if set to true)
        // reference: https://developer.wordpress.org/reference/functions/core_auto_updates_settings/
        // add_filter( 'auto_update_core', 'neoweb_auto_update_schedule', 10, 2 );
    }

    function ignore_auto_update_check($tests)
    {
        unset($tests['async']['background_updates']);
        return $tests;
    }

    function neoweb_log_updates($update_results)
    {
        $this->logger->log('Updates completed');

        if (isset($update_results['plugin'])) {
            $plugins_info = 'Updated Plugin(s):';
            foreach ($update_results['plugin'] as $plugin) {
                $plugins_info .= ' "' . $plugin->name . ' ' . $plugin->item->new_version . '"';
            }
            $this->logger->log($plugins_info);
        }

        if (isset($update_results['theme'])) {
            $themes_info = 'Updated Theme(s):';
            foreach ($update_results['theme'] as $theme) {
                $themes_info .= ' "' . $theme->name . ' ' . $theme->item->new_version . '"';
            }
            $this->logger->log($themes_info);
        }

        if (isset($update_results['core'])) {
            $core_info = 'Updated WP Core Version(s):';
            foreach ($update_results['core'] as $core) {
                $core_info .= ' "' . $core->name . '"';
            }
            $this->logger->log($core_info);
        }
    }

    function force_override_update_behavior($disabled)
    {

        if ($this->override) {
            $this->logger->log('Updater disabled check: Updates enabled');
            return false; // return false because disabled = false -> inverted logic here.
        }

        // If override is enabled, then set auto-updater disabled=false
        // Important: Inverted logic here :)
        $this->logger->log('Updater disabled check: Update disabled');
        return true;
    }

    function update()
    {
        delete_site_transient('update_plugins'); // Clear cached plugin update data
        wp_update_plugins(); // Trigger a fresh plugin update check

        delete_site_transient('update_themes'); // Clear cached theme update data
        wp_update_themes(); // Trigger a fresh theme update check

        delete_site_transient('update_core'); // Clear cached core update data
        wp_version_check(); // Trigger a fresh core update check


        $this->logger->log("Start neoweb auto-update");

        // Force auto-update enabled using "automatic_updater_disabled" filter
        $this->logger->log("Set global override to true.");
        $this->override = true;

        try {
            // request-update.php
            include_once(ABSPATH . '/wp-includes/update.php');
            wp_maybe_auto_update();
        } catch (Exception $e) {
            $this->logger("Fehler beim Update: " . $e->getMessage());
        } finally {
            $this->logger->log('reset global override');
            $this->override = false;
        }
    }
}
