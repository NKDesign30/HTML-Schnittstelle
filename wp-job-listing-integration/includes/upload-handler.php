<?php
// Dies ist nur ein einfacher Prototyp für den Upload-Handler. 
// Es müssen weitere Sicherheitsüberprüfungen und Validierungen hinzugefügt werden.

if (isset($_POST['submit'])) {
  // Datei-Upload-Logik hier...
  // Nach erfolgreichem Upload, rufen Sie den post-creator.php auf, um den Beitrag zu erstellen.
}

?>

<h2>Stellenanzeige hochladen</h2>
<form action="" method="post" enctype="multipart/form-data">
  Wählen Sie die Dateien zum Hochladen aus:
  <label for="jobListing">Wählen Sie die .zip-Datei zum Hochladen aus:</label>
  <input type="file" name="jobListing" id="jobListing" accept=".zip">
</form>
<?php
if (isset($_POST['submit']) && isset($_FILES['jobListing'])) {
  $uploadedFile = $_FILES['jobListing'];

  // Überprüfen, ob es sich um eine .zip-Datei handelt
  if ($uploadedFile['type'] == 'application/zip') {
    $zip = new ZipArchive;
    $res = $zip->open($uploadedFile['tmp_name']);
    if ($res === TRUE) {
      // Entpacken Sie das Archiv in ein temporäres Verzeichnis
      $extractPath = "/path/to/temp/directory";
      $zip->extractTo($extractPath);
      $zip->close();

      // Überprüfen Sie, ob die erforderlichen Dateien vorhanden sind
      if (file_exists($extractPath . '/index.html') && file_exists($extractPath . '/style.css')) {
        // Verarbeiten Sie die Dateien wie gewünscht...
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
