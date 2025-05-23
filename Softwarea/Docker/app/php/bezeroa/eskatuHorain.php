<?php
session_start();

if (!isset($_SESSION['emaila'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

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

    $id_bezeroa = $bezeroa['id_bezeroa'];

    // 3. Lortu formularioaren datuak
    $jatorria = $_POST['jatorria'] ?? null;
    $helmuga = $_POST['helmuga'] ?? null;
    $pertsona_kopurua = $_POST['pertsona_kopurua'] ?? null;
    $erreserba_id = $_POST['erreserba_id'] ?? null;

    // Data eta ordua bakarrik eskatzen dira erreserbarik ez badago
    if (empty($erreserba_id)) {
        $data = $_POST['data'] ?? null;
        $ordua = $_POST['ordua'] ?? null;

        if (!$jatorria || !$helmuga || !$pertsona_kopurua || !$data || !$ordua) {
            die("Eremu guztiak bete behar dira (erreserbarik gabe).");
        }

        $stmt = $pdo->prepare("INSERT INTO bidaia (jatorria, helmuga, data, ordua, egoera, pertsona_kopurua, bezeroa_id_bezeroa)
                               VALUES (?, ?, ?, ?, 'pendiente', ?, ?)");
        $stmt->execute([$jatorria, $helmuga, $data, $ordua, $pertsona_kopurua, $id_bezeroa]);
    } else {
        // Lortu data eta ordua erreserbatik
        $stmt = $pdo->prepare("SELECT data_esleipena, ordua_esleipena FROM erreserba WHERE id_erreserba = ? AND bezeroa_id_bezeroa = ?");
        $stmt->execute([$erreserba_id, $id_bezeroa]);
        $erreserba = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$erreserba) {
            die("Erreserba ez da aurkitu.");
        }

        $data = $erreserba['data_esleipena'];
        $ordua = $erreserba['ordua_esleipena'];

        if (!$jatorria || !$helmuga || !$pertsona_kopurua) {
            die("Eremu guztiak bete behar dira.");
        }

        $stmt = $pdo->prepare("INSERT INTO bidaia (jatorria, helmuga, data, ordua, egoera, pertsona_kopurua, bezeroa_id_bezeroa, erreserba_id_erreserba)
                               VALUES (?, ?, ?, ?, 'pendiente', ?, ?, ?)");
        $stmt->execute([$jatorria, $helmuga, $data, $ordua, $pertsona_kopurua, $id_bezeroa, $erreserba_id]);

        // Eguneratu erreserbaren egoera
        $update = $pdo->prepare("UPDATE erreserba SET egoera_erreserba = 'erabilita' WHERE id_erreserba = ? AND bezeroa_id_bezeroa = ?");
        $update->execute([$erreserba_id, $id_bezeroa]);
    }

    // 6. Bideratu berriro index.php-era
    header("Location: ../../index.php?eskatu_success=1");
    exit;
} catch (PDOException $e) {
    die("Errorea: " . $e->getMessage());
}
