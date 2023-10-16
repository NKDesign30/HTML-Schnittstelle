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

    // Überprüfen, ob es sich um eine .zip-Datei handelt
    if ($uploadedFile['type'] == 'application/zip') {
      $zip = new ZipArchive;
      $res = $zip->open($uploadedFile['tmp_name']);
      if ($res === TRUE) {
        // Bestimmen Sie den Pfad zum Upload-Verzeichnis
        $upload_dir = wp_upload_dir();
        $base_upload_path = $upload_dir['basedir'];

        // Fügen Sie den Unterordner 'HTMLupload' hinzu
        $extractPath = $base_upload_path . '/HTMLupload';

        // Stellen Sie sicher, dass das Verzeichnis existiert (wenn nicht, erstellen Sie es)
        if (!file_exists($extractPath)) {
          mkdir($extractPath, 0755, true);
        }

        // Entpacken Sie das Archiv in das festgelegte Verzeichnis
        $zip->extractTo($extractPath);
        $zip->close();

        // Diagnosecode: Überprüfen Sie den Inhalt des entpackten Verzeichnisses
        $files = scandir($extractPath);
        var_dump($files); // Dies wird den Inhalt des Verzeichnisses ausgeben


        // Überprüfen Sie, ob die erforderlichen Dateien vorhanden sind
        if (file_exists($extractPath . '/index.html') && file_exists($extractPath . '/style.css')) {
          // Hier können Sie den Inhalt von index.html und style.css verarbeiten und in einen WordPress-Beitrag konvertieren
          $output .= "Dateien erfolgreich hochgeladen und verarbeitet!";
        } else {
          $output .= "Die erforderlichen Dateien fehlen im Archiv.";
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
