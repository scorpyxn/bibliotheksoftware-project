<?php
require "config.php"; //include database connection from config.php

$q = trim($_GET["q"] ?? "");
$standort = trim($_GET["standort"] ?? "");

$where = []; //array to hold WHERE conditions for SQL query
$params = []; //array to hold parameters for prepared statement

//base SQL query: select all books with their loan return dates
$sql = "SELECT b.inventarnummer, b.titel, b.verfasser, b.gruppe, a.faellig_am
        FROM buecher b
        LEFT JOIN ausleihe a ON a.inventarnummer = b.inventarnummer AND a.rueckgabe_am IS NULL";

//empty search will list all books in buecher
if ($q !== "") {
    $where[] = "(b.verfasser LIKE :q OR b.titel LIKE :q OR b.gruppe LIKE :q)";
    $params["q"] = "%" . $q . "%"; 
}

//if standort provided, add where condition to where array
if ($standort !== "") { 
    $where[] = "b.standort LIKE :standort";
    $params["standort"] = "%" . $standort . "%";
}

// join all where conditions with AND and add to SQL query
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// sort by title, ascending
$sql .= " ORDER BY b.titel ASC";

$stmt = $pdo->prepare($sql); //prepare the query with placeholders
$stmt->execute($params);    //run query, binding parameters from $params
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all results as associative array with PDO::FETCH_ASSOC method
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Alle Bücher</title>
    <link rel="stylesheet" href="/bibliothek/style.css"> <!-- link to css file -->
</head>
<body>

<div class="container">
        <a href="index.php" class="adminbutton">Zurück</a> <!-- link to index.php -->

<h1>Alle Bücher</h1>

<?php if ($q !== ""): ?>
    <p>Suchbegriff: <b><?= htmlspecialchars($q) ?></b></p> <!-- list search term, if provided, with htmlspecialchars to prevent XSS --> 
<?php endif; ?>

<?php if ($standort !== ""): ?> <!-- list location, if provided -->               
    <p>Standort: <b><?= htmlspecialchars($standort) ?></b></p>
<?php endif; ?>

<?php if (count($rows) === 0): ?> <!-- if no results, show message -->
    <p>Keine Treffer gefunden.</p>
<?php else: ?>

    <?php foreach ($rows as $buch): ?>
        <?php $istAusgeliehen = ($buch["faellig_am"] !== null); ?>

        <div class="allebuecherdarstellung">

            <span class="buchtext">
                <?= htmlspecialchars($buch["titel"]) ?> – <?= htmlspecialchars($buch["verfasser"]) ?>  <!-- display book title and author -->
            </span>

            <div class="rechtsbereich">

                <?php if ($istAusgeliehen): ?> <!-- if book loaned out, show status and return date -->
                    <span class="status ausgeliehen">(AUSGELIEHEN)</span>
                    <span class="rueckgabe">
                        zurück erwartet am: <?= htmlspecialchars($buch["faellig_am"]) ?>
                    </span>
                    <span class="ausleihenbtn disabled">Ausleihen</span> <!-- disable button "Ausleihen" -->
                    <a class="ausleihenbtn"
                       href="rueckgabe.php?inventarnummer=<?= urlencode($buch["inventarnummer"]) ?>"> <!-- inventarnummer parameter -->
                        Zurückgeben
                    </a>
                <?php else: ?> <!-- if book available, show "Ausleihen" button and link to Ausleihe.php -->
                    <span class="status verfuegbar">VERFÜGBAR</span>
                    <a class="ausleihenbtn"
                       href="ausleihe.php?inventarnummer=<?= urlencode($buch["inventarnummer"]) ?>"> <!-- inventarnummer parameter -->
                        Ausleihen
                    </a>
                <?php endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

<?php endif; ?>



</div> <!-- end container -->
</body>
</html>