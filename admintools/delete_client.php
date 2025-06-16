<?php
session_start();
// Überprüfen, ob der Nutzer eingeloggt ist und Admin-Rechte hat
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

require '../db.php'; // Einbindung der Datenbankverbindung

// Überprüfen, ob eine ID übergeben wurde
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $kid = $_GET['id'];

    // Löschen des Kunden aus der Datenbank
    $stmt = $con->prepare("DELETE FROM kunden WHERE kid = ?");
    $stmt->bind_param("i", $kid);

    if ($stmt->execute()) {
        // Wenn Erfolgreiches Löschen, Weiterleitung zur Kundenverwaltung
        header("Location: clients.php");
        exit();
    } else {
        // Fehlerausgabe beim Löschen
        echo "<script>alert('Fehler beim Löschen des Kunden.');</script>";
        header("Location: clients.php");
        exit();
    }
} else {
    // Falls keine gültige ID übergeben wurde
    echo "<script>alert('Ungültige Anfrage!');</script>";
    header("Location: clients.php");
    exit();
}
