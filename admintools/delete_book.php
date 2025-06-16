<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) { 
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite, falls nicht
    exit();
}
require '../db.php'; // Einbindung der Datenbankverbindung

if (isset($_GET['id'])) { // Überprüft, ob eine ID übergeben wurde
    $id = $_GET['id']; // Speichert die übergebene ID

    // Bereitet eine SQL-Anweisung vor, um das Buch mit der angegebenen ID zu löschen
    $stmt = $con->prepare("DELETE FROM buecher WHERE id = ?");
    $stmt->bind_param("i", $id); // Bindet die ID als Integer-Parameter
    
    if ($stmt->execute()) { 
        // Überprüft, ob die SQL-Anweisung erfolgreich ausgeführt wurde
        $_SESSION['message'] = "Buch erfolgreich gelöscht."; // Erfolgsnachricht
    } else {
        $_SESSION['error'] = "Fehler beim Löschen des Buches."; // Fehlermeldung
    }

    $stmt->close(); // Schliesst das Statement
    header("Location: books.php"); // Weiterleitung zur Bücher-Seite
    exit();
} else {
    $_SESSION['error'] = "Ungültige Anfrage."; // Fehlermeldung bei ungültiger Anfrage
    header("Location: books.php"); // Weiterleitung zur Bücher-Seite
    exit();
}
?>
