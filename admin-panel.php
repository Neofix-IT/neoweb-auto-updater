<?php

/*
 * Neoweb AutoUpdater adminpanel
 * 
 * Allowing
 * 
*/ 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( defined( 'NEOWEB_UPDATER_ADMINPANEL_VISIBLE' ) && NEOWEB_UPDATER_ADMINPANEL_VISIBLE ){
	function neoweb_updater_adminpanel() {
		global $submenu;
	
		add_menu_page(
			'Neoweb AutoUpdater',
			'AutoUpdater',
			'update_core',
			'auto-updater', // hook/slug of page
			'neoweb_auto_updater_render', // function to render page
			'dashicons-update',
			30,
		);
	}
	add_action( 'admin_menu', 'neoweb_updater_adminpanel' );

	function neoweb_auto_updater_render(){
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
					if( file_exists($log_file) ){
						echo nl2br( esc_html( file_get_contents($log_file) ) );
						echo '<p>Log Ende</p>';
					} else {
						echo 'Logfile nicht gefunden';
					}
				?>
				</p>
			</div>
		</div>

		<style>
			#neoweb-updater div{
				display: block;
				box-sizing: border-box;
			}
			#neoweb-updater{
				margin: 20px 20px 20px 0;
			}
			#neoweb-updater .controlpanel{
				margin-bottom: 10px;
			}
			#neoweb-updater .button{
				padding: 5px 20px;
				background-color: #164aa0;
				color: white;
				border-radius: 2px;
				margin-right: 10px;
			}
			.log{
				background-color: lightgrey;
				padding: 10px;
				width: 100%;
			}
		</style>
		<script>
			document.querySelector(".button.clear-log").addEventListener("click", function (e){
				const apiUrl = '<?php echo get_rest_url() . 'neoweb/v1/clear-log'; ?>';
				const logContainer = document.querySelector('.log.content p');
				const requestHeaders = {"X-WP-Nonce": "<?php echo wp_create_nonce( 'wp_rest' ); ?>"};
	
				fetch(apiUrl, {headers: requestHeaders,})
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
			document.querySelector(".button.start-update").addEventListener("click", function (e){
				const apiUrl = '<?php echo get_rest_url() . 'neoweb/v1/run-update'; ?>';
				const outputElement = document.querySelector('.start-update.result');
				const requestHeaders = {"X-WP-Nonce": "<?php echo wp_create_nonce( 'wp_rest' ); ?>"};
	
				fetch(apiUrl, {headers: requestHeaders,})
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
			document.querySelector(".button.update-log").addEventListener("click", function (e){
				location.reload();
			});
		</script>
		<?php
	}
	
	add_action( 'rest_api_init', function () {
		register_rest_route( 'neoweb/v1', '/run-update', array(
		  'methods' => 'GET',
		  'callback' => 'neoweb_run_update',
		  'permission_callback' => 'has_updater_permission',
		) );
	  } );
	
	function neoweb_run_update(){
		require_once 'logger.php';

		global $neoweb_enable_update_override;
		neoweb_log("Start auto-update via Adminpanel");
		try{
			$neoweb_enable_update_override = true;
			include_once( ABSPATH . '/wp-includes/update.php' );
			wp_maybe_auto_update();
		} catch (Exception $e) {
			return "Update gescheitert";
		}
		neoweb_log('resetting update override for manual update using adminpanel');
		$neoweb_enable_update_override = false;
		return "Update gestartet";
	}

	add_action( 'rest_api_init', function () {
		register_rest_route( 'neoweb/v1', '/clear-log', array(
		  'methods' => 'GET',
		  'callback' => 'neoweb_clear_log',
		  'permission_callback' => 'has_updater_permission',
		) );
	  } );
	
	function neoweb_clear_log(){
		try{
			$log_file = WP_CONTENT_DIR . "/neoweb_autoupdater.log";
			if (unlink($log_file)){
				return "Logs gelöscht";
			}
		} catch (Exception $e) {}

		return 'Unerwarteter Fehler beim Löschen der Logs';
	}

	function has_updater_permission(){
        return current_user_can('update_core');
		return current_user_can('update_plugins');
		return current_user_can('update_themes');
    }
}
?>