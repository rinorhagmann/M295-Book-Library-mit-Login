<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite, falls nicht
    exit();
}
require '../db.php'; // Einbindung der Datenbankverbindung

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    // Überprüft, ob das Formular per POST gesendet wurde und eine ID vorhanden ist
    $id = $_POST['id']; // ID des Buches
    $title = $_POST['title']; // Titel des Buches
    $autor = $_POST['autor']; // Autor des Buches
    $kategorie = $_POST['kategorie']; // Kategorie des Buches
    $verkauft = $_POST['verkauft']; // Verkaufsstatus des Buches
    $kaeufer = $_POST['kaeufer']; // Käufer des Buches

    // Bereitet eine SQL-Anweisung vor, um die Buchdaten zu aktualisieren
    $stmt = $con->prepare("UPDATE buecher SET Title=?, autor=?, kategorie=?, verkauft=?, kaufer=? WHERE id=?");
    if (!$stmt) {
        // Fehlerbehandlung, falls die Vorbereitung der Abfrage fehlschlägt
        die("Fehler bei der Vorbereitung der Abfrage: " . $con->error);
    }

    // Bindet die Parameter an die SQL-Anweisung
    $stmt->bind_param("sssiii", $title, $autor, $kategorie, $verkauft, $kaeufer, $id);
    if (!$stmt->execute()) {
        // Fehlerbehandlung, falls die Ausführung der Abfrage fehlschlägt
        die("Fehler beim Ausführen der Abfrage: " . $stmt->error);
    }

    $stmt->close(); // Schließt das Statement
    header("Location: books.php"); // Weiterleitung zur Bücher-Seite
    exit();
} elseif (isset($_GET['id'])) {
    // Überprüft, ob eine ID über GET übergeben wurde
    $id = $_GET['id']; // ID des Buches
    // Bereitet eine SQL-Anweisung vor, um die Buchdaten abzurufen
    $stmt = $con->prepare("SELECT * FROM buecher WHERE id=?");
    if (!$stmt) {
        // Fehlerbehandlung, falls die Vorbereitung der Abfrage fehlschlägt
        die("Fehler bei der Vorbereitung der Abfrage: " . $con->error);
    }

    $stmt->bind_param("i", $id); // Bindet die ID als Parameter
    $stmt->execute(); // Führt die Abfrage aus
    $result = $stmt->get_result(); // Holt das Ergebnis der Abfrage
    $book = $result->fetch_assoc(); // Holt die Buchdaten als assoziatives Array
    $stmt->close(); // Schließt das Statement
} else {
    // Weiterleitung zur Bücher-Seite, falls keine gültige Anfrage vorliegt
    header("Location: books.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buch bearbeiten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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
    <h2 class="mb-4">Buch bearbeiten</h2>
    <form method="post" action="edit_book.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']); ?>">
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($book['Title']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Autor</label>
            <input type="text" class="form-control" name="autor" value="<?php echo htmlspecialchars($book['autor']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategorie</label>
            <input type="text" class="form-control" name="kategorie" value="<?php echo htmlspecialchars($book['kategorie']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Verkauft</label>
            <input type="number" class="form-control" name="verkauft" value="<?php echo htmlspecialchars($book['verkauft']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Käufer</label>
            <input type="number" class="form-control" name="kaeufer" value="<?php echo htmlspecialchars($book['kaufer']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Speichern</button>
        <a href="books.php" class="btn btn-secondary">Abbrechen</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
