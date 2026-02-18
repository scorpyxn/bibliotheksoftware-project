<?php
require "config.php";
$meldung = "";

if (isset($_POST["medium_hinzufuegen"])) { //check if form was submitted with button "medium_hinzuefuegen"

    $titel = trim($_POST["titel"] ?? ""); //get title from form, trim spaces
    $verfasser = trim($_POST["verfasser"] ?? ""); //get author
    $gruppe = trim($_POST["gruppe"] ?? ""); //get group
    $standort = trim($_POST["standort"] ?? ""); //get location

    if ($titel !== "" && $verfasser !== "" && $gruppe !== "" && $standort !== "") { //check if all fields are filled

        $sql = "INSERT INTO buecher (titel, verfasser, gruppe, standort)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titel, $verfasser, $gruppe, $standort]); //add new book with form values

        $meldung = "Medium wurde hinzugefügt.";
    } else { //if any field empty, show message
        $meldung = "Bitte alle Felder ausfüllen (inkl. Standort).";
    }
}


if (isset($_POST["medium_entfernen"])) { //check if form was submitted with button "medium_entfernen"
    $inventarloeschen = trim($_POST["inventarnummer"] ??""); //get inventory number 

    if ($inventarloeschen !== "") {
        $sql = "DELETE FROM buecher WHERE inventarnummer = ?"; 

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$inventarloeschen]); //delete book with given inventory number
        
        $meldung = "Medium mit Inventarnummer $inventarloeschen wurde entfernt.";
    }
}


$inventar = trim($_GET["inventarnummer"] ?? ""); //get inv number from query

if ($inventar !== "") {
    $sql = "SELECT titel, verfasser, anzahl_ausleihen FROM buecher WHERE inventarnummer = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$inventar]); // fetch values for given inv number
    $buch = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="/bibliothek/style.css"> <!-- stylesheet -->
</head>
<body>
        <?php if (!empty($meldung)) : ?>
        <p><?= htmlspecialchars($meldung) ?></p>
        <?php endif; ?>
    <a href="index.php" class="adminbutton">Zurück zur Startseite</a> <!-- back button -->

    <h2>Neues Medium hinzufügen</h2> <!-- add book form -->
<form method="POST">
    <label for="titel">Titel:</label>
    <input type="text" id="titel" name="titel" required><br>

    <label for="verfasser">Verfasser:</label>
    <input type="text" id="verfasser" name="verfasser" required><br>

    <label for="gruppe">Gruppe:</label>
    <input type="text" id="gruppe" name="gruppe" required><br>

    <label for="standort">Standort:</label>
    <input type="text" id="standort" name="standort" required><br>


    <button type="submit" name="medium_hinzufuegen">Hinzufügen</button><br>
</form>

<h2>Medium aus dem Bestand entfernen</h2> <!-- remove book form -->
<form method="post">
    <label>Inventarnummer:</label>
    <input type="number" name="inventarnummer" required>

    <button type="submit" name="medium_entfernen">Entfernen</button>
</form>
</body>
</html>