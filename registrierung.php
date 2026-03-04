<?php
require "config.php";

$meldung = ""; //message init

if (isset($_POST["registrieren"])) { //check if form submitted

    $vorname = trim($_POST["vorname"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $strasse = trim($_POST["strasse"] ?? "");
    $plz = trim($_POST["plz"] ?? "");
    $ort = trim($_POST["ort"] ?? "");

    if ($vorname !== "" && $name !== "" && $strasse !== "" && $plz !== "" && $ort !== "") { //check if all fields are filled

        $sql = "INSERT INTO ausleiher (vorname, name, strasse, plz, ort)
                VALUES (?, ?, ?, ?, ?)"; //prepare sql statement

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$vorname, $name, $strasse, $plz, $ort]); //execute statement with user inputs

        $neueID = $pdo->lastInsertId(); //get last inserted ID
        $meldung = "Registrierung erfolgreich. Deine Leser-ID ist: " . $neueID; //get last inserted ID, show success message with ID
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php if ($meldung !== ""): ?>
    <p class="message"><?= htmlspecialchars($melding) ?></p> <!-- htmlspecialchars to prevent XSS -->
    <?php endif; ?>

    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link rel="stylesheet" href="/bibliothek/style.css">
</head>

<body>
<div class="container">
    <a href="index.php" class="adminbutton">Zurück</a>
    <h1>Registrieren</h1>
    <form method="post"> <!-- form for user registration -->
        <label for="vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" required><br>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="strasse">Straße:</label>
        <input type="text" id="strasse" name="strasse" required><br>

        <label for="plz">PLZ:</label>
        <input type="text" id="plz" name="plz" required><br>

        <label for="ort">Ort:</label>
        <input type="text" id="ort" name="ort" required><br>

        <button type="submit" name="registrieren">Registrieren</button>
</form>
</div> <!-- end container -->
</body>

