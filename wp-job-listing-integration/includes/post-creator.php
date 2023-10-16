<?php
// Dies ist nur ein einfacher Prototyp für den Beitragsersteller. 
// Es müssen weitere Logiken hinzugefügt werden, um den Inhalt und das Styling korrekt zu integrieren.

// Beispielcode zum Erstellen eines Beitrags:
$post_data = array(
  'post_title'    => 'Neue Stellenanzeige',
  'post_content'  => 'Hier kommt der Inhalt von index.html',
  'post_status'   => 'draft',
  'post_type'     => 'post',
  'post_author'   => 1,
);

$post_id = wp_insert_post($post_data);
