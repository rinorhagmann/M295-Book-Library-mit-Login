<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id']) || $_SESSION['admin'] != 1) {
    // Überprüft, ob der Benutzer eingeloggt ist und Admin-Rechte hat
    header("Location: ../index.php"); // Weiterleitung zur Startseite, falls nicht
    exit();
}

require '../db.php'; // Einbindung der Datenbankverbindung

// Suchparameter aus der GET-Anfrage
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : ''; // Suchbegriff

// Pagination-Parameter
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Aktuelle Seite
$limit = 25; // Anzahl der Einträge pro Seite
$offset = ($page - 1) * $limit; // Berechnung des Offsets

// Gesamtanzahl der Einträge abrufen
$total_sql = "SELECT COUNT(*) AS total FROM buecher WHERE Title LIKE '%$search%' OR autor LIKE '%$search%' OR kategorie LIKE '%$search%'";
$total_result = $con->query($total_sql); // Führt die Abfrage aus
$total_row = $total_result->fetch_assoc(); // Holt die Gesamtanzahl der Einträge
$total_entries = $total_row['total']; // Gesamtanzahl der Einträge
$total_pages = ceil($total_entries / $limit); // Berechnung der Gesamtseiten

// Paginierte SQL-Abfrage
$sql = "SELECT * FROM buecher WHERE Title LIKE '%$search%' OR autor LIKE '%$search%' OR kategorie LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = $con->query($sql); // Führt die Abfrage aus
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bücherverwaltung</title>
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
    <h2 class="mb-4"><br>Bücherverwaltung</h2>
    <form class="d-flex mb-3" method="get">
        <!-- Suchformular -->
        <input class="form-control me-2" type="search" name="search" placeholder="Nach Büchern suchen..." value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-success" type="submit">Suchen</button>
    </form>
    <a href="add_book.php" class="btn btn-success mb-3">Neues Buch hinzufügen</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titel</th>
                <th>Autor</th>
                <th>Kategorie</th>
                <th>Verkauft</th>
                <th>Käufer</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Ausgabe der Buchdaten
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['Title']}</td>";
                    echo "<td>{$row['autor']}</td>";
                    echo "<td>{$row['kategorie']}</td>";
                    echo "<td>{$row['verkauft']}</td>";
                    echo "<td>{$row['kaufer']}</td>";
                    echo "<td>";
                    echo "<a href='edit_book.php?id={$row['id']}' class='btn btn-warning btn-sm'>Bearbeiten</a> ";
                    echo "<a href='delete_book.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Sind Sie sicher?\")'>Löschen</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                // Nachricht, falls keine Bücher gefunden wurden
                echo "<tr><td colspan='7' class='text-center'>Keine Bücher gefunden</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <!-- Links zur ersten Seite und zur vorherigen Seite -->
                <li class="page-item"><a class="page-link" href="?page=1&search=<?= urlencode($search) ?>">⏮ Erste</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">⬅ Zurück</a></li>
            <?php endif; ?>

            <li class="page-item disabled"><span class="page-link">Seite <?= $page ?> von <?= $total_pages ?></span></li>

            <?php if ($page < $total_pages): ?>
                <!-- Links zur nächsten Seite und zur letzten Seite -->
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Weiter ➡</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>">Letzte ⏭</a></li>
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
