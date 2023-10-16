<?php

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

      // Überprüfen Sie, ob die erforderlichen Dateien vorhanden sind
      if (file_exists($extractPath . '/index.html') && file_exists($extractPath . '/style.css')) {
        // Verarbeiten Sie die Dateien wie gewünscht...
        // Zum Beispiel: Erstellen Sie einen WordPress-Beitrag basierend auf dem Inhalt von index.html und style.css
      } else {
        echo "Die erforderlichen Dateien fehlen im Archiv.";
      }
    } else {
      echo "Fehler beim Öffnen des .zip-Archivs.";
    }
  } else {
    echo "Bitte laden Sie nur .zip-Dateien hoch.";
  }
}

?>

<h2>Stellenanzeige hochladen</h2>
<form action="" method="post" enctype="multipart/form-data">
  <?php wp_nonce_field('wpjli_upload_nonce'); ?>
  Wählen Sie die Dateien zum Hochladen aus:
  <label for="jobListing">Wählen Sie die .zip-Datei zum Hochladen aus:</label>
  <input type="file" name="jobListing" id="jobListing" accept=".zip">
  <input type="submit" name="submit" value="Hochladen">
</form>