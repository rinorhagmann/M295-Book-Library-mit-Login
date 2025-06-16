<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite, falls nicht
    exit();
}

require '../db.php'; // Einbindung der Datenbankverbindung

// Prüfen, ob eine ID übergeben wurde
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $kid = $_GET['id']; // Speichert die übergebene Kunden-ID

    // Holen der Kundendaten aus der Datenbank
    $stmt = $con->prepare("SELECT * FROM kunden WHERE kid = ?");
    $stmt->bind_param("i", $kid); // Bindet die Kunden-ID als Parameter
    $stmt->execute(); // Führt die Abfrage aus
    $result = $stmt->get_result(); // Holt das Ergebnis der Abfrage

    // Falls der Kunde nicht existiert
    if ($result->num_rows === 0) {
        echo "<script>alert('Kunde nicht gefunden!');</script>"; // Fehlermeldung
        header("Location: clients.php"); // Weiterleitung zur Kunden-Seite
        exit();
    }
    $row = $result->fetch_assoc(); // Speichert die Kundendaten als assoziatives Array
} else {
    echo "<script>alert('Ungültige Anfrage!');</script>"; // Fehlermeldung bei ungültiger Anfrage
    header("Location: clients.php"); // Weiterleitung zur Kunden-Seite
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kundeninformationen aus dem Formular
    $vorname = $_POST['vorname']; // Vorname des Kunden
    $name = $_POST['name']; // Nachname des Kunden
    $geburtstag = $_POST['geburtstag']; // Geburtstag des Kunden
    $geschlecht = $_POST['geschlecht']; // Geschlecht des Kunden
    $kunde_seit = $_POST['kunde_seit']; // Datum, seit wann der Kunde registriert ist
    $email = $_POST['email']; // E-Mail-Adresse des Kunden

    // Überprüfen, ob die E-Mail bereits existiert (außer für den aktuellen Kunden)
    $stmt = $con->prepare("SELECT * FROM kunden WHERE email = ? AND kid != ?");
    $stmt->bind_param("si", $email, $kid); // Bindet die E-Mail und Kunden-ID als Parameter
    $stmt->execute(); // Führt die Abfrage aus
    $result = $stmt->get_result(); // Holt das Ergebnis der Abfrage

    if ($result->num_rows > 0) {
        // Fehlermeldung, falls die E-Mail bereits existiert
        echo "<script>alert('Ein Kunde mit dieser E-Mail existiert bereits.');</script>";
    } else {
        // Aktualisieren der Kundendaten in der Datenbank
        $stmt = $con->prepare("UPDATE kunden SET vorname = ?, name = ?, geburtstag = ?, geschlecht = ?, kunde_seit = ?, email = ? WHERE kid = ?");
        $stmt->bind_param("ssssssi", $vorname, $name, $geburtstag, $geschlecht, $kunde_seit, $email, $kid);

        if ($stmt->execute()) {
            // Weiterleitung zur Kunden-Seite nach erfolgreicher Aktualisierung
            header("Location: clients.php");
            exit();
        } else {
            // Fehlermeldung bei Fehler während der Aktualisierung
            echo "<script>alert('Fehler beim Aktualisieren des Kunden.');</script>";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kunden bearbeiten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
<nav class="navbar navbar-expand-md navbar-dark bg-dark" style="position: fixed; width: 100%; z-index: 1000; top: 0">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../loggedin/index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="container mt-5">
    <h2 class="mb-4"><br>Kunden bearbeiten</h2>
    <form action="edit_client.php?id=<?= $kid ?>" method="POST">
        <div class="mb-3">
            <label for="vorname" class="form-label">Vorname</label>
            <input type="text" class="form-control" id="vorname" name="vorname" value="<?= htmlspecialchars($row['vorname']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Nachname</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="geburtstag" class="form-label">Geburtstag</label>
            <input type="date" class="form-control" id="geburtstag" name="geburtstag" value="<?= htmlspecialchars($row['geburtstag']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="geschlecht" class="form-label">Geschlecht</label>
            <select class="form-select" id="geschlecht" name="geschlecht" required>
                <option value="M" <?= $row['geschlecht'] === 'M' ? 'selected' : '' ?>>Männlich</option>
                <option value="F" <?= $row['geschlecht'] === 'F' ? 'selected' : '' ?>>Weiblich</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="kunde_seit" class="form-label">Kunde seit</label>
            <input type="date" class="form-control" id="kunde_seit" name="kunde_seit" value="<?= htmlspecialchars($row['kunde_seit']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        <a href="clients.php" class="btn btn-secondary">Abbrechen</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
