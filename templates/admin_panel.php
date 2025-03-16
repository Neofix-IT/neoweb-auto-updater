<?php


if (! defined('ABSPATH')) {
    exit;
}

?>

<div id="neoweb-updater">
    <div class="controlpanel">
        <a class="button start-update">
            Update starten
        </a>
        <a class="button clear-log">
            Logs Löschen
        </a>
        <a class="button update-log">
            Logs neu laden
        </a>
        <p class="start-update result"></p>
    </div>
    <div class="log content">
        <p>
            <?php
            $log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
            if (file_exists($log_file)) {
                echo nl2br(esc_html(file_get_contents($log_file)));
                echo '<p>Log Ende</p>';
            } else {
                echo 'Logfile nicht gefunden';
            }
            ?>
        </p>
    </div>
</div>

<style>
    #neoweb-updater div {
        display: block;
        box-sizing: border-box;
    }

    #neoweb-updater {
        margin: 20px 20px 20px 0;
        height: calc(100vh - var(--wp-admin--admin-bar--height) - 155px);
    }

    #neoweb-updater .controlpanel {
        margin-bottom: 10px;
        min-height: 75px;
    }

    #neoweb-updater .button {
        padding: 5px 20px;
        background-color: #164aa0;
        color: white;
        border-radius: 2px;
        margin-right: 10px;
    }

    .log.content {
        background-color: lightgrey;
        padding: 10px;
        width: 100%;
        max-height: 100%;
        overflow-y: scroll;
    }
</style>
<script>
    document.querySelector(".button.clear-log").addEventListener("click", function(e) {
        const apiUrl = '<?php echo get_rest_url() . 'neoweb/v1/clear-log'; ?>';
        const logContainer = document.querySelector('.log.content p');
        const requestHeaders = {
            "X-WP-Nonce": "<?php echo wp_create_nonce('wp_rest'); ?>"
        };

        fetch(apiUrl, {
                headers: requestHeaders,
            })
            .then(response => {
                if (!response.ok) {
                    return "Fehler bei der Abfrage";
                }
                return response.json();
            })
            .then(data => {
                // Display data in an HTML element
                logContainer.textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
    document.querySelector(".button.start-update").addEventListener("click", function(e) {
        const apiUrl = '<?php echo get_rest_url() . 'neoweb/v1/run-update'; ?>';
        const outputElement = document.querySelector('.start-update.result');
        const requestHeaders = {
            "X-WP-Nonce": "<?php echo wp_create_nonce('wp_rest'); ?>"
        };

        fetch(apiUrl, {
                headers: requestHeaders,
            })
            .then(response => {
                if (!response.ok) {
                    return "Fehler bei der Abfrage";
                }
                return response.json();
            })
            .then(data => {
                // Display data in an HTML element
                outputElement.textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
    document.querySelector(".button.update-log").addEventListener("click", function(e) {
        location.reload();
    });
</script>