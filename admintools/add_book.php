<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite, falls nicht
    exit();
}
require '../db.php'; // Einbindung der Datenbankverbindung

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Holt die Formulardaten
    $title = $_POST['Title']; // Titel des Buches
    $autor = $_POST['autor']; // Autor des Buches
    $kategorie = $_POST['kategorie']; // Kategorie des Buches
    $verkauft = $_POST['verkauft']; // Verkaufsstatus des Buches
    $kaeufer = $_POST['kaufer']; // Käufer des Buches (optional)

    // Fehlerbehandlung: Überprüft, ob erforderliche Felder ausgefüllt sind
    if (empty($title) || empty($autor) || empty($kategorie)) {
        die("Bitte füllen Sie alle erforderlichen Felder aus."); // Fehlermeldung
    }

    // Bereitet die SQL-Abfrage zum Einfügen eines neuen Buches vor
    $stmt = $con->prepare("INSERT INTO buecher (Title, autor, kategorie, verkauft, kaufer) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        // Fehlerbehandlung, falls die Vorbereitung der Abfrage fehlschlägt
        die("Fehler bei der Vorbereitung der Abfrage: " . $con->error);
    }

    // Bindet die Parameter an die SQL-Abfrage
    $stmt->bind_param("ssiii", $title, $autor, $kategorie, $verkauft, $kaeufer);
    if (!$stmt->execute()) {
        // Fehlerbehandlung, falls die Ausführung der Abfrage fehlschlägt
        die("Fehler beim Ausführen der Abfrage: " . $stmt->error);
    }

    $stmt->close(); // Schließt das Statement
    header("Location: books.php"); // Weiterleitung zur Bücher-Seite nach erfolgreichem Einfügen
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Neues Buch hinzufügen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">ADMIN</a>
        </div>
    </nav>
</header>

<div class="container mt-5">
    <h2 class="mb-4">Neues Buch hinzufügen</h2>
    <form method="post" action="add_book.php">
        <!-- Formularfelder für die Buchdaten -->
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input type="text" class="form-control" name="Title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" class="form-control" name="autor" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategorie</label>
            <input type="text" class="form-control" name="kategorie" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Verkauft</label>
            <input type="number" class="form-control" name="verkauft" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Käufer</label>
            <input type="number" class="form-control" name="kaufer">
        </div>
        <button type="submit" class="btn btn-success">Speichern</button>
        <a href="books.php" class="btn btn-secondary">Abbrechen</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
