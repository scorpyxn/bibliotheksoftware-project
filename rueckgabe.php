<?php
require "config.php";

$inventar = trim($_GET["inventarnummer"] ?? "");
$meldung = "";

if ($inventar === "") {
    die("Fehler: Keine Inventarnummer übergeben.");
}

// Prüfen, ob Rückgabe abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ist das Buch überhaupt ausgeliehen?
    $stmt = $pdo->prepare("SELECT rueckgabe FROM ausleihe WHERE inventarnummer = ?");
    $stmt->execute([$inventar]);
    $buch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$buch) {
        $meldung = "Dieses Buch ist nicht ausgeliehen oder bereits zurückgegeben.";
    } else {
        // Eintrag aus ausleihe löschen (Buch zurückgegeben)
        $stmt = $pdo->prepare("DELETE FROM ausleihe WHERE inventarnummer = ?");
        $stmt->execute([$inventar]);
        $meldung = "Buch erfolgreich zurückgegeben.";
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

<h1>Buch zurückgeben</h1>

<p>Inventarnummer: <?= htmlspecialchars($inventar) ?></p>

<?php if ($meldung !== ""): ?>
    <p><?= htmlspecialchars($meldung) ?></p>
<?php endif; ?>

<form method="post">
    <button type="submit">Rückgabe bestätigen</button>
</form>

<a href="AlleBuecher.php" class="adminbutton">Zurück</a>

</body>
</html>
