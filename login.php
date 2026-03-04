<?php
require "config.php";

// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$meldung = ""; // user message
$loginType = ""; // login as user or admin

// handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginType = $_POST["loginType"] ?? "";
    
    if ($loginType === "user") {
        // login as normal user with reader ID
        $leseausweis = trim($_POST["leseausweis"] ?? "");
        
        if ($leseausweis === "") {
            $meldung = "Bitte Leser-ID eingeben.";
        } else {
            // check if reader ID exists
            $stmt = $pdo->prepare("SELECT vorname, name FROM ausleiher WHERE leseausweisnummer = ?");
            $stmt->execute([$leseausweis]);
            $ausleiher = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ausleiher) {
                $meldung = "Diese Leser-ID existiert nicht.";
            } else {
                // set session
                $_SESSION["benutzer"] = $ausleiher["vorname"] . " " . $ausleiher["name"];
                $_SESSION["leseausweisnummer"] = $leseausweis;
                $_SESSION["rolle"] = "benutzer";
                header("Location: index.php");
                exit;
            }
        }
    } elseif ($loginType === "admin") {
        // login as admin with password
        $passwort = $_POST["passwort"] ?? "";
        $adminPW = "admin123"; // simple password (change in production!)
        
        if ($passwort === $adminPW) {
            $_SESSION["benutzer"] = "Verwalter";
            $_SESSION["rolle"] = "verwalter";
            header("Location: index.php");
            exit;
        } else {
            $meldung = "Falsches Passwort.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden</title>
    <link rel="stylesheet" href="/bibliothek/style.css">
</head>
<body>

<div class="container">
    <h1>Anmelden</h1>
    
    <?php if ($meldung !== ""): ?>
        <p class="message"><?= htmlspecialchars($meldung) ?></p>
    <?php endif; ?>
    
    <h2>Als Benutzer anmelden</h2>
    <form method="post">
        <input type="hidden" name="loginType" value="user">
        <label for="leseausweis">Leser-ID:</label>
        <input type="text" id="leseausweis" name="leseausweis" required>
        <button type="submit">Anmelden</button>
    </form>
    
    <h2>Als Verwalter anmelden</h2>
    <form method="post">
        <input type="hidden" name="loginType" value="admin">
        <label for="passwort">Passwort:</label>
        <input type="password" id="passwort" name="passwort" required>
        <button type="submit">Anmelden</button>
    </form>
    
    <hr style="margin: 20px 0; border: none; border-top: 1px solid #444;">
    
    <a href="index.php" class="adminbutton">Zurück zur Startseite</a>
    
</div> <!-- end container -->

</body>
</html>
