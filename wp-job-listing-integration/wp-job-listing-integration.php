<?php
/*
Plugin Name: WP Job Listing Integration
Description: Ein Plugin zum Hochladen von Stellenanzeigen als HTML/CSS und zur automatischen Beitragserstellung.
Version: 1.0
Author: Niko
*/

// Admin-Menü hinzufügen
add_action('admin_menu', 'wpjli_add_admin_menu');
function wpjli_add_admin_menu() {
    add_menu_page('Job Listing Upload', 'Job Listing Upload', 'manage_options', 'wp-job-listing-upload', 'wpjli_upload_page', 'dashicons-upload', 6);
}

// Upload-Seite rendern
function wpjli_upload_page() {
    include(plugin_dir_path(__FILE__) . 'includes/upload-handler.php');
}

// Enqueue Admin CSS
add_action('admin_enqueue_scripts', 'wpjli_enqueue_admin_styles');
function wpjli_enqueue_admin_styles() {
    wp_enqueue_style('wpjli-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
}

// Weitere Funktionen und Handler können hier hinzugefügt werden...
