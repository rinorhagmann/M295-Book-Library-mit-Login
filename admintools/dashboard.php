<?php
session_start();
// Überprüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); //Wenn nicht eingeloggt, weiterleiten zur Login-Seite
    exit();
}

// Prüfen, ob der Nutzer Admin ist
if ($_SESSION['admin'] != 1) {
    echo "Sie sind nicht berechtigt, diese Seite zu besuchen! Sie werden automatisch zur Homepage weitergeleitet..."; // Nachricht anzeigen
    echo '<meta http-equiv="refresh" content="5;url=../index.php">'; // Weiterleitung zur Homepage nach 5 Sekunden
    exit();
}

// Verbindung zur Datenbank herstellen
require '../db.php';
?>

<!doctype html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
  <body>
<header data-bs-theme="dark">
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">ADMIN</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="../loggedin/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="../loggedin/buecher.php">Bücher</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main class="container mt-5">
  <h1 class="text-center">Admin Dashboard</h1>
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title">Passwortverwaltung</h5>
          <p class="card-text">Passwort ändern</p>
          <a href="../loggedin/changepassword.php" class="btn btn-primary">Verwalten</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title">Bücherverwaltung</h5>
          <p class="card-text">Bücher hinzufügen, bearbeiten oder entfernen</p>
          <a href="books.php" class="btn btn-primary">Verwalten</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title">Kundenverwaltung</h5>
          <p class="card-text">Kunden anzeigen, bearbeiten und löschen</p>
          <a href="clients.php" class="btn btn-primary">Verwalten</a>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="container mt-5 text-center">
  <p>&copy; 2025 Bücher-Antiquariat, Inc. &middot; <a href="../impressum.php">Impressum</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
