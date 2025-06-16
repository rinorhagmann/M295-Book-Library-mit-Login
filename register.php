<?php
require 'db.php'; // Datenbankverbindung einbinden

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $benutzername = $_POST['benutzername'];
    $name = $_POST['name'];
    $vorname = $_POST['vorname'];
    $email = $_POST['email'];
    $passwort = $_POST['passwort']; // Passwort aus POST-Daten

    $admin = 1;

    // Eingaben prüfen
    if (!empty($benutzername) && !empty($name) && !empty($vorname) && !empty($email) && !empty($passwort)) {
        // Passwort hashen
        $hashed_password = password_hash($passwort, PASSWORD_DEFAULT);

        // Benutzer in die Datenbank eintragen
        $query = "INSERT INTO benutzer (benutzername, name, vorname, email, passwort, admin) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $benutzername, $name, $vorname, $email, $hashed_password, $admin);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Registrierung erfolgreich!<br>Bitte warten Sie 5 Sekunden, um zur Login-Seite zu gelangen.";
            echo '<div class="message success">' . $message . '</div>';
            echo '<meta http-equiv="refresh" content="5;url=login.php">';
            exit;
        } else {
            $error = "Fehler: " . mysqli_error($con);
            echo '<div class="message error">' . $error . '</div>';
        }
    } else {
        echo "Bitte fülle alle Felder aus!";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrieren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-signin {
            max-width: 330px;
            padding: 15px;
        }
    </style>
  </head>
  <body class="text-center" style="background: #f4f4f4;"><br><br><br><br><br><br>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <main class="form-signin w-150 m-auto" style="background: #fff; padding: 30px; border-radius: 20px;">
                    <form method="post" action="">
                        <h1 class="h3 mb-3 fw-normal">Registrieren</h1>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="floatingBenutzername" name="benutzername" placeholder="Benutzername" required>
                            <label for="floatingBenutzername">Benutzername</label>
                        </div><br>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="floatingName" name="name" placeholder="Name" required>
                            <label for="floatingName">Name</label>
                        </div><br>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="floatingVorname" name="vorname" placeholder="Vorname" required>
                            <label for="floatingVorname">Vorname</label>
                        </div><br>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="floatingEmail" name="email" placeholder="name@example.com" required>
                            <label for="floatingEmail">E-Mail Adresse</label>
                        </div><br>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="floatingPassword" name="passwort" placeholder="Passwort" required>
                            <label for="floatingPassword">Passwort</label>
                        </div><br>

                        <button class="btn btn-primary w-100 py-2" type="submit">Registrieren</button><br><br>
                        <p>Sie haben ein Konto? <a href="login.php">Anmelden</a></p>
                    </form>
                </main>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>