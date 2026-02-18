<?php ?>
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
      <a href="verwaltung.php" class="adminbutton">Verwaltung</a>  <!-- link to admin page -->
      <a href="registrierung.php" class="adminbutton">Registrieren</a> <!-- link to registration page -->
    </div>
  </div>

  <h1 class="lwtitle">Willkommen auf der Seite der Bücherei Buxtehude.</h1>



  <form class="searchbar" action="AlleBuecher.php" method="get"> <!-- search form with get method -->
    <input type="text" name="q" placeholder="Suche nach Titel, Autor, ISBN …">
    <input name="standort" placeholder="Standort">
    <button type="submit">Suchen</button>
  </form>

</body>
</html>
