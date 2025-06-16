<?php
session_start();
session_unset();
session_destroy();
echo "Sie haben sich erfolgreich ausgeloggt! Bitte warten Sie 5 Sekunden, um zur Login-Seite zu gelangen.";
echo '<meta http-equiv="refresh" content="5;url=login.php">';

exit();
?>