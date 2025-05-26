<?php
$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";

// $host = "sql7.freesqldatabase.com";
// $db = "sql7780328";
// $user = "sql7780328";
// $pass = "MzWdllcr3Y";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Errorea konexioan: " . $e->getMessage());
}

// https://www.freesqldatabase.com/
// replit.com php config.
