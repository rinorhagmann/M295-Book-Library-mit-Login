<?php
session_start();
// Überprüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require '../db.php'; // Datenbankverbindung
$admin = $_SESSION['admin'] ?? false; // Überprüfung, ob der Benutzer ein Administrator ist

$verkauft = [
    0 => 'Nein',
    1 => 'Ja'
]; // Status für verkauft/nicht verkauft

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { // Überprüfung der ID
    die("Ungültige Buch-ID."); // Wenn keine ID vorhanden ist oder die ID keine Zahl ist, wird eine Fehlermeldung ausgegeben.
}

$id = (int)$_GET['id']; // ID aus der URL holen und in eine Ganzzahl umwandeln

// MySQLi-Datenbankabfrage
$con = new mysqli($host, $user, $pass, $db); // Verbindung zur Datenbank herstellen

if ($con->connect_error) { // Überprüfung auf Verbindungsfehler
    die("Verbindung zur Datenbank fehlgeschlagen: " . $con->connect_error); // Wenn die Verbindung fehlschlägt, wird eine Fehlermeldung ausgegeben.
}

$sql = "SELECT * FROM buecher WHERE id = ?"; // SQL-Abfrage vorbereiten
$stmt = $con->prepare($sql); // SQL-Abfrage vorbereiten
$stmt->bind_param("i", $id); // Parameter binden (i = integer)
$stmt->execute(); // SQL-Abfrage ausführen
$result = $stmt->get_result(); // Ergebnis der Abfrage holen
$buch = $result->fetch_assoc(); // Ergebnis als assoziatives Array holen

if (!$buch) { // Überprüfung, ob ein Buch gefunden wurde
    die("Buch nicht gefunden."); // Wenn kein Buch gefunden wurde, wird eine Fehlermeldung ausgegeben.
}

// Kategorien aus der Datenbank laden
$kategorien = [];
$kategorienResult = $con->query("SELECT id, kategorie FROM kategorien"); // Spaltenname angepasst
while ($row = $kategorienResult->fetch_assoc()) {
    $kategorien[(int)$row['id']] = $row['kategorie']; // Spaltenname angepasst
}
$kategorieText = $kategorien[$buch['kategorie']] ?? "Unbekannte Kategorie"; // Kategorie des Buches ausgeben, wenn vorhanden, sonst "Unbekannte Kategorie"

// Zustände aus der Datenbank laden
$zustaende = [];
$zustaendeResult = $con->query("SELECT zustand, beschreibung FROM zustaende"); // Spaltenname angepasst
while ($row = $zustaendeResult->fetch_assoc()) {
    $zustaende[$row['zustand']] = $row['beschreibung']; // Spaltenname angepasst
}
$zustandText = $zustaende[$buch['zustand']] ?? "Unbekannter Zustand"; // Zustand des Buches ausgeben, wenn vorhanden, sonst "Unbekannter Zustand"

$stmt->close(); // Vorbereitetes Statement schliessen
$con->close(); // Verbindung zur Datenbank schliessen
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buch['Title'] ?? 'Unbekannter Titel') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png" type="image/png">
</head>
<body>
<header>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Bücher-Antiquariat</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active" href="buecher.php">Bücher</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($admin == 1) ? '' : 'disabled'; ?>" href="<?php echo ($admin == 1) ? '../admintools/dashboard.php' : '#'; ?>">Admin</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
    <div class="container mt-5">
    <h1><?= isset($buch['Title']) && $buch['Title'] !== null && $buch['Title'] !== '' ? htmlspecialchars($buch['Title']) : 'Unbekannt' ?></h1><br>
        <h4><?= htmlspecialchars($buch['Beschreibung'] ?? 'Keine Beschreibung verfügbar') ?></h4><br>
        <p><strong>Nummer:</strong> <?= htmlspecialchars($buch['nummer'] ?? 'Unbekannt' ) ?></p>
        <p><strong>Autor:</strong> <?= htmlspecialchars($buch['autor'] ?? 'Unbekannt') ?></p>
        <p><strong>Kategorie:</strong> <?= htmlspecialchars($kategorieText ?? 'Unbekannte Kategorie') ?></p>
        <p><strong>Verfasser:</strong> <?= htmlspecialchars($buch['verfasser'] ?? 'Unbekannt') ?></p>
        <p><strong>Verkauft:</strong> <?= $verkauft[$buch['verkauft']] ?? 'Unbekannt' ?></p>
        <p><strong>Käufer:</strong> <?= $buch['kaufer'] ?? 'Unbekannt' ?></p>
        <p><strong>Katalog:</strong> <?= htmlspecialchars($buch['katalog'] ?? 'Unbekannt') ?></p>
        <p><strong>Zustand:</strong> <?= htmlspecialchars($zustandText ?? 'Unbekannter Zustand') ?></p><br>
        <a href="buecher.php" class="btn btn-secondary">Zurück zur Liste</a>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
