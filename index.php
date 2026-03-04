<?php require "config.php"; ?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Bibliothek</title>
  <link rel="stylesheet" href="/bibliothek/style.css">
</head>

<body>

  <div class="header">
    <img src="img/logo.png" width="800" height="140" class="left">

    <div>
      <a href="AlleBuecher.php" class="adminbutton">Alle Bücher anzeigen</a> <!-- link to book list -->
      <?php if (isset($_SESSION["rolle"]) && $_SESSION["rolle"] === "verwalter"): ?>
        <a href="verwaltung.php" class="adminbutton">Verwaltung</a>  <!-- link to admin page, only for admins -->
      <?php endif; ?>
      <a href="registrierung.php" class="adminbutton">Registrieren</a> <!-- link to registration page -->
      <?php if (isset($_SESSION["benutzer"])): ?>
        <span style="color: #e0e0e0; margin-right: 10px;">Angemeldet als: <?= htmlspecialchars($_SESSION["benutzer"]) ?></span>
        <a href="logout.php" class="adminbutton">Abmelden</a>
      <?php else: ?>
        <a href="login.php" class="adminbutton">Anmelden</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="container">
  <h1 class="lwtitle">Willkommen auf der Seite der Bücherei Buxtehude.</h1>



  <form class="searchbar" action="AlleBuecher.php" method="get"> <!-- search form with get method -->
    <input type="text" name="q" placeholder="Suche nach Titel, Autor...">
    <input type="text" name="standort" placeholder="Standort">
    <button type="submit">Suchen</button>
  </form>

</div> <!-- end container -->
</body>
</html>
