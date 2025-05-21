<?php
session_start();

if (!isset($_SESSION['emaila'])) {
    die("Erabiltzaile autentifikatua izan behar duzu erreserba bat egiteko.");
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

    // 2. Lortu bezeroaren IDa
    $stmt = $pdo->prepare("SELECT id_bezeroa FROM bezeroa WHERE emaila = ?");
    $stmt->execute([$emaila]);
    $bezeroa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bezeroa) {
        die("Bezeroa ez da aurkitu.");
    }

    $id_bezeroa = $bezeroa['id_bezeroa'];

    // 3. Lortu datuak formularioan
    $data = $_POST['data'] ?? null;
    $ordua = $_POST['ordua'] ?? null;

    if (!$data || !$ordua) {
        die("Data eta ordua beharrezkoak dira.");
    }

    // 4. Sartu erreserba taulan
    $stmt = $pdo->prepare("INSERT INTO erreserba (data_esleipena, ordua_esleipena, egoera_erreserba, bezeroa_id_bezeroa)
                           VALUES (?, ?, 'aktibo', ?)");
    $stmt->execute([$data, $ordua, $id_bezeroa]);

    // 5. Bideratu berriro index.php-era
    header("Location: ../../index.php?success=1");
    exit;
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}
