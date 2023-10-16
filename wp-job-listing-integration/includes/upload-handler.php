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
  <input type="file" name="jobListing" id="jobListing">
  <input type="submit" value="Hochladen" name="submit">
</form>