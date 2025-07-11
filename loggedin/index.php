<?php
session_start();
// Überprüfen, ob der Nutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$vorname = $_SESSION['vorname'];
$name = $_SESSION['name'];
$admin = $_SESSION['admin'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bücher-Antiquariat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
</head>
  <body>
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>
    
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
            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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

<main>

  <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active" style="background: #000000">
        <img class="bd-placeholder-img" src="../img/bg1.jpg" width="100%" height="100%" alt="Book Image" style="opacity: 0.5;">
        <div class="container">
          <div class="carousel-caption text-start">
            <h1>Hallo, <?php echo htmlspecialchars($name) . " " . htmlspecialchars($vorname); ?>!</h1>
            <p class="opacity-75">Hier finden Sie alles mögliche über unser Bücher-Antiquariat!</p>
            <p><a class="btn btn-lg btn-primary" href="#1">Sehen Sie sich es an!</a></p>
          </div>
        </div>
      </div>
      <div class="carousel-item" style="background: #000000">
      <img class="bd-placeholder-img" src="../img/bg2.jpg" width="100%" height="100%" alt="Book Image" style="opacity: 0.5;">
        <div class="container">
          <div class="carousel-caption">
            <h1>Unsere Auswahl</h1>
            <p>Hier finden Sie unsere Auswahl an Büchern!</p>
            <p><a class="btn btn-lg btn-primary" href="buecher.php">Zur Bücherliste</a></p>
          </div>
        </div>
      </div>
      <div class="carousel-item" style="background: #000000">
      <img class="bd-placeholder-img" src="../img/bg3.jpg" width="100%" height="100%" alt="Book Image" style="opacity: 0.5;">
        <div class="container">
          <div class="carousel-caption text-end">
            <h1>Sie möchten Ihr Passwort ändern?</h1>
            <p>Dazu wird Ihr altes, sowie neues Passwort benötigt!</p>
            <p><a class="btn btn-lg btn-primary" href="changepassword.php">Passwort ändern</a></p>
          </div>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>


  <!-- Marketing messaging and featurettes
  ================================================== -->
  <!-- Wrap the rest of the page in another container to center all the content. -->

  <div class="container marketing">

    <!-- Three columns of text below the carousel -->


    <!-- START THE FEATURETTES -->


    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading fw-normal lh-1" id="1">Entdecken Sie literarische Schätze. <span class="text-body-secondary">Jedes Buch erzählt eine Geschichte.</span></h2>
        <p class="lead">In unserem Bücher-Antiquariat finden Sie seltene und wertvolle Ausgaben, die Geschichte atmen. Von Erstausgaben bis zu vergriffenen Klassikern - hier wird jeder Bücherliebhaber fündig.</p>
      </div>
      <div class="col-md-5">
        <img src="../img/ft1.jpg" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" width="500" height="500" alt="Antiquarische Bücher">
      </div>
    </div>

    <hr class="featurette-divider">

    <div class="row featurette">
      <div class="col-md-7 order-md-2">
        <h2 class="featurette-heading fw-normal lh-1">Fachmännische Beratung. <span class="text-body-secondary">Ihr Weg zum perfekten Buch.</span></h2>
        <p class="lead">Unser erfahrenes Team steht Ihnen mit Rat und Tat zur Seite. Ob Sie eine bestimmte Ausgabe suchen oder Ihre Sammlung erweitern möchten - wir helfen Ihnen, das richtige Buch zu finden.</p>
      </div>
      <div class="col-md-5 order-md-1">
        <img src="../img/ft2.jpg" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" width="500" height="500" alt="Buchberatung">
      </div>
    </div>

    <hr class="featurette-divider">

    <div class="row featurette">
      <div class="col-md-7">
        <h2 class="featurette-heading fw-normal lh-1">Restauration und Pflege. <span class="text-body-secondary">Bewahren Sie Literaturgeschichte.</span></h2>
        <p class="lead">Wir bieten professionelle Restaurationsservices für Ihre wertvollen Bücher. Unsere Experten sorgen dafür, dass Ihre literarischen Schätze für zukünftige Generationen erhalten bleiben.</p>
      </div>
      <div class="col-md-5">
        <img src="../img/ft3.jpg" class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" width="500" height="500" alt="Buchrestauration">
      </div>
    </div>

    <hr class="featurette-divider">

    <!-- /END THE FEATURETTES -->

  </div><!-- /.container -->


  <!-- FOOTER -->
  <footer class="container">
    <p class="float-end"><a href="#">Zurück nach oben &#8593; </a></p>
    <p>&copy; 2025 Bücher-Antiquariat, Inc. &middot; <a href="impressum.php">Impressum</a></p><br><br><br>
  </footer>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>