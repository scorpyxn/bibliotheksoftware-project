<?php
// start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// config.php

$host = "localhost"; //localhost for local development
$db   = "bibliothek"; //database name, must match database in mysql
$user = "root"; //username for mysql
$pass = ""; //password for mysql, empty for default xampp setup

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8", //create new PDO instance for database connection, utf8 charset for proper character encoding
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //set error mode to exception for better error handling
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen"); //if database connection fails, show error msg
}
