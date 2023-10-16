<?php
/*
Plugin Name: WP Job Listing Integration
Description: Ein Plugin zum Hochladen von Stellenanzeigen als HTML/CSS und zur automatischen Beitragserstellung.
Version: 1.0
Author: Niko
*/

// Admin-Menü hinzufügen
add_action('admin_menu', 'wpjli_add_admin_menu');
function wpjli_add_admin_menu()
{
  add_menu_page('Job Listing Upload', 'Job Listing Upload', 'manage_options', 'wp-job-listing-upload', 'wpjli_upload_page', 'dashicons-upload', 6);
}

// Upload-Seite rendern
function wpjli_upload_page()
{
  include(plugin_dir_path(__FILE__) . 'includes/upload-handler.php');
}

// Enqueue Admin CSS
add_action('admin_enqueue_scripts', 'wpjli_enqueue_admin_styles');
function wpjli_enqueue_admin_styles()
{
  wp_enqueue_style('wpjli-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
}

function wpjli_upload_form_shortcode()
{
  // Überprüfen, ob der Benutzer eingeloggt ist
  if (!is_user_logged_in()) {
    return 'Bitte melden Sie sich an, um eine Stellenanzeige hochzuladen.';
  }

  ob_start(); // Starte die Ausgabepufferung

  // Ihr Formularcode hier
?>
  <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="wpjli_handle_upload">
    <?php wp_nonce_field('wpjli_upload_nonce', 'wpjli_nonce'); ?>
    <label for="jobListing">Wählen Sie die Dateien zum Hochladen aus:</label>
    <input type="file" name="jobListing" id="jobListing">
    <input type="submit" value="Hochladen">
  </form>
<?php

  return ob_get_clean(); // Gib den gepufferten Inhalt zurück
}
add_shortcode('wpjli_upload_form', 'wpjli_upload_form_shortcode');
