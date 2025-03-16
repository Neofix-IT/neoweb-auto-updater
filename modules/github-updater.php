<?php

/**
 * File: github-updater.php
 * Description: This module adds an auto-update feature for the Neoweb Updater
 * Author: Martin Heini
 * Author URI: https://neocode.ch
 * Version: 1.0
 * License: MIT
 * Created: 2025-03-16
 * Updated: 2025-03-16
 */


// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class GitHubAutoUpdater
{
    private $slug;
    private $github_repo;

    function __construct($slug, $github_repo)
    {
        $this->github_repo = $github_repo;
        $this->slug = $slug;

        add_filter('update_plugins_github.com', array($this, 'update_check'), 10, 4);
    }

    function update_check($update, array $plugin_data, string $plugin_file, $locales)
    {
        // Only check this plugin
        if ($this->slug !== $plugin_file) {
            return $update;
        }

        // Already completed update check elsewhere
        if (!empty($update)) {
            return $update;
        }

        // Call GitHub release API
        $response = wp_remote_get("https://api.github.com/repos/{$this->github_repo}/releases/latest");
        if (is_wp_error($response)) {
            return $update; // Exit if error
        }

        $release = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($release['tag_name'])) {
            $latest_version = ltrim($release['tag_name'], 'v');

            // Check for a .zip file in the assets
            $download_url = null;
            if (!empty($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    if (isset($asset['browser_download_url']) && str_ends_with($asset['browser_download_url'], '.zip')) {
                        $download_url = $asset['browser_download_url'];
                        break; // Stop searching once a valid .zip file is found
                    }
                }
            }

            // Only return update information if a valid .zip file is found
            if ($download_url) {
                return array(
                    'slug'    => $this->slug,
                    'version' => $latest_version,
                    'url'     => $release['html_url'],
                    'package' => $download_url,
                );
            }
        }

        return $update; // Return unchanged if no valid .zip file is found
    }
}
