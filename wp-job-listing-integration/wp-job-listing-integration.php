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

// Weitere Funktionen und Handler können hier hinzugefügt werden...
function wpjli_upload_form_shortcode()
{
  // Überprüfen, ob der Benutzer eingeloggt ist
  if (!is_user_logged_in()) {
    return 'Bitte melden Sie sich an, um eine Stellenanzeige hochzuladen.';
  }

  $output = ''; // Variable für die Ausgabe

  // Wenn das Formular abgesendet wurde
  if (isset($_POST['submit']) && isset($_FILES['jobListing']) && wp_verify_nonce($_POST['_wpnonce'], 'wpjli_upload_nonce')) {

    $uploadedFile = $_FILES['jobListing'];

    // Überprüfen Sie die Dateierweiterung
    $file_extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
    if (strtolower($file_extension) == 'zip') {

      $zip = new ZipArchive;
      $res = $zip->open($uploadedFile['tmp_name']);
      if ($res === TRUE) {
        // Bestimmen Sie den Pfad zum Upload-Verzeichnis
        $upload_dir = wp_upload_dir();
        $base_upload_path = $upload_dir['basedir'];

        // Fügen Sie den Unterordner 'HTMLupload' hinzu
        $extractPath = $base_upload_path . '/HTMLupload';

        // Entpacken Sie das Archiv in das festgelegte Verzeichnis
        $zip->extractTo($extractPath);
        $zip->close();

        // Diagnosecode: Überprüfen Sie den Inhalt des entpackten Verzeichnisses
        $files = scandir($extractPath);
        $extracted_folder = null;

        // Finden Sie den richtigen Unterordner (ignorieren Sie "._" Dateien und andere Systemdateien)
        foreach ($files as $file) {
          if ($file !== '.' && $file !== '..' && !preg_match('/^._/', $file)) {
            $extracted_folder = $file;
            break;
          }
        }

        if ($extracted_folder) {
          // Überprüfen Sie, ob die erforderlichen Dateien vorhanden sind
          if (file_exists($extractPath . '/' . $extracted_folder . '/index.html') && file_exists($extractPath . '/' . $extracted_folder . '/style.css')) {

            // Lese den Inhalt von index.html
            $html_content = file_get_contents($extractPath . '/' . $extracted_folder . '/index.html');

            // Extrahiere den Titel aus index.html
            preg_match('/<title>([^<]+)<\/title>/', $html_content, $matches);
            $post_title = isset($matches[1]) ? $matches[1] : 'Neue Stellenanzeige'; // Verwenden Sie den extrahierten Titel oder einen Standardtitel

            // Lese den Inhalt von style.css
            $css_content = '<style>' . file_get_contents($extractPath . '/' . $extracted_folder . '/style.css') . '</style>';

            // Erstelle einen neuen WordPress-Beitrag
            $post_id = wp_insert_post(array(
              'post_title'    => $post_title,
              'post_content'  => $css_content . $html_content,
              'post_status'   => 'publish',
              'post_author'   => get_current_user_id(),
              'post_type'     => 'job_listing'
            ));

            if ($post_id) {
              $output .= "Stellenanzeige erfolgreich erstellt!";
            } else {
              $output .= "Fehler beim Erstellen der Stellenanzeige.";
            }
          } else {
            $output .= "Die erforderlichen Dateien fehlen im Archiv.";
          }
        } else {
          $output .= "Es wurde kein gültiger Unterordner im Archiv gefunden.";
        }
      } else {
        $output .= "Fehler beim Öffnen des .zip-Archivs.";
      }
    } else {
      $output .= "Bitte laden Sie nur .zip-Dateien hoch.";
    }
  }

  ob_start(); // Starte die Ausgabepufferung

  // Ihr Formularcode hier
  $output .= '
  <form action="" method="post" enctype="multipart/form-data">
      ' . wp_nonce_field('wpjli_upload_nonce', '_wpnonce', true, false) . '
      <label for="jobListing">Wählen Sie die Dateien zum Hochladen aus:</label>
      <input type="file" name="jobListing" id="jobListing" accept=".zip">
      <input type="submit" name="submit" value="Hochladen">
  </form>';

  return $output; // Gib den generierten Inhalt zurück
}
add_shortcode('wpjli_upload_form', 'wpjli_upload_form_shortcode');
