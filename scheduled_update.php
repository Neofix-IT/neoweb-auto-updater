<?php

require_once "logger.php";

$neoweb_enable_update_override = false;

class NeowebUpdateScheduler{

    // Scheduled time of the update (Using WP-timezone)
    const SCHEDULED_TIME = "02:00";

}

$neoWebUpdater = new NeowebUpdateScheduler;

?>