<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../login.php"); // Weiterleitung zur Login-Seite, falls nicht
    exit();
}
require '../db.php'; // Einbindung der Datenbankverbindung

// Filterparameter aus der GET-Anfrage
$search_name = isset($_GET['search_name']) ? $con->real_escape_string($_GET['search_name']) : ''; // Name
$search_vorname = isset($_GET['search_vorname']) ? $con->real_escape_string($_GET['search_vorname']) : ''; // Vorname
$search_kunde_seit = isset($_GET['search_kunde_seit']) ? $con->real_escape_string($_GET['search_kunde_seit']) : ''; // Kunde seit
$filter_email = isset($_GET['filter_email']) ? $con->real_escape_string($_GET['filter_email']) : ''; // Kontakt per Mail

// Pagination-Parameter
$limit = 25; // Anzahl der Einträge pro Seite
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Aktuelle Seite
$offset = ($page - 1) * $limit; // Berechnung des Offsets

// Gesamtanzahl der Einträge ermitteln
$total_query = "SELECT COUNT(*) AS total FROM kunden WHERE 
    (name LIKE '%$search_name%' OR '$search_name' = '') AND 
    (vorname LIKE '%$search_vorname%' OR '$search_vorname' = '') AND 
    (kunde_seit LIKE '%$search_kunde_seit%' OR '$search_kunde_seit' = '') AND 
    (kontaktpermail = '$filter_email' OR '$filter_email' = '')";
$total_result = $con->query($total_query); // Führt die Abfrage aus
$total_rows = $total_result->fetch_assoc()['total']; // Gesamtanzahl der Einträge
$total_pages = ceil($total_rows / $limit); // Berechnung der Gesamtseiten

// Abfrage der Kundendaten mit Filter, Limit und Offset
$query = "SELECT * FROM kunden WHERE 
    (name LIKE '%$search_name%' OR '$search_name' = '') AND 
    (vorname LIKE '%$search_vorname%' OR '$search_vorname' = '') AND 
    (kunde_seit LIKE '%$search_kunde_seit%' OR '$search_kunde_seit' = '') AND 
    (kontaktpermail = '$filter_email' OR '$filter_email' = '')
    LIMIT $limit OFFSET $offset";

$result = $con->query($query); // Führt die Abfrage aus
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kundenverwaltung</title>
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
    <h2 class="mb-4"><br>Kundenverwaltung</h2>
    <form class="row g-3 mb-3" method="get">
        <!-- Filterformular -->
        <div class="col-md-3">
            <label for="search_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="search_name" name="search_name" value="<?php echo htmlspecialchars($search_name); ?>" placeholder="Name suchen">
        </div>
        <div class="col-md-3">
            <label for="search_vorname" class="form-label">Vorname</label>
            <input type="text" class="form-control" id="search_vorname" name="search_vorname" value="<?php echo htmlspecialchars($search_vorname); ?>" placeholder="Vorname suchen">
        </div>
        <div class="col-md-3">
            <label for="search_kunde_seit" class="form-label">Kunde seit</label>
            <input type="date" class="form-control" id="search_kunde_seit" name="search_kunde_seit" value="<?php echo htmlspecialchars($search_kunde_seit); ?>">
        </div>
        <div class="col-md-3">
            <label for="filter_email" class="form-label">Kontakt per Mail erwünscht</label>
            <select class="form-select" id="filter_email" name="filter_email">
                <option value="" <?php echo $filter_email === '' ? 'selected' : ''; ?>>Alle</option>
                <option value="1" <?php echo $filter_email === '1' ? 'selected' : ''; ?>>Ja</option>
                <option value="0" <?php echo $filter_email === '0' ? 'selected' : ''; ?>>Nein</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Filtern</button>
            <a href="clients.php" class="btn btn-secondary">Zurücksetzen</a>
        </div>
    </form>
    <a href="add_client.php" class="btn btn-success mb-3">Neuen Kunden hinzufügen</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vorname</th>
                <th>Name</th>
                <th>Geburtstag</th>
                <th>Geschlecht</th>
                <th>Kunde seit</th>
                <th>E-Mail</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <!-- Ausgabe der Kundendaten -->
                <tr>
                    <td><?= htmlspecialchars($row['kid']) ?></td>
                    <td><?= htmlspecialchars($row['vorname']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['geburtstag']) ?></td>
                    <td><?= htmlspecialchars($row['geschlecht']) ?></td>
                    <td><?= htmlspecialchars($row['kunde_seit']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <a href="edit_client.php?id=<?= $row['kid'] ?>" class="btn btn-warning btn-sm">Bearbeiten</a>
                        <a href="delete_client.php?id=<?= $row['kid'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Sind Sie sicher?');">Löschen</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=1&search_name=<?= urlencode($search_name) ?>&search_vorname=<?= urlencode($search_vorname) ?>&search_kunde_seit=<?= urlencode($search_kunde_seit) ?>&filter_email=<?= urlencode($filter_email) ?>">⏮ Erste</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search_name=<?= urlencode($search_name) ?>&search_vorname=<?= urlencode($search_vorname) ?>&search_kunde_seit=<?= urlencode($search_kunde_seit) ?>&filter_email=<?= urlencode($filter_email) ?>">⬅ Zurück</a></li>
            <?php endif; ?>

            <li class="page-item disabled"><span class="page-link">Seite <?= $page ?> von <?= $total_pages ?></span></li>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search_name=<?= urlencode($search_name) ?>&search_vorname=<?= urlencode($search_vorname) ?>&search_kunde_seit=<?= urlencode($search_kunde_seit) ?>&filter_email=<?= urlencode($filter_email) ?>">Weiter ➡</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>&search_name=<?= urlencode($search_name) ?>&search_vorname=<?= urlencode($search_vorname) ?>&search_kunde_seit=<?= urlencode($search_kunde_seit) ?>&filter_email=<?= urlencode($filter_email) ?>">Letzte ⏭</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<footer class="container mt-5 text-center">
    <p>&copy; 2025 Bücher-Antiquariat, Inc. &middot; <a href="../impressum.php">Impressum</a></p>
</footer><br><br><br>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
