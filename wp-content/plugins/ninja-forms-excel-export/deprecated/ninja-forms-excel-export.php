<?php

define( 'HAET_NINJAFORMSSPREADSHEET_PATH', plugin_dir_path(__FILE__) );
define( 'HAET_NINJAFORMSSPREADSHEET_URL', plugin_dir_url(__FILE__) );

require HAET_NINJAFORMSSPREADSHEET_PATH . 'includes/class-ninjaformsspreadsheet.php';
load_plugin_textdomain('ninja-forms-spreadsheet', false, HAET_NINJAFORMSSPREADSHEET_PATH . '/translations' );




if (class_exists("NinjaFormsSpreadsheet")) {
    $wp_ninjaformsspreadsheet = new NinjaFormsSpreadsheet();
}

//Actions and Filters	
if (isset($wp_ninjaformsspreadsheet)) {
    add_action( 'admin_menu', array(&$wp_ninjaformsspreadsheet, 'admin_page'), 20);
    add_action( 'admin_init', array(&$wp_ninjaformsspreadsheet, 'admin_metaboxes'));
    add_action( 'admin_enqueue_scripts', array(&$wp_ninjaformsspreadsheet, 'admin_page_scripts_and_styles'));
}

function nf_spreadsheet_setup_license() {
	if ( class_exists( 'NF_Extension_Updater' ) ) {
        $NF_Extension_Updater = new NF_Extension_Updater( 'Excel Export', '3.1', 'Hannes Etzelstorfer', __FILE__ );
	}
}

add_action( 'admin_init', 'nf_spreadsheet_setup_license' );

?>