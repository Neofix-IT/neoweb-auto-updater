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
			'Neoweb AutoUpdater',
			'update_core',
			'auto-updater', // hook/slug of page
			'neoweb_auto_updater_render', // function to render page
			'dashicons-welcome-widgets-menus',
			30,
		);
	}
	add_action( 'admin_menu', 'neoweb_updater_adminpanel' );
	 
	
	function neoweb_auto_updater_render() {
		?>
		<div style="display: block; box-sizing: border-box; margin: 20px 20px 20px 0;">
			<a id="start-test" style="display: block; cursor: pointer; padding: 10px 30px; background-color: green; color: white;">Update starten</a>
			<p id="output"></p>
		</div>
		<script>
			document.querySelector("#start-test").addEventListener("click", function (e){
				const apiUrl = '<?php echo get_rest_url() . 'neoweb/v1/run-update'; ?>';
				const outputElement = document.getElementById('output');
	
				fetch(apiUrl)
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
		</script>
		<?php
	}
	
	
	add_action( 'rest_api_init', function () {
		register_rest_route( 'neoweb/v1', '/run-update', array(
		  'methods' => 'GET',
		  'callback' => 'neoweb_run_update',
		) );
	  } );
	
	function neoweb_run_update(){
		try{
			include_once( ABSPATH . '/wp-admin/includes/admin.php' );
			include_once( ABSPATH . '/wp-admin/includes/class-wp-upgrader.php' );
		
			$upgrader = new WP_Automatic_Updater;
			$upgrader->run();
		} catch (Exception $e) {
			return "Update gescheitert";	
		}
		return "Update gestartet";
	}
}

?>