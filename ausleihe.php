<?php
require "config.php";

$inventar = trim($_GET["inventarnummer"] ?? "");

if ($inventar === "") {
    die("Fehler: Keine Inventarnummer übergeben.");
}

$meldung = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $leseausweis = trim($_POST["leseausweis"] ?? "");

    if ($leseausweis === "") {
        $meldung = "Bitte Leser-ID eingeben.";
    } else {

        // Existiert die Leser-ID?
        $stmt = $pdo->prepare("SELECT 1 FROM ausleiher WHERE leseausweisnummer = ?");
        $stmt->execute([$leseausweis]);
        $leserExistiert = (bool)$stmt->fetchColumn();

        if (!$leserExistiert) {
            $meldung = "Diese Leser-ID existiert nicht.";
        } else {

            // Ist das Buch schon ausgeliehen?
            $stmt = $pdo->prepare("SELECT rueckgabe FROM ausleihe WHERE inventarnummer = ?");
            $stmt->execute([$inventar]);
            $bereitsAusgeliehen = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($bereitsAusgeliehen) {
                $meldung = "Buch ist bereits ausgeliehen. Zurück erwartet am: " . $bereitsAusgeliehen["rueckgabe"];
            } else {

                // Ausleihe eintragen
                $rueckgabe = date("Y-m-d", strtotime("+14 days"));

                $sql = "INSERT INTO ausleihe (inventarnummer, leseausweisnummer, rueckgabe)
                        VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$inventar, $leseausweis, $rueckgabe]);
                $sql = "UPDATE buecher
                    SET anzahl_ausleihen = COALESCE(anzahl_ausleihen, 0) + 1
                    WHERE inventarnummer = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$inventar]);


                $meldung = "Ausleihe erfolgreich. Rückgabe bis: " . $rueckgabe;
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

<h1>Buch ausleihen</h1>

<p>Inventarnummer: <?= htmlspecialchars($inventar) ?></p>

<?php if ($meldung !== ""): ?>
    <p><?= htmlspecialchars($meldung) ?></p>
<?php endif; ?>

<form method="post">
    <label for="leseausweis">Deine Leser-ID:</label>
    <input type="text" id="leseausweis" name="leseausweis" required>
    <button type="submit">Ausleihen</button>
</form>

<a href="AlleBuecher.php" class="adminbutton">Zurück</a>

</body>
</html>
