<?php
require "config.php";

$inventar = trim($_GET["inventarnummer"] ?? "");
$meldung = "";

if ($inventar === "") {
    die("Fehler: Keine Inventarnummer übergeben.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $leseausweis = trim($_POST["leseausweis"] ?? "");

    if ($leseausweis === "") {
        $meldung = "Bitte Leser-ID eingeben.";
    } else {

        // check whether the book is currently loaned out and to this reader ID
        $stmt = $pdo->prepare("
            SELECT * FROM ausleihe 
            WHERE inventarnummer = ? 
            AND rueckgabe_am IS NULL
            AND leseausweisnummer = ?
        ");
        $stmt->execute([$inventar, $leseausweis]);
        $ausleihe = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ausleihe) {
            $meldung = "Fehler: Dieses Buch ist nicht unter Ihrer Leser-ID ausgeliehen oder bereits zurückgegeben.";
        } else {

            // record the return
            $stmt = $pdo->prepare("
                UPDATE ausleihe 
                SET rueckgabe_am = NOW() 
                WHERE inventarnummer = ? 
                AND rueckgabe_am IS NULL
                AND leseausweisnummer = ?
            ");
            $stmt->execute([$inventar, $leseausweis]);

            // optional: check for overdue
            $heute = date("Y-m-d");
            if ($heute > $ausleihe["faellig_am"]) {
                $meldung = "Buch erfolgreich zurückgegeben. Achtung: Rückgabe war verspätet.";
            } else {
                $meldung = "Buch erfolgreich zurückgegeben.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Buch zurückgeben</title>
    <link rel="stylesheet" href="/bibliothek/style.css">
</head>
<body>

<div class="container">

<h1>Buch zurückgeben</h1>

<p>Inventarnummer: <?= htmlspecialchars($inventar) ?></p>

<?php if ($meldung !== ""): ?>
    <p class="message"><?= htmlspecialchars($meldung) ?></p>
<?php endif; ?>

<form method="post">
    <label for="leseausweis">Leser-ID:</label>
    <input type="text" id="leseausweis" name="leseausweis" required>
    <button type="submit">Rückgabe bestätigen</button>
</form>

<a href="AlleBuecher.php" class="adminbutton">Zurück</a>

</div> <!-- end container -->
</body>
</html>