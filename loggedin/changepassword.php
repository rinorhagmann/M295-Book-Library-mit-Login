<?php
session_start(); // Startet die Session, um auf Session-Variablen zuzugreifen
if (!isset($_SESSION['user_id'])) {
    // Überprüft, ob der Benutzer eingeloggt ist
    header("Location: ../index.php"); // Weiterleitung zur Startseite, falls nicht
    exit();
}

$admin = $_SESSION['admin']; // Speichert den Admin-Status des Benutzers

require '../db.php'; // Einbindung der Datenbankverbindung

$message = ''; // Variable für Rückmeldungen an den Benutzer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Holt die Formulardaten
    $user_id = $_SESSION['user_id']; // ID des eingeloggten Benutzers
    $current_password = $con->real_escape_string($_POST['current_password']); // Aktuelles Passwort
    $new_password = $con->real_escape_string($_POST['new_password']); // Neues Passwort
    $confirm_password = $con->real_escape_string($_POST['confirm_password']); // Bestätigung des neuen Passworts

    // Abfrage des aktuellen Passworts aus der Datenbank
    $result = $con->query("SELECT passwort FROM benutzer WHERE ID = $user_id");
    $user = $result->fetch_assoc(); // Holt die Benutzerdaten

    if (password_verify($current_password, $user['passwort'])) {
        // Überprüft, ob das aktuelle Passwort korrekt ist
        if ($new_password === $confirm_password) {
            // Überprüft, ob das neue Passwort mit der Bestätigung übereinstimmt
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hashen des neuen Passworts
            $con->query("UPDATE benutzer SET passwort = '$hashed_password' WHERE ID = $user_id"); // Aktualisiert das Passwort in der Datenbank
            $message = "Passwort erfolgreich geändert."; // Erfolgsmeldung
        } else {
            $message = "Die neuen Passwörter stimmen nicht überein."; // Fehlermeldung bei nicht übereinstimmenden Passwörtern
        }
    } else {
        $message = "Das aktuelle Passwort ist falsch."; // Fehlermeldung bei falschem aktuellem Passwort
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passwort ändern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
            <a class="nav-link" href="../logout.php">Logout</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($admin == 1) ? '' : 'disabled'; ?>" 
            href="<?php echo ($admin == 1) ? '../admintools/dashboard.php' : '#'; ?>">
                Admin
            </a>
          </li>

        </ul>
      </div>
    </div>
  </nav>
</header>
<br><br><br><br>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Passwort ändern</h2>
    <?php if ($message): ?>
        <!-- Anzeige von Rückmeldungen -->
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <!-- Formular für die Passwortänderung -->
        <div class="mb-3">
            <label for="current_password" class="form-label">Aktuelles Passwort</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Neues Passwort</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Neues Passwort bestätigen</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Passwort ändern</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
