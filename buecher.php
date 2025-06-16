<?php
require 'db.php'; // Verbindung zur Datenbank

// Kategorien aus der Datenbank laden
$kategorien = [];
$kategorienResult = $con->query("SELECT id, kategorie FROM kategorien"); // Spaltenname angepasst
while ($row = $kategorienResult->fetch_assoc()) {
    $kategorien[(int)$row['id']] = $row['kategorie']; // Spaltenname angepasst
}

// Zustände aus der Datenbank laden
$zustaende = [];
$zustaendeResult = $con->query("SELECT zustand, beschreibung FROM zustaende"); // Spaltenname angepasst
while ($row = $zustaendeResult->fetch_assoc()) {
    $zustaende[$row['zustand']] = $row['beschreibung']; // Spaltenname angepasst
}

// Verkauft Angabe
$verkauft = [
    0 => "Nein",
    1 => "Ja"
];

// Standardwerte für Pagination & Sortierung
$limit = 20; // 20 Bücher pro Seite
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Aktuelle Seite
$offset = ($page - 1) * $limit; // Offset für SQL-Query

// Erlaubte Spalten für Sortierung & Filterung
$allowedColumns = ['id', 'katalog', 'nummer', 'Title', 'kategorie', 'verkauft', 'kaufer', 'autor', 'foto', 'verfasser', 'zustand']; // Alle erlaubten spalten
$sortBy = isset($_GET['sort']) && in_array($_GET['sort'], $allowedColumns) ? $_GET['sort'] : 'id'; // Standardwert für Sortierung
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC'; // Standardwert für Sortierung ASC und DESC

$filter = isset($_GET['filter']) && in_array($_GET['filter'], $allowedColumns) ? $_GET['filter'] : ''; // get Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // get Suchbegriff
// Wird danach in URL angezeigt, wenn Filter oder Suche gesetzt ist

// SQL-Grundgerüst
$sql = "SELECT * FROM buecher WHERE 1=1";

// Falls ein Filter gesetzt ist
$types = ""; // Typen für die Parameterbindung
$params = []; // Parameter für die SQL-Abfrage

//Logik für die Suche und Filter
if (!empty($filter) && !empty($search)) { // Filter und Suche
    if ($filter === 'kategorie') { // Wenn nach Kategorie gefiltert wird
        // Suche nach Kategorie-ID (unabhängig von Groß-/Kleinschreibung)
        $categoryId = false; // Standardwert für Kategorie-ID
        foreach ($kategorien as $id => $name) { // Alle Kategorien durchlaufen
            if (stripos($name, $search) !== false) { // Teilstring-Suche
                $categoryId = $id; // Wenn Kategorie gefunden, speichere die ID
                break; // Schleife abbrechen, wenn Kategorie gefunden wurde
            }
        }

        if ($categoryId !== false) { // Wenn die Kategorie-ID gefunden wurde
            $sql .= " AND kategorie = ?"; // Filtere nach Kategorie-ID
            $params[] = $categoryId; // Füge die Kategorie-ID zu den Parametern hinzu
            $types .= "i"; // Integer // Typ für die Kategorie-ID
        } else { //Ansonsten
            die("Kategorie nicht gefunden."); // Fehler ausgeben, wenn die Kategorie nicht existiert
        }
    } else { // Wenn nach anderen Spalten gefiltert wird
        $sql .= " AND $filter LIKE ?"; // Filtere nach der angegebenen Spalte
        $params[] = "%$search%"; // Füge den Suchbegriff zu den Parametern hinzu
        $types .= "s"; // String
    }
}

// Limit & Offset immer hinzufügen, aber als separate Parameter
$sql .= " ORDER BY $sortBy $order LIMIT ? OFFSET ?"; // Sortierung und Pagination hinzufügen

// Statement vorbereiten
$stmt = mysqli_prepare($con, $sql); // Überprüfen, ob das Prepared Stmt erfolgreich war

if (!$stmt) { // ist das Statement nicht erfolgreich?
    die("SQL-Fehler: " . mysqli_error($con)); // Fehler ausgeben
}

// Parameter binden
if (!empty($params)) { // Wenn Filter-Parameter vorhanden sind
    // Füge LIMIT und OFFSET zu den Parametern hinzu
    $params[] = $limit; // Limit
    $params[] = $offset; // Offset
    $types .= "ii"; // Für LIMIT und OFFSET
    
    // Binde alle Parameter
    mysqli_stmt_bind_param($stmt, $types, ...$params); // Bindet alle Parameter in der richtigen Reihenfolge
} else {
    // Wenn keine Filter-Parameter vorhanden sind, binde nur LIMIT und OFFSET
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset); // Bindet nur LIMIT und OFFSET
}

// Statement ausführen
mysqli_stmt_execute($stmt); // Überprüfen, ob das Statement erfolgreich war

// Ergebnis abrufen
$result = mysqli_stmt_get_result($stmt); // Überprüfen, ob das Ergebnis erfolgreich war

// Bücher abrufen
$buecher = mysqli_fetch_all($result, MYSQLI_ASSOC); // Überprüfen, ob die Bücher erfolgreich abgerufen wurden

// Gesamtanzahl der Bücher für Pagination basierend auf Filter und Suche
$totalBooksSql = "SELECT COUNT(*) FROM buecher WHERE 1=1";

// Falls ein Filter gesetzt ist, dieselben Bedingungen wie oben anwenden
$totalParams = []; // Parameter für die SQL-Abfrage
$totalTypes = ""; // Typen für die Parameterbindung

//Logik für die Seitenanzahl
if (!empty($filter) && !empty($search)) { // Filter und Suche
    if ($filter === 'kategorie') { // Wenn nach Kategorie gefiltert wird
        $categoryId = false; // Standardwert für Kategorie-ID
        foreach ($kategorien as $id => $name) { // Alle Kategorien durchlaufen
            if (stripos($name, $search) !== false) { // Teilstring-Suche
                $categoryId = $id; // Wenn Kategorie gefunden, speichere die ID
                break; // Schleife abbrechen, wenn Kategorie gefunden wurde
            }
        }

        if ($categoryId !== false) { // Wenn die Kategorie-ID gefunden wurde
            $totalBooksSql .= " AND kategorie = ?"; // Filtere nach Kategorie-ID
            $totalParams[] = $categoryId; // Füge die Kategorie-ID zu den Parametern hinzu
            $totalTypes .= "i"; // Integer Typ für die Kategorie-ID
        } else { // Ansonsten
            die("Kategorie nicht gefunden."); // Fehler ausgeben, wenn die Kategorie nicht existiert
        }
    } else { // Wenn nach anderen Spalten gefiltert wird
        $totalBooksSql .= " AND $filter LIKE ?"; // Filtere nach der angegebenen Spalte
        $totalParams[] = "%$search%"; // Füge den Suchbegriff zu den Parametern hinzu
        $totalTypes .= "s"; // String Typ für den Suchbegriff
    }
}

// Statement für Gesamtanzahl vorbereiten
$totalStmt = mysqli_prepare($con, $totalBooksSql); 

if (!$totalStmt) { // Überprüfen, ob das Prepared Stmt erfolgreich war
    die("SQL-Fehler: " . mysqli_error($con)); // Fehler ausgeben
}

// Parameter binden, falls vorhanden
if (!empty($totalParams)) { // Wenn Filter-Parameter vorhanden sind
    mysqli_stmt_bind_param($totalStmt, $totalTypes, ...$totalParams);// Bindet alle Parameter in der richtigen Reihenfolge
}

// Statement ausführen
mysqli_stmt_execute($totalStmt);

// Ergebnis abrufen
$totalBooksResult = mysqli_stmt_get_result($totalStmt); // Überprüfen, ob das Ergebnis erfolgreich war
$totalBooks = mysqli_fetch_array($totalBooksResult)[0]; // Gesamtanzahl der Bücher abrufen

// Gesamtanzahl der Seiten berechnen
$totalPages = ceil($totalBooks / $limit); 

mysqli_close($con); // Verbindung zur Datenbank schließen
?>



<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bücherliste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<style>
    body {
        padding: 0;
    }
</style>
<header data-bs-theme="dark">
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Bücher-Antiquariat</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto mb-2 mb-md-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="buecher.php">Bücher</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="admintools/dashboard.php">Admin</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header><br><br><br>

<main class="container mt-5 pt-5">
    <h1 class="text-center">📚 Bücherliste</h1>

    <!-- Filter & Suche -->
    <form method="GET" class="mb-4 d-flex justify-content-center">
        <select name="filter" class="form-select me-2" style="max-width: 200px;">
            <option value="" <?= empty($filter) ? 'selected' : '' ?> disabled>Suchen nach...</option>
            <option value="Title" <?= $filter === 'Title' ? 'selected' : '' ?>>Titel</option>
            <option value="autor" <?= $filter === 'autor' ? 'selected' : '' ?>>Autor</option>
            <option value="kategorie" <?= $filter === 'kategorie' ? 'selected' : '' ?>>Kategorie</option>
        </select>

        <input type="text" name="search" class="form-control me-2" placeholder="Suche..." value="<?= htmlspecialchars($search) ?>" style="max-width: 300px;">

        <!-- Sortieren nach -->
        <select name="sort" class="form-select me-2" style="max-width: 200px;">
            <option value="id" <?= $sortBy === 'id' ? 'selected' : '' ?> disabled>Sortieren nach...</option>
            <option value="Title" <?= $sortBy === 'Title' ? 'selected' : '' ?>>Titel</option>
            <option value="autor" <?= $sortBy === 'autor' ? 'selected' : '' ?>>Autor</option>
            <option value="kategorie" <?= $sortBy === 'kategorie' ? 'selected' : '' ?>>Kategorie</option>
            <option value="verfasser" <?= $sortBy === 'verfasser' ? 'selected' : '' ?>>Verfasser</option>
            <option value="zustand" <?= $sortBy === 'zustand' ? 'selected' : '' ?>>Zustand</option>
        </select>

        <select name="order" class="form-select me-2" style="max-width: 200px;">
            <option value="asc" <?= $order === 'ASC' ? 'selected' : '' ?>>Aufsteigend</option>
            <option value="desc" <?= $order === 'DESC' ? 'selected' : '' ?>>Absteigend</option>
        </select>

        <button type="submit" class="btn btn-success">Suchen & Sortieren</button>
    </form>


    <!-- Bücherliste -->
    <div class="row">
    <?php foreach ($buecher as $buch): ?>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body" style="display: flex; justify-content: space-between;">
                <div>
                    <h5 class="card-title"><?= isset($buch['Title']) && $buch['Title'] !== null && $buch['Title'] !== '' ? htmlspecialchars($buch['Title']) : 'Unbekannt' ?></h5><br>
                    <p class="card-text"><strong>Autor:</strong> <?= isset($buch['autor']) && $buch['autor'] !== null && $buch['autor'] !== '' ? htmlspecialchars($buch['autor']) : 'Unbekannt' ?></p>
                    <p class="card-text"><strong>Verfasser:</strong> <?= isset($buch['verfasser']) && $buch['verfasser'] !== null && $buch['verfasser'] !== '' ? htmlspecialchars($buch['verfasser']) : 'Unbekannt' ?></p>
                    <p class="card-text"><strong>Kategorie:</strong> <?= isset($kategorien[$buch['kategorie']]) && $kategorien[$buch['kategorie']] !== null ? htmlspecialchars($kategorien[$buch['kategorie']]) : 'Unbekannt' ?></p>
                    <p class="card-text"><strong>Zustand:</strong> <?= isset($zustaende[$buch['zustand']]) && $zustaende[$buch['zustand']] !== null ? htmlspecialchars($zustaende[$buch['zustand']]) : 'Unbekannt' ?></p><br><br>
                    <a href="buch.php?id=<?= $buch['id'] ?>" class="btn btn-primary">Details anschauen</a>
                </div>    
                    <div style="display: flex; justify-content: space-between;"><img src="img/buch-symbol_632498-3975.jpg" alt="Buch" width="325px" height="325px"></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=1&sort=<?= $sortBy ?>&order=<?= $order ?>&filter=<?= $filter ?>&search=<?= htmlspecialchars($search) ?>">⏮ Erste</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&sort=<?= $sortBy ?>&order=<?= $order ?>&filter=<?= $filter ?>&search=<?= htmlspecialchars($search) ?>">⬅ Zurück</a></li>
            <?php endif; ?>

            <li class="page-item disabled"><span class="page-link">Seite <?= $page ?> von <?= $totalPages ?></span></li>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&sort=<?= $sortBy ?>&order=<?= $order ?>&filter=<?= $filter ?>&search=<?= htmlspecialchars($search) ?>">Weiter ➡</a></li>
                <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>&sort=<?= $sortBy ?>&order=<?= $order ?>&filter=<?= $filter ?>&search=<?= htmlspecialchars($search) ?>">Letzte ⏭</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</main>

<footer class="container mt-5">
    <p class="float-end"><a href="#">Zurück nach oben &#8593; </a></p>
    <p>&copy; 2025 Bücher-Antiquariat, Inc. &middot; <a href="impressum.php">Impressum</a></p><br><br><br>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>