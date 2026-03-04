<?php
require "config.php";

// check if user is admin
if (!isset($_SESSION["rolle"]) || $_SESSION["rolle"] !== "verwalter") {
    die("Zugriff verweigert. Nur Verwalter dürfen diese Seite sehen.");
}
$meldung = ""; // user message

if (isset($_POST["medium_hinzufuegen"])) { // form submitted to add a medium

    $titel = trim($_POST["titel"] ?? ""); // get title from form, trim spaces
    $verfasser = trim($_POST["verfasser"] ?? ""); // get author
    $gruppe = trim($_POST["gruppe"] ?? ""); // get group
    $standort = trim($_POST["standort"] ?? ""); // get location

    if ($titel !== "" && $verfasser !== "" && $gruppe !== "" && $standort !== "") { // ensure all fields are filled

        $sql = "INSERT INTO buecher (titel, verfasser, gruppe, standort)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titel, $verfasser, $gruppe, $standort]); // execute insertion with provided values

        $meldung = "Medium wurde hinzugefügt.";
    } else { // if any field empty, set error message
        $meldung = "Bitte alle Felder ausfüllen (inkl. Standort).";
    }
}


if (isset($_POST["medium_entfernen"])) { // form submitted to remove a medium
    $inventarloeschen = trim($_POST["inventarnummer"] ??""); // get inventory number to delete

    if ($inventarloeschen !== "") {
        $sql = "DELETE FROM buecher WHERE inventarnummer = ?"; 

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$inventarloeschen]); // delete book with given inventory number
        
        $meldung = "Medium mit Inventarnummer $inventarloeschen wurde entfernt.";
    }
}


$inventar = trim($_GET["inventarnummer"] ?? ""); // get inventory number from query string

if ($inventar !== "") {
    $sql = "SELECT titel, verfasser, anzahl_ausleihen FROM buecher WHERE inventarnummer = ?"; 
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$inventar]); // fetch values for given inv number
    $buch = $stmt->fetch(PDO::FETCH_ASSOC);
}

// list all books with current loan status and borrower
$heute = date("Y-m-d");
$sql = "SELECT b.inventarnummer, b.titel, b.verfasser, b.gruppe, b.standort,
               b.anzahl_ausleihen, a.faellig_am, ausl.vorname, ausl.name
        FROM buecher b
        LEFT JOIN ausleihe a ON a.inventarnummer = b.inventarnummer
            AND a.rueckgabe_am IS NULL
        LEFT JOIN ausleiher ausl ON a.leseausweisnummer = ausl.leseausweisnummer";
$stmt = $pdo->query($sql);
$alleBuecher = $stmt->fetchAll(PDO::FETCH_ASSOC);

// list all borrowers along with total loans (historical)
// also include contact details (street, zip, city)
$sql = "SELECT x.leseausweisnummer, x.vorname, x.name, x.strasse, x.plz, x.ort,
               COUNT(l.leseausweisnummer) AS anzahl_ausleihen
        FROM ausleiher x
        LEFT JOIN ausleihe l ON l.leseausweisnummer = x.leseausweisnummer
        GROUP BY x.leseausweisnummer";
$stmt = $pdo->query($sql);
$alleAusleiher = $stmt->fetchAll(PDO::FETCH_ASSOC);

// list all loan records (historical)
$sql = "SELECT a.*, b.titel AS buch_titel, ausl.vorname AS ausl_vorname, ausl.name AS ausl_name
        FROM ausleihe a
        LEFT JOIN buecher b ON a.inventarnummer = b.inventarnummer
        LEFT JOIN ausleiher ausl ON a.leseausweisnummer = ausl.leseausweisnummer";
$stmt = $pdo->query($sql);
$alleAusleihen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// list overdue loan records
$sql = "SELECT a.*, b.titel AS buch_titel, ausl.vorname AS ausl_vorname, ausl.name AS ausl_name
        FROM ausleihe a
        JOIN buecher b ON a.inventarnummer = b.inventarnummer
        JOIN ausleiher ausl ON a.leseausweisnummer = ausl.leseausweisnummer
        WHERE a.rueckgabe_am IS NULL AND a.faellig_am < ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$heute]);
$ueberfaellige = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="/bibliothek/style.css"> <!-- stylesheet -->
</head>
<body>
<div class="container">
        <?php if (!empty($meldung)) : ?>
        <p class="message"><?= htmlspecialchars($meldung) ?></p>
        <?php endif; ?>
    <a href="index.php" class="adminbutton">Zurück</a> <!-- back button -->

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

<h2>Bücherliste</h2>
<table>
    <thead>
        <tr>
            <th>Inv.-Nr.</th>
            <th>Titel</th>
            <th>Verfasser</th>
            <th>Gruppe</th>
            <th>Standort</th>
            <th>Ausleiher</th>
            <th>Status</th>
            <th>Fällig am</th>
            <th>Überfällig</th>
            <th>Anzahl Ausleihen</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($alleBuecher as $b): ?>
        <?php $istAusgeliehen = ($b['faellig_am'] !== null); ?>
        <?php $ueberfaellig = $istAusgeliehen && ($b['faellig_am'] < $heute); ?>
        <tr>
            <td><?= htmlspecialchars($b['inventarnummer']) ?></td>
            <td><?= htmlspecialchars($b['titel']) ?></td>
            <td><?= htmlspecialchars($b['verfasser']) ?></td>
            <td><?= htmlspecialchars($b['gruppe']) ?></td>
            <td><?= htmlspecialchars($b['standort']) ?></td>
            <td><?= $istAusgeliehen ? htmlspecialchars($b['vorname'] . ' ' . $b['name']) : '-' ?></td>
            <td class="status <?= $istAusgeliehen ? 'ausgeliehen' : 'verfuegbar' ?>">
                <?= $istAusgeliehen ? 'ausgeliehen' : 'verfügbar' ?>
            </td>
            <td><?= $istAusgeliehen ? htmlspecialchars($b['faellig_am']) : '-' ?></td>
            <td class="<?= $ueberfaellig ? 'ueberfaellig' : '' ?>">
                <?= $ueberfaellig ? 'JA' : '' ?></td>
            <td><?= htmlspecialchars($b['anzahl_ausleihen']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Überfällige Ausleihen</h2>
<?php if (count($ueberfaellige) === 0): ?>
    <p>Keine überfälligen Medien.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Inv.-Nr.</th>
            <th>Buchtitel</th>
            <th>Ausleiher</th>
            <th>Ausleihdatum</th>
            <th>Fällig am</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($ueberfaellige as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['inventarnummer']) ?></td>
            <td><?= htmlspecialchars($u['buch_titel']) ?></td>
            <td><?= htmlspecialchars($u['ausl_vorname'] . ' ' . $u['ausl_name']) ?></td>
            <td><?= htmlspecialchars($u['ausleihdatum']) ?></td>
            <td><?= htmlspecialchars($u['faellig_am']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<h2>Ausleiher</h2>
<table>
    <thead>
        <tr>
            <th>Leser-ID</th>
            <th>Vorname</th>
            <th>Name</th>
            <th>Straße</th>
            <th>PLZ</th>
            <th>Ort</th>
            <th>Anzahl Ausleihen</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($alleAusleiher as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['leseausweisnummer']) ?></td>
            <td><?= htmlspecialchars($l['vorname']) ?></td>
            <td><?= htmlspecialchars($l['name']) ?></td>
            <td><?= htmlspecialchars($l['strasse']) ?></td>
            <td><?= htmlspecialchars($l['plz']) ?></td>
            <td><?= htmlspecialchars($l['ort']) ?></td>
            <td><?= htmlspecialchars($l['anzahl_ausleihen']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Ausleihe</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Inv.-Nr.</th>
            <th>Leser-ID</th>
            <th>Buchtitel</th>
            <th>Ausleiher</th>
            <th>Ausleihdatum</th>
            <th>Fällig am</th>
            <th>Rückgabe am</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($alleAusleihen as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['id'] ?? '') ?></td>
            <td><?= htmlspecialchars($a['inventarnummer']) ?></td>
            <td><?= htmlspecialchars($a['leseausweisnummer']) ?></td>
            <td><?= htmlspecialchars($a['buch_titel']) ?></td>
            <td><?= htmlspecialchars($a['ausl_vorname'] . ' ' . $a['ausl_name']) ?></td>
            <td><?= htmlspecialchars($a['ausleihdatum']) ?></td>
            <td><?= htmlspecialchars($a['faellig_am']) ?></td>
            <td><?= htmlspecialchars($a['rueckgabe_am']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div> <!-- end container -->
</body>
</html>