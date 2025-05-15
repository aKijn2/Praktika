<?php
session_start();

if (!isset($_SESSION['emaila'])) {
    header("Location: login.php");
    exit;
}

$host = "db";
$db = "alaiktomugi";
$user = "root";
$pass = "mysql";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Lortu erabiltzailearen emaila
    $emaila = $_SESSION['emaila'];

    // 2. Lortu bezeroaren IDa (eta egiaztatu existitzen dela)
    $stmt = $pdo->prepare("SELECT id_bezeroa FROM bezeroa WHERE emaila = ?");
    $stmt->execute([$emaila]);
    $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bezeroa) {
        die("Bezeroa ez da aurkitu.");
    }
    $id_bezeroa = $bezeroa['id_bezeroa'];  // Guardamos el id para usarlo en el insert

    // 3. Lortu formularioaren datuak
    $jatorria = $_POST['jatorria'] ?? null;
    $helmuga = $_POST['helmuga'] ?? null;
    $pertsona_kopurua = $_POST['pertsona_kopurua'] ?? null;
    $data = $_POST['data'] ?? null;
    $ordua = $_POST['ordua'] ?? null;

    if (!$jatorria || !$helmuga || !$pertsona_kopurua || !$data || !$ordua) {
        die("Eremu guztiak bete behar dira.");
    }

    // 4. Sartu bidaia berria, ahora añadiendo bezeroa_id_bezeroa para vincular el viaje con el usuario
    $stmt = $pdo->prepare("INSERT INTO bidaia (jatorria, helmuga, data, ordua, egoera, pertsona_kopurua, bezeroa_id_bezeroa)
                           VALUES (?, ?, ?, ?, 'pendiente', ?, ?)");
    $stmt->execute([$jatorria, $helmuga, $data, $ordua, $pertsona_kopurua, $id_bezeroa]);

    // 5. Bideratu berriro index.php-era
    header("Location: index.php?eskatu_success=1");
    exit;
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}
