<?php
require "config.php"; // include database connection and settings

// retrieve inventory number from query string
$inventar = trim($_GET["inventarnummer"] ?? "");

// if no inventory number is provided, stop with an error
if ($inventar === "") {
    die("Fehler: Keine Inventarnummer übergeben.");
}

$meldung = ""; // message shown to the user

// handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // get the reader ID from the posted form
    $leseausweis = trim($_POST["leseausweis"] ?? "");

    if ($leseausweis === "") {
        // no reader ID supplied
        $meldung = "Bitte Leser-ID eingeben.";
    } else {

        // check if the reader ID exists in the database
        $stmt = $pdo->prepare("SELECT 1 FROM ausleiher WHERE leseausweisnummer = ?");
        $stmt->execute([$leseausweis]);
        $leserExistiert = (bool)$stmt->fetchColumn();

        if (!$leserExistiert) {
            // invalid reader ID
            $meldung = "Diese Leser-ID existiert nicht.";
        } else {

            // check whether the book is already loaned out
            $stmt = $pdo->prepare("SELECT faellig_am FROM ausleihe WHERE inventarnummer = ? AND rueckgabe_am IS NULL");
            $stmt->execute([$inventar]);
            $bereitsAusgeliehen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($bereitsAusgeliehen) {
                // book currently checked out, show due date
                $meldung = "Buch ist bereits ausgeliehen. Zurück erwartet am: " . $bereitsAusgeliehen["faellig_am"];
            } else {
                // insert new loan record
                $ausleihdatum = date("Y-m-d");
                $faellig = date("Y-m-d", strtotime("+14 days"));

                $sql = "INSERT INTO ausleihe 
                (inventarnummer, leseausweisnummer, ausleihdatum, faellig_am)
                VALUES (?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$inventar, $leseausweis, $ausleihdatum, $faellig]);

                // increment the books total loan count
                $sql = "UPDATE buecher
                        SET anzahl_ausleihen = COALESCE(anzahl_ausleihen, 0) + 1
                        WHERE inventarnummer = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$inventar]);

                // success message with due date
                $meldung = "Ausleihe erfolgreich. Rückgabe bis: " . $faellig;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Buch ausleihen</title>
    <link rel="stylesheet" href="/bibliothek/style.css">
</head>
<body>

<div class="container">
    <a href="AlleBuecher.php" class="adminbutton">Zurück</a>

<h1>Buch ausleihen</h1>

<!-- show the selected inventory number -->
<p>Inventarnummer: <?= htmlspecialchars($inventar) ?></p>

<!-- display message if one exists (error or success) -->
<?php if ($meldung !== ""): ?>
    <p class="message"><?= htmlspecialchars($meldung) ?></p>
<?php endif; ?>

<!-- loan form: reader ID only -->
<form method="post">
    <label for="leseausweis">Deine Leser-ID:</label>
    <input type="text" id="leseausweis" name="leseausweis" required>
    <button type="submit">Ausleihen</button>
</form>

<!-- back link to book list -->


</div> <!-- end container -->
</body>
</html>